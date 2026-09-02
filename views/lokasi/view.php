<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Lokasi $model */

$this->title = $model->lokasi_id;
$this->params['breadcrumbs'][] = ['label' => 'Lokasis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="lokasi-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'lokasi_id' => $model->lokasi_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'lokasi_id' => $model->lokasi_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'lokasi_id',
            'unit_id',
            'kategori_lokasi',
            'lokasi',
            'created_by',
            'created_at',
            'updated_by',
            'updated_at',
            'deleted_at',
            'is_active',
        ],
    ]) ?>

</div>
