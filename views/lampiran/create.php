<?php

/** @var yii\web\View $this */
/** @var app\models\Lampiran $model */
/** @var app\models\Agenda $agenda */

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

$this->title = 'Tambah Dokumentasi Rapat';
?>

<div class="breadcrumb">
    <?= Html::a('Dashboard', ['/dashboard/index']) ?>
    &nbsp;›&nbsp;
    <?= Html::a('Kelola Agenda', ['/agenda/index']) ?>
    &nbsp;›&nbsp;
    <?= Html::a('Detail', ['/agenda/view', 'id' => $agenda->agenda_id]) ?>
    &nbsp;›&nbsp;
    <span class="current">Tambah Dokumentasi</span>
</div>

<div class="agenda-card lampiran-form-card">
    <div class="agenda-section-heading">
        <div>
            <h1>Tambah Dokumentasi Rapat</h1>
            <p><?= Html::encode($agenda->pembahasan) ?></p>
        </div>
    </div>

    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
    ]); ?>

    <?= $form->field($model, 'uploadFile')->fileInput([
        'accept' => '.jpg,.jpeg,.jfif,.png,.gif,.webp,image/jpeg,image/png,image/gif,image/webp',
    ])->hint('Hanya gambar JPG, JPEG, JFIF, PNG, GIF, atau WEBP. PDF, Word, dan Excel tidak diperbolehkan. Maksimal 5 MB.') ?>

    <div class="lampiran-form-actions">
        <?= Html::a('Batal', ['/agenda/view', 'id' => $agenda->agenda_id], ['class' => 'btn-lampiran-cancel']) ?>
        <?= Html::submitButton('Upload Foto', ['class' => 'btn-lampiran-submit']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<style>
    .lampiran-form-card {
        max-width: 620px;
        margin: 0 auto;
    }

    .lampiran-form-card h1 {
        margin: 0 0 5px;
        color: #111827;
        font-size: 20px;
    }

    .lampiran-form-card p {
        margin: 0;
        color: #6b7280;
        font-size: 12px;
    }

    .lampiran-form-card .form-group {
        margin-top: 20px;
    }

    .lampiran-form-card input[type="file"] {
        width: 100%;
        padding: 10px;
        border: 1px solid #dfe3e8;
        border-radius: 7px;
        box-sizing: border-box;
        font-size: 12px;
    }

    .lampiran-form-card .hint-block {
        margin-top: 6px;
        color: #6b7280;
        font-size: 11px;
    }

    .lampiran-form-card .help-block {
        margin-top: 6px;
        color: #c0392b;
        font-size: 11px;
    }

    .lampiran-form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        margin-top: 24px;
    }

    .btn-lampiran-cancel,
    .btn-lampiran-submit {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 34px;
        padding: 0 15px;
        border-radius: 7px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
    }

    .btn-lampiran-cancel {
        border: 1px solid #dfe3e8;
        color: #374151;
    }

    .btn-lampiran-submit {
        border: 1px solid #247b59;
        background: #247b59;
        color: #ffffff;
        cursor: pointer;
    }
</style>
