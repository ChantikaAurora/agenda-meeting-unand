<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Agenda $agenda */
/** @var app\models\Lampiran $model */

$this->title = 'Edit Notulen';
?>
<div class="nt-breadcrumb">
    <a href="<?= Html::encode(Url::to(['/notulis/dashboard'])) ?>">Dashboard</a>
    &nbsp;›&nbsp;
    <a href="<?= Html::encode(Url::to(['/notulis/index'])) ?>">Kelola Agenda</a>
    &nbsp;›&nbsp; <span>Edit Notulen</span>
</div>

<h1 class="nt-page-title">Edit Notulen</h1>
<p class="nt-page-description">Perbarui dokumen dan informasi notulen untuk agenda rapat ini.</p>

<div class="nt-card" style="max-width: 760px; padding: 20px;">
    <div style="padding: 14px 16px; margin-bottom: 20px; border: 1px solid #e1e5e9; border-radius: 7px; background: #f8f9fa;">
        <div style="margin-bottom: 5px; color: #737981; font-size: .72rem; text-transform: uppercase;">Agenda Rapat</div>
        <strong style="font-size: 1rem;"><?= Html::encode($agenda->pembahasan) ?></strong>
        <div style="margin-top: 5px; color: #737981; font-size: .8rem;">
            <?= Yii::$app->formatter->asDate($agenda->tanggal, 'php:d M Y') ?>
            &nbsp;|&nbsp; <?= substr($agenda->waktu_mulai, 0, 5) ?> - <?= substr($agenda->waktu_selesai, 0, 5) ?> WIB
        </div>
    </div>

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
        <div class="form-group">
            <label class="form-label" for="lampiran-file">Ganti File Notulen (opsional)</label>
            <input id="lampiran-file" class="form-control" type="file" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
            <div style="margin-top: 5px; color: #737981; font-size: .75rem;">Kosongkan jika file tidak ingin diganti. Maksimal 10 MB.</div>
            <?php if ($model->hasErrors('file_path')): ?>
                <div class="form-error"><?= Html::encode($model->getFirstError('file_path')) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label class="form-label" for="lampiran-status">Status Notulen</label>
            <?= Html::dropDownList('status', $model->status, [
                'draft' => 'Draft',
                'final' => 'Selesai Diunggah',
            ], ['id' => 'lampiran-status', 'class' => 'form-control']) ?>
        </div>

        <div class="form-group">
            <label class="form-label" for="lampiran-ringkasan">Ringkasan (opsional)</label>
            <?= Html::textarea('ringkasan', $model->ringkasan, ['id' => 'lampiran-ringkasan', 'class' => 'form-control', 'rows' => 5, 'placeholder' => 'Tambahkan ringkasan singkat notulen...']) ?>
        </div>

        <div class="form-actions">
            <?= Html::submitButton('Simpan Perubahan', ['class' => 'nt-action-button primary', 'style' => 'cursor:pointer;']) ?>
            <?= Html::a('Batal', ['/notulis/index'], ['class' => 'nt-action-button muted']) ?>
        </div>
    <?php ActiveForm::end(); ?>
</div>
