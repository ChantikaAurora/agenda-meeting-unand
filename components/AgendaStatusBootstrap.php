<?php

declare(strict_types=1);

namespace app\components;

use app\services\AgendaStatusSynchronizer;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\console\Application as ConsoleApplication;

/**
 * Jaring pengaman kalau cron belum/ tidak bisa dipasang (mis. saat demo di
 * laptop atau shared hosting tanpa cron): sinkronisasi dijalankan menumpang
 * request web, tetapi dibatasi maksimal sekali per menit lewat cache.
 *
 * Ini SENGAJA bukan endpoint HTTP. Endpoint publik untuk memicu perubahan
 * data massal akan jadi permukaan serangan baru (harus dijaga token, rawan
 * dipakai untuk membanjiri database). Menumpang request yang sudah ada tidak
 * menambah permukaan serangan sama sekali.
 *
 * Aktifkan/nonaktifkan lewat params: 'agendaStatusAutoSync' => true.
 */
final class AgendaStatusBootstrap implements BootstrapInterface
{
    public function bootstrap($app): void
    {
        if ($app instanceof ConsoleApplication) {
            return;
        }

        if (($app->params['agendaStatusAutoSync'] ?? false) !== true) {
            return;
        }

        // Dijalankan setelah request selesai diproses supaya tidak menambah
        // waktu tunggu pengguna saat halaman dimuat.
        $app->on(Application::EVENT_AFTER_REQUEST, static function (): void {
            AgendaStatusSynchronizer::buat()->sinkronkanTerbatas();
        });
    }
}
