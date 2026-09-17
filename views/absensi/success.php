<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\Agenda $agenda */
/** @var app\models\Absensi $absensi */

use yii\helpers\Html;

$this->title = 'Absensi Berhasil';
?>
<div class="public-card text-center">
    <div class="mb-3" style="font-size: 3rem; line-height: 1;">&#9989;</div>
    <h4 class="card-title mb-2">Absensi Berhasil Dicatat</h4>
    <p class="text-secondary mb-1">
        Terima kasih, <strong><?= Html::encode($absensi->nama) ?></strong>.
    </p>
    <p class="text-secondary">
        Kehadiran Anda pada agenda <strong><?= Html::encode($agenda->pembahasan) ?></strong>
        telah tercatat pada
        <?= Html::encode(Yii::$app->formatter->asDatetime($absensi->waktu_scan, 'php:d M Y H:i')) ?>.
    </p>
    <p class="text-secondary small mt-3">Anda dapat menutup halaman ini.</p>
</div>
