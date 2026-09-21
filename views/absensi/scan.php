<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\Absensi $model */
/** @var app\models\Agenda $agenda */
/** @var string $token */

use app\models\Absensi;
use app\models\Agenda;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

$this->title = 'Pendaftaran Kehadiran - ' . $agenda->pembahasan;

$this->registerJsFile(
    'https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js',
    ['position' => View::POS_HEAD],
);

$badgeMap = [
    Agenda::STATUS_TERJADWAL => ['Upcoming Meeting', 'badge-upcoming'],
    Agenda::STATUS_BERLANGSUNG => ['Sedang Berlangsung', 'badge-ongoing'],
    Agenda::STATUS_SELESAI => ['Meeting Selesai', 'badge-done'],
];
[$badgeText, $badgeClass] = $badgeMap[$agenda->status] ?? ['Agenda', 'badge-upcoming'];

$icon = static function (string $path): string {
    return '<svg class="meta-icon" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">' . $path . '</svg>';
};

$iconCalendar = $icon('<path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/>');
$iconClock = $icon('<path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"/><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0"/>');
$iconPin = $icon('<path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6"/>');
?>

<h1 class="page-title">Pendaftaran Kehadiran</h1>
<p class="page-subtitle">Silakan lengkapi data diri Anda pada formulir di bawah ini untuk melakukan absensi.</p>

<div class="public-card meeting-card">
    <span class="meeting-badge <?= $badgeClass ?>"><?= Html::encode($badgeText) ?></span>
    <h2 class="meeting-title"><?= Html::encode($agenda->pembahasan) ?></h2>

    <hr class="meeting-divider">

    <div class="meeting-meta">
        <?= $iconCalendar ?>
        <div>
            <span class="meta-label">Date</span>
            <span class="meta-value"><?= Html::encode(Yii::$app->formatter->asDate($agenda->tanggal, 'php:l, d F Y')) ?></span>
        </div>
    </div>

    <div class="meeting-meta">
        <?= $iconClock ?>
        <div>
            <span class="meta-label">Time</span>
            <span class="meta-value">
                <?= Html::encode(substr((string) $agenda->waktu_mulai, 0, 5)) ?>
                -
                <?= Html::encode(substr((string) $agenda->waktu_selesai, 0, 5)) ?> WIB
            </span>
        </div>
    </div>

    <div class="meeting-meta">
        <?= $iconPin ?>
        <div>
            <span class="meta-label">Venue</span>
            <span class="meta-value"><?= Html::encode($agenda->lokasi->lokasi ?? '-') ?></span>
        </div>
    </div>
</div>

<div class="public-card form-card">
    <?php $form = ActiveForm::begin([
        'id' => 'absensi-form',
        'action' => ['/absensi/submit'],
        'fieldConfig' => [
            'options' => ['class' => 'form-group mb-3'],
            'labelOptions' => ['class' => 'form-label'],
            'errorOptions' => ['class' => 'invalid-feedback d-block'],
        ],
    ]) ?>

    <?= Html::hiddenInput('token', $token) ?>
    <?= Html::activeHiddenInput($model, 'signatureData', ['id' => 'signature-data-input']) ?>

    <!-- === Pencarian nama (autofill dari data yang pernah tercatat) === -->
    <div class="form-group mb-3" id="lookup-group"
         data-lookup-url="<?= Html::encode(Url::to(['/absensi/lookup', 'token' => $token])) ?>">
        <label class="form-label" for="lookup-input">Cari Nama Anda</label>
        <div class="input-with-icon">
            <svg class="input-icon" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1M12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
            </svg>
            <input type="text" id="lookup-input" class="form-control" autocomplete="off"
                   placeholder="Ketik nama untuk isi otomatis...">
            <div class="lookup-suggestions" id="lookup-suggestions" hidden></div>
        </div>
        <div class="lookup-hint" id="lookup-hint" hidden>
            <svg viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2"/>
            </svg>
            <span id="lookup-hint-text">Data ditemukan otomatis</span>
        </div>
    </div>

    <?= $form->field($model, 'tipe_identitas')->dropDownList(
        Absensi::tipeIdentitasList(),
        ['prompt' => 'Pilih Tipe Identitas', 'id' => 'absensi-tipe-identitas'],
    ) ?>

    <?= $form->field($model, 'identitas_number')->textInput([
        'maxlength' => true,
        'placeholder' => 'Sesuai kartu identitas',
        'id' => 'absensi-identitas-number',
    ]) ?>

    <?= $form->field($model, 'jabatan')->dropDownList(
        Absensi::jabatanList(),
        ['prompt' => 'Pilih Jabatan', 'id' => 'absensi-jabatan'],
    ) ?>

    <?= $form->field($model, 'jabatanLainnya', [
        'options' => ['class' => 'form-group mb-3', 'id' => 'jabatan-lainnya-group'],
    ])->textInput([
        'maxlength' => true,
        'placeholder' => 'Tuliskan jabatan Anda',
        'id' => 'absensi-jabatan-lainnya',
    ])->label('Jabatan Lainnya') ?>

    <?= $form->field($model, 'instansi')->textInput([
        'maxlength' => true,
        'placeholder' => 'Asal instansi',
        'id' => 'absensi-instansi',
    ])->label('Instansi <span class="label-optional">(Opsional)</span>') ?>

    <?= $form->field($model, 'nama')->textInput([
        'maxlength' => true,
        'placeholder' => 'Masukkan nama lengkap',
        'id' => 'absensi-nama',
    ]) ?>

    <?= $form->field($model, 'email')->textInput([
        'maxlength' => true,
        'type' => 'email',
        'placeholder' => 'Masukkan alamat email',
        'id' => 'absensi-email',
    ]) ?>

    <!-- === Tanda tangan digital === -->
    <div class="form-group mb-4 required">
        <div class="signature-header">
            <label class="form-label mb-0">Tanda Tangan Digital</label>
            <button type="button" id="signature-clear" class="signature-reset">
                <svg viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2z"/>
                    <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466"/>
                </svg>
                Reset
            </button>
        </div>
        <div class="signature-pad-wrapper" id="signature-wrapper">
            <canvas id="signature-pad"></canvas>
            <span class="signature-placeholder" id="signature-placeholder">Gambar tanda tangan di sini</span>
        </div>
        <div class="invalid-feedback d-block" id="signature-error">
            <?= Html::encode($model->getFirstError('signatureData') ?? '') ?>
        </div>
    </div>

    <div class="d-grid">
        <?= Html::submitButton(
            '<svg class="btn-icon" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">'
            . '<path d="M2 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3.5L12.5 1zM3 2h9.5L14 3.5V14h-1V9a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v5H2V2zm2 0h6v3H5zm0 8h6v4H5z"/>'
            . '</svg> Simpan Absensi',
            ['class' => 'btn btn-submit btn-lg'],
        ) ?>
    </div>

    <?php ActiveForm::end() ?>
</div>
