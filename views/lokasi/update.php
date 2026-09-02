<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Lokasi $model */

$this->title = 'Update Lokasi: ' . $model->lokasi_id;
$this->params['breadcrumbs'][] = ['label' => 'Lokasis', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->lokasi_id, 'url' => ['view', 'lokasi_id' => $model->lokasi_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="lokasi-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
