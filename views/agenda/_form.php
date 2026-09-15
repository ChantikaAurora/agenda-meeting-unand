<?php

/** @var yii\web\View $this */
/** @var app\models\Agenda $model */
/** @var yii\widgets\ActiveForm $form */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Agenda;
use app\models\Lokasi;
?>

<div class="card">
    <?php $form = ActiveForm::begin([
        'fieldConfig' => [
            'options' => ['class' => 'form-group'],
            'labelOptions' => ['class' => 'form-label'],
            'errorOptions' => ['class' => 'form-error'],
        ],
    ]); ?>

    <div class="form-row-2">
        <?= $form->field($model, 'nomor_surat')->textInput([
            'maxlength' => true,
            'class' => 'form-control',
            'placeholder' => '001/UN16/RPT/2026',
        ]) ?>

        <?= $form->field($model, 'pembahasan')->textInput([
            'maxlength' => true,
            'class' => 'form-control',
        ]) ?>
    </div>

    <?= $form->field($model, 'deskripsi')->textarea([
        'rows' => 4,
        'class' => 'form-control',
        'maxlength' => 500,
        'id' => 'agenda-deskripsi-input',
        'oninput' => "document.getElementById('deskripsi-counter').textContent = this.value.length + ' / 500 karakter';",
    ]) ?>
    <div id="deskripsi-counter" style="text-align:right;font-size:0.75rem;color:#999;margin-top:-10px;margin-bottom:14px;">
        <?= mb_strlen($model->deskripsi ?? '') ?> / 500 karakter
    </div>

    <div class="form-row-3">
        <?= $form->field($model, 'tanggal')->input('date', ['class' => 'form-control']) ?>
        <?= $form->field($model, 'waktu_mulai')->input('time', ['class' => 'form-control']) ?>
        <?= $form->field($model, 'waktu_selesai')->input('time', ['class' => 'form-control']) ?>
    </div>

    <div class="form-row-2">
        <?= $form->field($model, 'tahun_akademik')->textInput([
            'maxlength' => true,
            'class' => 'form-control',
            'placeholder' => '2026/2027',
        ]) ?>

        <?= $form->field($model, 'lokasi_id')->dropDownList(
            // ArrayHelper::map otomatis meng-encode teks lewat Html::dropDownList di baliknya,
            // jadi aman dari XSS meskipun nama lokasi/unit berasal dari input user sebelumnya.
            ArrayHelper::map(
                Lokasi::find()->andWhere(['is_active' => true, 'deleted_at' => null])->all(),
                'lokasi_id',
                static fn($m) => $m->lokasi . ' — ' . ($m->unit->nama_unit ?? '-')
            ),
            ['prompt' => '-- Pilih Lokasi --', 'class' => 'form-control']
        ) ?>
    </div>

    <?= $form->field($model, 'status')->dropDownList(Agenda::statusList(), ['class' => 'form-control']) ?>

    <div class="form-actions">
        <?= Html::submitButton($model->isNewRecord ? 'Simpan' : 'Perbarui', ['class' => 'btn-primary-sm']) ?>
        <?= Html::a('Batal', ['index'], ['class' => 'btn-secondary-sm']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
