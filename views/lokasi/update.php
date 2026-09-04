<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Lokasi $model */

$this->title = 'Ubah Lokasi';

?>
<div class="breadcrumb">
    <a href="<?= Yii::$app->homeUrl ?>">Dashboard</a> &nbsp;›&nbsp;
    <?= Html::a('Kelola Unit & Lokasi', ['/lokasi/index']) ?> &nbsp;›&nbsp;
    <span class="current">Ubah</span>
</div>

<div class="lokasi-update">

    <!-- ==========================================
         HEADER
         ========================================== -->

    <div class="lokasi-update-header">

        <h1>
            <?= Html::encode($this->title) ?>
        </h1>

        <p>
            <?= Html::encode($model->lokasi) ?>
        </p>

    </div>


    <!-- ==========================================
         FORM
         ========================================== -->

    <div class="lokasi-update-card">

        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>

    </div>

</div>


<?php

$this->registerCss(<<<CSS

/* ==========================================
   LOKASI UPDATE
   ========================================== */

.lokasi-update {
    padding: 0;
}


/* ==========================================
   HEADER
   ========================================== */

.lokasi-update-header {
    background: #ffffff;
    border-radius: 12px;
    padding: 22px 22px;
    margin-bottom: 18px;
    border: 1px solid #eeeeee;
}

.lokasi-update-header h1 {
    margin: 0 0 5px 0;
    font-size: 26px;
    font-weight: 700;
    color: #111827;
}

.lokasi-update-header p {
    margin: 0;
    font-size: 14px;
    color: #6b7280;
}


/* ==========================================
   FORM CARD
   ========================================== */

.lokasi-update-card {
    background: #ffffff;
    border: 1px solid #dfe3e8;
    border-radius: 12px;
    padding: 24px 18px;
    margin-bottom: 20px;
}


/* ==========================================
   LABEL FORM
   ========================================== */

.lokasi-update-card label {
    font-size: 13px;
    font-weight: 500;
    color: #111827;
    margin-bottom: 7px;
}


/* ==========================================
   INPUT
   ========================================== */

.lokasi-update-card .form-control,
.lokasi-update-card select,
.lokasi-update-card textarea {
    border: 1px solid #d6d9dd;
    border-radius: 7px;
    min-height: 40px;
    font-size: 13px;
    color: #111827;
    box-shadow: none;
}

.lokasi-update-card .form-control:focus,
.lokasi-update-card select:focus,
.lokasi-update-card textarea:focus {
    border-color: #185c37;
    box-shadow: 0 0 0 2px rgba(24, 92, 55, 0.08);
}


/* ==========================================
   BUTTON
   ========================================== */

.lokasi-update-card .btn-success {
    background-color: #185c37;
    border-color: #185c37;
    border-radius: 7px;
    padding: 9px 18px;
    font-size: 13px;
    font-weight: 600;
}

.lokasi-update-card .btn-success:hover {
    background-color: #12482b;
    border-color: #12482b;
}


/* ==========================================
   ERROR
   ========================================== */

.lokasi-update-card .help-block {
    font-size: 12px;
    color: #dc3545;
    margin-top: 5px;
}


/* ==========================================
   CHECKBOX
   ========================================== */

.lokasi-update-card .checkbox {
    margin-top: 15px;
    margin-bottom: 15px;
}

.lokasi-update-card .checkbox label {
    font-size: 13px;
    font-weight: 400;
}


/* ==========================================
   RESPONSIVE
   ========================================== */

@media (max-width: 768px) {

    .lokasi-update-header {
        padding: 20px 16px;
    }

    .lokasi-update-header h1 {
        font-size: 23px;
    }

    .lokasi-update-card {
        padding: 20px 15px;
    }

}

CSS
);

?>