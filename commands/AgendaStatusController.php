<?php

declare(strict_types=1);

namespace app\commands;

use Throwable;
use Yii;
use app\services\AgendaStatusResolver;
use app\services\AgendaStatusSynchronizer;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

/**
 * Sinkronisasi status agenda dengan jadwalnya.
 *
 * Dijalankan periodik lewat cron:
 *   * * * * * /usr/bin/php /path/ke/agenda_meeting/yii agenda-status/sync >> /var/log/agenda-status.log 2>&1
 */
class AgendaStatusController extends Controller
{
    /** Hanya tampilkan apa yang akan berubah, tanpa menulis ke database. */
    public $dryRun = false;

    public function options($actionID)
    {
        return array_merge(parent::options($actionID), ['dryRun']);
    }

    public function optionAliases()
    {
        return array_merge(parent::optionAliases(), ['d' => 'dryRun']);
    }

    /**
     * Ubah agenda menjadi 'berlangsung' saat waktunya tiba dan 'selesai'
     * setelah waktu selesai terlewat. Agenda 'dibatalkan' tidak disentuh.
     */
    public function actionSync(): int
    {
        $sekarang = AgendaStatusResolver::sekarang();

        try {
            $hasil = AgendaStatusSynchronizer::buat($this->dryRun)->sinkronkan($sekarang);
        } catch (Throwable $e) {
            $this->stderr('Gagal menyinkronkan status agenda: ' . $e->getMessage() . PHP_EOL, Console::FG_RED);

            return ExitCode::UNAVAILABLE;
        }

        $waktu = $sekarang->format('Y-m-d H:i:s T');

        if ($hasil === []) {
            $this->stdout("[{$waktu}] Tidak ada status yang perlu diubah." . PHP_EOL);

            return ExitCode::OK;
        }

        $awalan = $this->dryRun ? '[SIMULASI] ' : '';

        foreach ($hasil as $status => $jumlah) {
            $this->stdout("{$awalan}[{$waktu}] {$jumlah} agenda -> {$status}" . PHP_EOL, Console::FG_GREEN);
        }

        return ExitCode::OK;
    }
}
