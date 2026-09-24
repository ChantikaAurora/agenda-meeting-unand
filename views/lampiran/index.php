<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Agenda $agenda */
/** @var app\models\Lampiran $model */
/** @var string $fileUrl */
/** @var bool $fileExists */

$this->title = 'Lihat Notulen';
$extension = strtolower(pathinfo($model->file_path, PATHINFO_EXTENSION));
$canManage = !Yii::$app->user->isGuest && Yii::$app->user->identity->can('manageLampiran');
?>
<div class="nt-breadcrumb">
    <a href="<?= Html::encode(Url::to(['/notulis/dashboard'])) ?>">Dashboard</a>
    &nbsp;›&nbsp;
    <a href="<?= Html::encode(Url::to(['/notulis/index'])) ?>">Daftar Agenda</a>
    &nbsp;›&nbsp; <span>Lihat Notulen</span>
</div>

<h1 class="nt-page-title">Lihat Notulen</h1>
<p class="nt-page-description">Periksa berkas notulen dan kelola perubahan dokumennya.</p>

<div class="nt-card" style="max-width: 900px; padding: 20px;">
    <div style="padding: 14px 16px; margin-bottom: 20px; border: 1px solid #e1e5e9; border-radius: 7px; background: #f8f9fa;">
        <div style="margin-bottom: 5px; color: #737981; font-size: .72rem; text-transform: uppercase;">Agenda Rapat</div>
        <strong style="font-size: 1rem;"><?= Html::encode($agenda->pembahasan) ?></strong>
        <div style="margin-top: 5px; color: #737981; font-size: .8rem;">
            <?= Yii::$app->formatter->asDate($agenda->tanggal, 'php:d M Y') ?>
            &nbsp;|&nbsp; <?= substr($agenda->waktu_mulai, 0, 5) ?> - <?= substr($agenda->waktu_selesai, 0, 5) ?> WIB
        </div>
    </div>

    <div style="margin-bottom: 18px;">
        <div class="form-label">Berkas Notulen</div>
        <div style="padding: 12px; border: 1px solid #e1e5e9; border-radius: 6px;">
            <strong><?= Html::encode($model->original_name ?: basename($model->file_path)) ?></strong>
            <div style="margin-top: 4px; color: #737981; font-size: .78rem;">
                Format <?= Html::encode(strtoupper($extension)) ?> &nbsp;|&nbsp; Status <?= Html::encode($model->status) ?>
            </div>
            <?php if (!$fileExists): ?>
                <div class="form-error" style="margin-top: 8px;">File fisik tidak ditemukan di server.</div>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($model->ringkasan)): ?>
        <div style="margin-bottom: 18px;">
            <div class="form-label">Ringkasan</div>
            <div style="white-space: pre-line; color: #4e5358; font-size: .85rem;"><?= Html::encode($model->ringkasan) ?></div>
        </div>
    <?php endif; ?>

    <div class="form-actions">
        <?php if ($fileExists): ?>
            <?= Html::a('Lihat Isi Berkas', ['/lampiran/preview', 'agenda_id' => $agenda->agenda_id, 'notulen' => 1], ['class' => 'nt-action-button primary']) ?>
            <?= Html::a('Download Berkas', ['/lampiran/download', 'agenda_id' => $agenda->agenda_id], ['class' => 'nt-action-button']) ?>
        <?php endif; ?>
        <?php if ($canManage): ?>
            <?= Html::a('Edit Notulen', ['/lampiran/update', 'agenda_id' => $agenda->agenda_id, 'notulen' => 1], ['class' => 'nt-action-button']) ?>
            <?= Html::beginForm(['/lampiran/delete', 'agenda_id' => $agenda->agenda_id], 'post', ['style' => 'display:inline;']) ?>
                <?= Html::submitButton('Hapus Berkas', [
                    'class' => 'nt-action-button',
                    'style' => 'border-color:#c0392b;color:#c0392b;cursor:pointer;',
                    'data' => ['confirm' => 'Yakin ingin menghapus berkas notulen ini?'],
                ]) ?>
            <?= Html::endForm() ?>
        <?php endif; ?>
    </div>
</div>
