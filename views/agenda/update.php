<?php

/** @var yii\web\View $this */
/** @var app\models\Agenda $model */

use yii\helpers\Html;

$this->title = 'Ubah Agenda: ' . $model->pembahasan;
?>
<div class="breadcrumb">
    <a href="<?= Yii::$app->homeUrl ?>">Dashboard</a> &nbsp;›&nbsp;
    <?= Html::a('Kelola Agenda', ['/agenda/index']) ?> &nbsp;›&nbsp;
    <span class="current">Ubah</span>
</div>

<div class="dash-banner">
    <div>
        <h1>Ubah Agenda</h1>
        <p><?= Html::encode($model->pembahasan) ?></p>
    </div>
</div>

<?= $this->render('_form', ['model' => $model]) ?>
