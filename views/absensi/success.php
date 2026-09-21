<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\Agenda $agenda */
/** @var app\models\Absensi $absensi */

use yii\helpers\Html;

$this->title = 'Absensi Berhasil';
?>

<div class="public-card status-card status-success">

    <div class="status-icon-wrap status-icon-success">
        <svg viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05"/>
        </svg>
    </div>

    <h1 class="status-title">Absensi Berhasil Dicatat</h1>

    <p class="status-message">
        Terima kasih, <strong><?= Html::encode($absensi->nama) ?></strong>.
        Kehadiran Anda telah tercatat.
    </p>

    <div class="status-details">

        <div class="status-detail-row">
            <span class="status-detail-label">Agenda</span>
            <span class="status-detail-value"><?= Html::encode($agenda->pembahasan) ?></span>
        </div>

        <div class="status-detail-row">
            <span class="status-detail-label">Waktu Absen</span>
            <span class="status-detail-value">
                <?= Html::encode(Yii::$app->formatter->asDatetime($absensi->waktu_scan, 'php:d F Y, H:i')) ?> WIB
            </span>
        </div>

        <?php if (!empty($absensi->jabatan)): ?>
            <div class="status-detail-row">
                <span class="status-detail-label">Jabatan</span>
                <span class="status-detail-value"><?= Html::encode($absensi->jabatan) ?></span>
            </div>
        <?php endif; ?>

    </div>

    <p class="status-footnote">Anda dapat menutup halaman ini.</p>

</div>
