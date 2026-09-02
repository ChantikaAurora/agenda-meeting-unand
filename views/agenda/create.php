<?php

/** @var yii\web\View $this */
/** @var app\models\Agenda $model */

use yii\helpers\Html;

$this->title = 'Buat Agenda Baru';
?>
<div class="breadcrumb">
    <a href="<?= Yii::$app->homeUrl ?>">Dashboard</a> &nbsp;›&nbsp;
    <?= Html::a('Kelola Agenda', ['/agenda/index']) ?> &nbsp;›&nbsp;
    <span class="current">Buat Baru</span>
</div>

<div class="dash-banner">
    <div>
        <h1><?= Html::encode($this->title) ?></h1>
        <p>Isi detail agenda rapat yang akan dijadwalkan.</p>
    </div>
</div>

<?= $this->render('_form', ['model' => $model]) ?>
