<?php

namespace app\models;

use DateTimeImmutable;
use Yii;
use app\services\AgendaStatusResolver;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "agenda".
 *
 * @property int $agenda_id
 * @property string|null $nomor_surat
 * @property string $pembahasan
 * @property string|null $deskripsi
 * @property string $tanggal
 * @property string $tahun_akademik
 * @property string $waktu_mulai
 * @property string $waktu_selesai
 * @property int $lokasi_id
 * @property string|null $qr_code_value
 * @property string|null $qr_code_path
 * @property string $status
 * @property int|null $created_by
 * @property string $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 * @property string|null $deleted_at
 *
 * @property-read string $statusSaatIni
 * @property-read string $labelStatusSaatIni
 *
 * @property Absensi[] $absensis
 * @property AgendaMember[] $agendaMembers
 * @property User $createdBy
 * @property Lampiran[] $lampirans
 * @property Lokasi $lokasi
 * @property Member[] $members
 * @property User $updatedBy
 */
class Agenda extends \yii\db\ActiveRecord
{
    public const STATUS_TERJADWAL = 'terjadwal';
    public const STATUS_BERLANGSUNG = 'berlangsung';
    public const STATUS_SELESAI = 'selesai';
    public const STATUS_DIBATALKAN = 'dibatalkan';

    public const SCENARIO_INPUT_PENGGUNA = 'input-pengguna';

    public static function statusList(): array
    {
        return [
            self::STATUS_TERJADWAL => 'Terjadwal',
            self::STATUS_BERLANGSUNG => 'Berlangsung',
            self::STATUS_SELESAI => 'Selesai',
            self::STATUS_DIBATALKAN => 'Dibatalkan',
        ];
    }

    public static function statusPilihanForm(): array
    {
        return [
            self::STATUS_TERJADWAL => 'Ikuti jadwal (otomatis)',
            self::STATUS_DIBATALKAN => 'Dibatalkan',
        ];
    }

    public static function tableName()
    {
        return 'agenda';
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],

            'blameable' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios[self::SCENARIO_INPUT_PENGGUNA] =
            $scenarios[self::SCENARIO_DEFAULT];

