<?php

declare(strict_types=1);

namespace app\services;

use DateTimeImmutable;
use Throwable;
use Yii;
use app\models\Agenda;
use yii\db\Connection;

/**
 * Menyimpan (persist) status agenda hasil perhitungan AgendaStatusResolver
 * ke database, lengkap dengan jejak audit ke tabel activity_log.
 *
 * Tampilan sudah selalu benar tanpa kelas ini (status dihitung on the fly),
 * tetapi nilai di database tetap perlu disinkronkan supaya laporan, filter
 * status, dan ekspor tidak membaca data basi.
 */
final class AgendaStatusSynchronizer
{
    /** Jeda minimum antar-sinkronisasi otomatis dari web, dalam detik. */
    public const JEDA_THROTTLE_DETIK = 60;

    private const KUNCI_CACHE = 'agenda.status.sync.terakhir';

    public function __construct(
        private readonly Connection $db,
        private readonly bool $simulasiSaja = false,
    ) {
    }

    public static function buat(bool $simulasiSaja = false): self
    {
        return new self(Yii::$app->db, $simulasiSaja);
    }

    /**
     * Sinkronkan seluruh agenda yang statusnya sudah tidak sesuai jadwal.
     *
     * @return array<string,int> jumlah baris per status tujuan, mis. ['berlangsung' => 2]
     */
    public function sinkronkan(?DateTimeImmutable $sekarang = null): array
    {
        $sekarang ??= AgendaStatusResolver::sekarang();
        $perubahan = $this->kumpulkanPerubahan($sekarang);

        if ($perubahan === [] || $this->simulasiSaja) {
            return $this->ringkas($perubahan);
        }

        $transaksi = $this->db->beginTransaction();
        try {
            foreach ($perubahan as $statusBaru => $baris) {
                $this->terapkan($statusBaru, $baris, $sekarang);
            }
            $transaksi->commit();
        } catch (Throwable $e) {
            $transaksi->rollBack();
            Yii::error('Sinkronisasi status agenda gagal: ' . $e->getMessage(), __METHOD__);
            throw $e;
        }

        return $this->ringkas($perubahan);
    }

    /**
     * Versi aman untuk dipanggil dari request web: dibatasi maksimal satu kali
     * per JEDA_THROTTLE_DETIK dan tidak pernah melempar exception ke pengguna.
     * Kegagalan sinkronisasi tidak boleh membuat halaman ikut error.
     */
    public function sinkronkanTerbatas(): void
    {
        $cache = Yii::$app->cache;

        // cache->add() bersifat "tulis hanya jika belum ada" sehingga berfungsi
        // sebagai kunci sederhana: request paralel tidak akan jalan bersamaan.
        if (!$cache->add(self::KUNCI_CACHE, time(), self::JEDA_THROTTLE_DETIK)) {
            return;
        }

        try {
            $this->sinkronkan();
        } catch (Throwable $e) {
            Yii::error('Sinkronisasi status otomatis dilewati: ' . $e->getMessage(), __METHOD__);
        }
    }

    /**
     * Ambil kandidat agenda dan kelompokkan berdasarkan status tujuannya.
     *
     * @return array<string,list<array{agenda_id:int,status:string}>>
     */
    private function kumpulkanPerubahan(DateTimeImmutable $sekarang): array
    {
        $kandidat = Agenda::find()
            ->select(['agenda_id', 'status', 'tanggal', 'waktu_mulai', 'waktu_selesai'])
            ->andWhere(['deleted_at' => null])
            ->andWhere(['status' => AgendaStatusResolver::STATUS_OTOMATIS])
            // Agenda yang sudah 'selesai' dan waktunya memang sudah lewat tidak
            // akan pernah berubah lagi -> jangan ikut ditarik setiap kali cron jalan.
            ->andWhere(['not', [
                'and',
                ['status' => Agenda::STATUS_SELESAI],
                ['<', 'tanggal', $sekarang->format('Y-m-d')],
            ]])
            ->asArray()
            ->all();

        $perubahan = [];

        foreach ($kandidat as $baris) {
            $statusBaru = AgendaStatusResolver::resolve(
                $baris['tanggal'],
                $baris['waktu_mulai'],
                $baris['waktu_selesai'],
                $baris['status'],
                $sekarang,
            );

            if ($statusBaru === $baris['status']) {
                continue;
            }

            $perubahan[$statusBaru][] = [
                'agenda_id' => (int) $baris['agenda_id'],
                'status'    => (string) $baris['status'],
            ];
        }

        return $perubahan;
    }

    /**
     * @param list<array{agenda_id:int,status:string}> $baris
     */
    private function terapkan(string $statusBaru, array $baris, DateTimeImmutable $sekarang): void
    {
        $id = array_column($baris, 'agenda_id');
        $statusLama = array_values(array_unique(array_column($baris, 'status')));

        // Guard `status IN (statusLama)` mencegah race condition: kalau di sela
        // SELECT dan UPDATE ada admin yang membatalkan agenda, barisnya terlewat
        // dan pembatalan manual tetap menang.
        $terupdate = $this->db->createCommand()->update(
            Agenda::tableName(),
            [
                'status'     => $statusBaru,
                'updated_at' => $sekarang->format('Y-m-d H:i:s'),
            ],
            [
                'and',
                ['agenda_id' => $id],
                ['status' => $statusLama],
                ['deleted_at' => null],
            ],
        )->execute();

        if ($terupdate === 0) {
            return;
        }

        $this->catatAudit($baris, $statusBaru, $sekarang);
    }

    /**
     * Tulis jejak audit. performed_by dibiarkan NULL karena pelakunya sistem,
     * bukan manusia -- jangan mengatasnamakan user yang kebetulan sedang login.
     *
     * @param list<array{agenda_id:int,status:string}> $baris
     */
    private function catatAudit(array $baris, string $statusBaru, DateTimeImmutable $sekarang): void
    {
        $nilai = [];

        foreach ($baris as $item) {
            $nilai[] = [
                'agenda',
                $item['agenda_id'],
                'auto-status',
                null,
                'system',
                $item['status'],
                $statusBaru,
                null,
                null,
                'Status diubah otomatis sesuai jadwal.',
                $sekarang->format('Y-m-d H:i:s'),
            ];
        }

        $this->db->createCommand()->batchInsert(
            '{{%activity_log}}',
            [
                'table_name', 'record_id', 'action', 'performed_by', 'performed_role',
                'old_value', 'new_value', 'ip_address', 'user_agent', 'description', 'created_at',
            ],
            $nilai,
        )->execute();
    }

    /**
     * @param array<string,list<array{agenda_id:int,status:string}>> $perubahan
     * @return array<string,int>
     */
    private function ringkas(array $perubahan): array
    {
        return array_map(static fn(array $baris): int => count($baris), $perubahan);
    }
}
