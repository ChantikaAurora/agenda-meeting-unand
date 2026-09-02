<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Member $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="member-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'nama')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'jabatan')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'instansi')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tipe_identitas')->dropDownList([
        'KTP' => 'KTP',
        'NIP' => 'NIP',
        'NIM' => 'NIM',
        'Lainnya' => 'Lainnya',
    ], ['prompt' => '-- Pilih Tipe Identitas --']) ?>

    <?= $form->field($model, 'identitas_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput([
        'maxlength' => true,
    ]) ?>

    <?= $form->field($model, 'is_active')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton('Simpan', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>