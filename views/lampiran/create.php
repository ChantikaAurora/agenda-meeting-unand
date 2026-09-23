<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Agenda $agenda */
/** @var app\models\Lampiran $model */

$isNotulen = ($model->jenis_lampiran === 'notulen' || Yii::$app->controller->layout === 'notulis');

if ($isNotulen) {
    $this->title = 'Upload Notulen';
} else {
    $this->title = 'Tambah Dokumentasi Rapat';
}
?>

<?php if ($isNotulen): ?>
    <div class="nt-breadcrumb">
        <a href="<?= Html::encode(Url::to(['/notulis/dashboard'])) ?>">Dashboard</a>
        &nbsp;›&nbsp;
        <a href="<?= Html::encode(Url::to(['/notulis/index'])) ?>">Daftar Agenda</a>
        &nbsp;›&nbsp; <span>Upload Notulen</span>
    </div>

    <h1 class="nt-page-title">Upload Notulen</h1>
    <p class="nt-page-description">Unggah dokumen notulen untuk agenda rapat yang dipilih.</p>

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
            <div class="form-group mb-3">
                <label class="form-label" for="lampiran-file">File Notulen</label>
                <input id="lampiran-file" class="form-control" type="file" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx" required>
                <div style="margin-top: 5px; color: #737981; font-size: .75rem;">Format PDF, DOC, DOCX, XLS, XLSX, PPT, atau PPTX. Maksimal 10 MB.</div>
                <?php if ($model->hasErrors('file_path')): ?>
                    <div class="text-danger mt-1" style="font-size: .8rem;"><?= Html::encode($model->getFirstError('file_path')) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group mb-3">
                <label class="form-label" for="lampiran-status">Status Notulen</label>
                <?= Html::dropDownList('status', $model->status, [
                    'draft' => 'Draft',
                    'final' => 'Selesai Diunggah',
                ], ['id' => 'lampiran-status', 'class' => 'form-select']) ?>
            </div>

            <div class="form-group mb-3">
                <label class="form-label" for="lampiran-ringkasan">Ringkasan (opsional)</label>
                <?= Html::textarea('ringkasan', $model->ringkasan, ['id' => 'lampiran-ringkasan', 'class' => 'form-control', 'rows' => 5, 'placeholder' => 'Tambahkan ringkasan singkat notulen...']) ?>
            </div>

            <div class="form-actions d-flex gap-2 mt-4">
                <?= Html::submitButton('Upload Notulen', ['class' => 'btn btn-success', 'style' => 'cursor:pointer;']) ?>
                <?= Html::a('Batal', ['/notulis/index'], ['class' => 'btn btn-secondary']) ?>
            </div>
        <?php ActiveForm::end(); ?>
    </div>

<?php else: ?>

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
        ])->hint('Hanya gambar JPG, JPEG, JFIF, PNG, GIF, atau WEBP. Maksimal 5 MB.') ?>

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
<?php endif; ?>