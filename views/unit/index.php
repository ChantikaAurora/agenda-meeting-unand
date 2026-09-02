<?php

use app\models\Unit;
use app\models\Lokasi;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var yii\data\ActiveDataProvider $lokasiDataProvider */

$this->title = 'Kelola Unit & Lokasi Rapat';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="unit-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>Manajemen data master unit/fakultas dan ruang rapat universitas.</p>

    <ul class="nav nav-tabs" id="unitLokasiTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="unit-tab" data-bs-toggle="tab" data-bs-target="#unit-pane" type="button" role="tab">
                Daftar Unit & Fakultas
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="lokasi-tab" data-bs-toggle="tab" data-bs-target="#lokasi-pane" type="button" role="tab">
                Daftar Ruang Rapat & Lokasi
            </button>
        </li>
    </ul>

    <div class="tab-content" style="margin-top: 15px;">

        <div class="tab-pane fade show active" id="unit-pane" role="tabpanel">
            <p>
                <?= Html::a('Tambah Unit', ['create'], ['class' => 'btn btn-success']) ?>
            </p>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'nama_unit',
                    'kategori_unit',
                    'created_at',
                    [
                        'class' => ActionColumn::className(),
                        'urlCreator' => function ($action, Unit $model, $key, $index, $column) {
                            return Url::toRoute([$action, 'unit_id' => $model->unit_id]);
                        }
                    ],
                ],
            ]); ?>
        </div>

        <div class="tab-pane fade" id="lokasi-pane" role="tabpanel">
            <p>
                <?= Html::a('Tambah Lokasi', ['/lokasi/create'], ['class' => 'btn btn-success']) ?>
            </p>
            <?= GridView::widget([
                'dataProvider' => $lokasiDataProvider,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'kategori_lokasi',
                    'lokasi',
                    'unit_id',
                    [
                        'class' => ActionColumn::className(),
                        'urlCreator' => function ($action, Lokasi $model, $key, $index, $column) {
                            return Url::toRoute(['/lokasi/' . $action, 'lokasi_id' => $model->lokasi_id]);
                        }
                    ],
                ],
            ]); ?>
        </div>

    </div>

</div>