        return $scenarios;
    }

    public function rules()
    {
        return [
            [
                [
                    'nomor_surat',
                    'deskripsi',
                    'qr_code_value',
                    'qr_code_path',
                    'created_by',
                    'updated_by',
                    'updated_at',
                    'deleted_at',
                ],
                'default',
                'value' => null,
            ],

            [
                [
                    'pembahasan',
                    'tanggal',
                    'tahun_akademik',
                    'waktu_mulai',
                    'waktu_selesai',
                    'lokasi_id',
                    'status',
                ],
                'required',
            ],

            [
                ['deskripsi'],
                'string',
                'max' => 500,
            ],

            [
                ['tanggal'],
                'date',
                'format' => 'php:Y-m-d',
            ],

            [
                ['waktu_mulai', 'waktu_selesai'],
                'time',
                'format' => 'php:H:i',
            ],

            [
                ['created_at', 'updated_at', 'deleted_at'],
                'safe',
            ],

            [
                ['lokasi_id', 'created_by', 'updated_by'],
                'integer',
            ],

            [
                ['nomor_surat'],
                'string',
                'max' => 100,
            ],

            [
                [
                    'pembahasan',
                    'qr_code_value',
                    'qr_code_path',
                ],
                'string',
                'max' => 255,
            ],

            [
                ['tahun_akademik'],
                'string',
                'max' => 20,
            ],

            /*
             * Sistem (cron/sinkronisasi) boleh menulis
             * seluruh status.
             */
            [
                'status',
                'in',
                'range' => array_keys(
                    self::statusList()
                ),
            ],

            /*
             * Pengguna hanya boleh mengirim status
             * yang memang keputusan manusia.
             *
             * Ini mencegah orang memalsukan
             * 'selesai' lewat form yang dimodifikasi.
             */
            [
                'status',
                'in',
                'range' => array_keys(
                    self::statusPilihanForm()
                ),
                'on' => self::SCENARIO_INPUT_PENGGUNA,
                'message' =>
                    'Status tersebut tidak dapat dipilih manual; '
                    . 'status berlangsung/selesai ditentukan otomatis '
                    . 'oleh jadwal.',
            ],

            [
                ['created_by'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => [
                    'created_by' => 'user_id',
                ],
            ],

            [
                ['lokasi_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Lokasi::class,
                'targetAttribute' => [
                    'lokasi_id' => 'lokasi_id',
                ],
            ],

            [
                ['updated_by'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => [
                    'updated_by' => 'user_id',
                ],
            ],

            [
                'waktu_selesai',
                'validateWaktu',
            ],
        ];
    }

    /**
     * Validasi kustom:
     * waktu selesai harus lebih besar dari waktu mulai.
     */
    public function validateWaktu(
        $attribute,
        $params
    ) {
        if (
            !$this->hasErrors('waktu_mulai')
            && !$this->hasErrors('waktu_selesai')
        ) {
            if (
                strtotime($this->waktu_selesai)
                <= strtotime($this->waktu_mulai)
            ) {
                $this->addError(
                    $attribute,
                    'Waktu selesai harus lebih besar dari waktu mulai.'
                );
            }
        }
    }

    public function attributeLabels()
    {
        return [
            'agenda_id' => 'ID',
            'nomor_surat' => 'Nomor Surat',
            'pembahasan' => 'Pembahasan',
            'deskripsi' => 'Deskripsi',
            'tanggal' => 'Tanggal',
            'tahun_akademik' => 'Tahun Akademik',
            'waktu_mulai' => 'Waktu Mulai',
            'waktu_selesai' => 'Waktu Selesai',
            'lokasi_id' => 'Lokasi',
            'qr_code_value' => 'QR Code Value',
            'qr_code_path' => 'QR Code Path',
            'status' => 'Status',
            'created_by' => 'Dibuat Oleh',
            'created_at' => 'Dibuat Pada',
            'updated_by' => 'Diperbarui Oleh',
            'updated_at' => 'Diperbarui Pada',
            'deleted_at' => 'Dihapus Pada',
        ];
    }

    /* ================= Status berbasis jadwal ================= */

    public function getStatusSaatIni(
        ?DateTimeImmutable $sekarang = null
    ): string {
        return AgendaStatusResolver::resolve(
            $this->tanggal,
            $this->waktu_mulai,
            $this->waktu_selesai,
            $this->status,
            $sekarang,
        );
    }

    public function getLabelStatusSaatIni(): string
    {
        $status = $this->getStatusSaatIni();

        return self::statusList()[$status]
            ?? $status;
    }

    /**
     * Status agenda ini masih dikendalikan waktu
     * (belum dibatalkan manual)?
     */
    public function statusDikelolaOtomatis(): bool
    {
        return AgendaStatusResolver::dikelolaOtomatis(
            $this->status
        );
    }

    public function getJadwalMulai(): ?DateTimeImmutable
    {
        return AgendaStatusResolver::gabungkan(
            $this->tanggal,
            $this->waktu_mulai
        );
    }

    public function getJadwalSelesai(): ?DateTimeImmutable
    {
        return AgendaStatusResolver::gabungkan(
            $this->tanggal,
            $this->waktu_selesai
        );
    }

    /**
     * Berapa menit sebelum rapat dimulai
     * QR/presensi boleh dibuka.
     *
     * Default: 30 menit.
     */
    public static function menitAbsensiDibuka(): int
    {
        return max(
            0,
            (int) (
                Yii::$app->params[
                    'absensiDibukaMenitSebelum'
                ] ?? 30
            )
        );
    }

    /**
     * Apakah presensi rapat ini sedang dibuka?
     *
     * Aturan:
     *
     * - Absensi dibuka 30 menit sebelum rapat dimulai.
     * - Absensi ditutup 30 menit setelah rapat selesai.
     *
     * Contoh:
     *
     * Rapat 08:00 - 10:00
     * Absensi 07:30 - 10:30
     */
    public function absensiTerbuka(
        ?DateTimeImmutable $sekarang = null
    ): bool {
        /*
         * Agenda dibatalkan tidak boleh menerima absensi.
         */
        if (
            $this->status
            === self::STATUS_DIBATALKAN
        ) {
            return false;
        }

        $mulai =
            $this->getJadwalMulai();

        $selesai =
            $this->getJadwalSelesai();

        /*
         * Jika jadwal tidak lengkap,
         * absensi tidak boleh dibuka.
         */
        if (
            $mulai === null
            || $selesai === null
        ) {
            return false;
        }

        $sekarang ??=
            AgendaStatusResolver::sekarang();

        /*
         * ============================================================
         * WAKTU MULAI ABSENSI
         * ============================================================
         *
         * Default 30 menit sebelum rapat.
         */
        $dibuka = $mulai->modify(
            '-' . self::menitAbsensiDibuka()
            . ' minutes'
        );

        /*
         * ============================================================
         * WAKTU TUTUP ABSENSI
         * ============================================================
         *
         * 30 menit setelah rapat selesai.
         */
        $ditutup = $selesai->modify(
            '+30 minutes'
        );

        /*
         * Absensi hanya valid di dalam window:
         *
         * [30 menit sebelum mulai]
         * sampai
         * [30 menit setelah selesai]
         */
        return (
            $sekarang >= $dibuka
            && $sekarang <= $ditutup
        );
    }

    /* ================= Relasi ================= */

    public function getAbsensis()
    {
        return $this->hasMany(
            Absensi::class,
            ['agenda_id' => 'agenda_id']
        );
    }

    public function getAgendaMembers()
    {
        return $this->hasMany(
            AgendaMember::class,
            ['agenda_id' => 'agenda_id']
        );
    }

    public function getCreatedBy()
    {
        return $this->hasOne(
            User::class,
            ['user_id' => 'created_by']
        );
    }

    public function getLampirans()
    {
        return $this->hasMany(
            Lampiran::class,
            ['agenda_id' => 'agenda_id']
        );
    }

    public function getLokasi()
    {
        return $this->hasOne(
            Lokasi::class,
            ['lokasi_id' => 'lokasi_id']
        );
    }

    public function getMembers()
    {
        return $this->hasMany(
            Member::class,
            ['member_id' => 'member_id']
        )->viaTable(
            'agenda_member',
            ['agenda_id' => 'agenda_id']
        );
    }

    public function getUpdatedBy()
    {
        return $this->hasOne(
            User::class,
            ['user_id' => 'updated_by']
        );
    }

    public function generateQrToken(): string
    {
        return 'AGD-'
            . Yii::$app->security
                ->generateRandomString(32);
    }
}