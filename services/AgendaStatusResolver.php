<?php

declare(strict_types=1);

namespace app\services;

use DateTimeImmutable;
use DateTimeZone;
use Yii;
use app\models\Agenda;

/**
 * Menentukan status agenda yang SEHARUSNYA berlaku berdasarkan jadwalnya.
 *
 * Kelas ini sengaja dibuat murni logika (tidak menyentuh database, tidak
 * menyentuh request) supaya:
 *  - jadi satu-satunya sumber kebenaran aturan status (single source of truth),
 *  - gampang diuji unit test tanpa perlu DB,
 *  - bisa dipakai bersama oleh tampilan (on the fly) dan oleh cron (persist).
 */
final class AgendaStatusResolver
{
    /** Status yang sepenuhnya dikelola sistem berdasarkan waktu. */
    public const STATUS_OTOMATIS = [
        Agenda::STATUS_TERJADWAL,
        Agenda::STATUS_BERLANGSUNG,
        Agenda::STATUS_SELESAI,
    ];

    /** Status yang hanya boleh diubah manual oleh pengguna berwenang. */
    public const STATUS_MANUAL = [
        Agenda::STATUS_DIBATALKAN,
    ];

    private const POLA_TANGGAL = '/^\d{4}-\d{2}-\d{2}$/';
    private const POLA_WAKTU   = '/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/';

    /**
     * Apakah status ini boleh ditimpa otomatis oleh sistem?
     * 'dibatalkan' selalu dihormati: keputusan manusia tidak boleh dianulir mesin.
     */
    public static function dikelolaOtomatis(?string $status): bool
    {
        return in_array($status, self::STATUS_OTOMATIS, true);
    }

    /**
     * Hitung status yang seharusnya berlaku pada waktu $sekarang.
     *
     * Aturan:
     *   sekarang <  waktu_mulai    -> terjadwal
     *   waktu_mulai <= sekarang <= waktu_selesai -> berlangsung
     *   sekarang >  waktu_selesai  -> selesai
     *
     * @param string|null $statusTersimpan status yang saat ini ada di database
     * @return string status hasil perhitungan (selalu salah satu nilai valid)
     */
    public static function resolve(
        ?string $tanggal,
        ?string $waktuMulai,
        ?string $waktuSelesai,
        ?string $statusTersimpan,
        ?DateTimeImmutable $sekarang = null,
    ): string {
        // Status manual (dibatalkan) tidak pernah diubah oleh perhitungan waktu.
        if (!self::dikelolaOtomatis($statusTersimpan)) {
            return (string) $statusTersimpan;
        }

        $zona    = self::zonaWaktu();
        $mulai   = self::gabungkan($tanggal, $waktuMulai, $zona);
        $selesai = self::gabungkan($tanggal, $waktuSelesai, $zona);

        // Data jadwal tidak lengkap/rusak: jangan menebak, pertahankan apa adanya.
        if ($mulai === null || $selesai === null) {
            return (string) $statusTersimpan;
        }

        $sekarang ??= self::sekarang();

        if ($sekarang < $mulai) {
            return Agenda::STATUS_TERJADWAL;
        }

        if ($sekarang <= $selesai) {
            return Agenda::STATUS_BERLANGSUNG;
        }

        return Agenda::STATUS_SELESAI;
    }

    public static function sekarang(): DateTimeImmutable
    {
        return new DateTimeImmutable('now', self::zonaWaktu());
    }

    /**
     * Zona waktu aplikasi (Asia/Jakarta), BUKAN zona waktu server database.
     * Semua perbandingan waktu memakai ini supaya hasil di web dan di cron sama.
     */
    public static function zonaWaktu(): DateTimeZone
    {
        return new DateTimeZone(Yii::$app->timeZone);
    }

    /**
     * Gabungkan kolom `tanggal` (Y-m-d) dan kolom `time` (H:i atau H:i:s)
     * menjadi satu titik waktu. Format divalidasi ketat lebih dulu agar nilai
     * kotor dari database tidak diam-diam diinterpretasikan jadi tanggal lain.
     */
    public static function gabungkan(
        ?string $tanggal,
        ?string $waktu,
        ?DateTimeZone $zona = null,
    ): ?DateTimeImmutable {
        if ($tanggal === null || $waktu === null) {
            return null;
        }

        if (preg_match(self::POLA_TANGGAL, $tanggal) !== 1) {
            return null;
        }

        if (preg_match(self::POLA_WAKTU, $waktu) !== 1) {
            return null;
        }

        $jamMenit = substr($waktu, 0, 5);
        $hasil = DateTimeImmutable::createFromFormat(
            'Y-m-d H:i',
            $tanggal . ' ' . $jamMenit,
            $zona ?? self::zonaWaktu(),
        );

        if ($hasil === false) {
            return null;
        }

        // createFromFormat mewarisi detik dari waktu saat ini -> nolkan.
        return $hasil->setTime(
            (int) substr($jamMenit, 0, 2),
            (int) substr($jamMenit, 3, 2),
            0,
        );
    }
}
