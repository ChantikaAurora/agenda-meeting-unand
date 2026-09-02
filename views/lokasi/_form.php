<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Unit;

/** @var yii\web\View $this */
/** @var app\models\Lokasi $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="lokasi-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'unit_id')->dropDownList(
        ArrayHelper::map(Unit::find()->all(), 'unit_id', 'nama_unit'),
        ['prompt' => '-- Pilih Unit --']
    ) ?>

    <?= $form->field($model, 'kategori_lokasi')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'lokasi')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_active')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton('Simpan', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>