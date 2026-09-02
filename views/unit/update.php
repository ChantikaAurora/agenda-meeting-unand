<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Unit $model */

$this->title = 'Update Unit: ' . $model->unit_id;
$this->params['breadcrumbs'][] = ['label' => 'Units', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->unit_id, 'url' => ['view', 'unit_id' => $model->unit_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="unit-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
