<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use app\models\Unit;

/** @var yii\web\View $this */
/** @var app\models\Unit $model */
/** @var yii\bootstrap5\ActiveForm $form */
?>

<div class="unit-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'nama_unit')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'kategori_unit')->dropDownList(Unit::optsKategoriUnit(), ['prompt' => '-- Pilih Kategori --']) ?>

    <?= $form->field($model, 'is_active')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton('Simpan', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>