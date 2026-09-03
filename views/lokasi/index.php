<?php

use app\models\Lokasi;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Kelola Unit & Lokasi';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lokasi-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Lokasi', ['create'], ['class' => 'btn btn-success']) ?>
    </p>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'lokasi_id',
            'unit_id',
            'kategori_lokasi',
            'lokasi',
            'created_by',
            //'created_at',
            //'updated_by',
            //'updated_at',
            //'deleted_at',
            //'is_active',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Lokasi $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'lokasi_id' => $model->lokasi_id]);
                 }
            ],
        ],
    ]); ?>


</div>
