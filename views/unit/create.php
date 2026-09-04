<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Unit $model */

$this->title = 'Buat Unit Baru';

?>
<div class="breadcrumb">
    <a href="<?= Yii::$app->homeUrl ?>">Dashboard</a> &nbsp;›&nbsp;
    <?= Html::a('Kelola Unit & Lokasi', ['/lokasi/index']) ?> &nbsp;›&nbsp;
    <span class="current">Buat Unit Baru</span>
</div>

<div class="unit-create">

    <!-- ==========================================
         HEADER
         ========================================== -->

    <div class="unit-create-header">

        <h1>
            <?= Html::encode($this->title) ?>
        </h1>

        <p>
            Isi data unit/fakultas baru untuk digunakan dalam agenda rapat.
        </p>

    </div>


    <!-- ==========================================
         FORM
         ========================================== -->

    <div class="unit-create-card">

        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>

    </div>

</div>


<?php

$this->registerCss(<<<CSS

/* ==========================================
   UNIT CREATE
   ========================================== */

.unit-create {
    padding: 0;
}


/* ==========================================
   HEADER
   ========================================== */

.unit-create-header {
    background: #ffffff;
    border-radius: 12px;
    padding: 22px 22px;
    margin-bottom: 18px;
    border: 1px solid #eeeeee;
}

.unit-create-header h1 {
    margin: 0 0 5px 0;
    font-size: 26px;
    font-weight: 700;
    color: #111827;
}

.unit-create-header p {
    margin: 0;
    font-size: 14px;
    color: #6b7280;
}


/* ==========================================
   FORM CARD
   ========================================== */

.unit-create-card {
    background: #ffffff;
    border: 1px solid #dfe3e8;
    border-radius: 12px;
    padding: 24px 18px;
    margin-bottom: 20px;
}


/* ==========================================
   LABEL FORM
   ========================================== */

.unit-create-card label {
    font-size: 13px;
    font-weight: 500;
    color: #111827;
    margin-bottom: 7px;
}


/* ==========================================
   INPUT
   ========================================== */

.unit-create-card .form-control,
.unit-create-card select,
.unit-create-card textarea {
    border: 1px solid #d6d9dd;
    border-radius: 7px;
    min-height: 40px;
    font-size: 13px;
    color: #111827;
    box-shadow: none;
}

.unit-create-card .form-control:focus,
.unit-create-card select:focus,
.unit-create-card textarea:focus {
    border-color: #185c37;
    box-shadow: 0 0 0 2px rgba(24, 92, 55, 0.08);
}


/* ==========================================
   BUTTON
   ========================================== */

.unit-create-card .btn-success {
    background-color: #185c37;
    border-color: #185c37;
    border-radius: 7px;
    padding: 9px 18px;
    font-size: 13px;
    font-weight: 600;
}

.unit-create-card .btn-success:hover {
    background-color: #12482b;
    border-color: #12482b;
}


/* ==========================================
   ERROR
   ========================================== */

.unit-create-card .help-block {
    font-size: 12px;
    color: #dc3545;
    margin-top: 5px;
}


/* ==========================================
   CHECKBOX
   ========================================== */

.unit-create-card .checkbox {
    margin-top: 15px;
    margin-bottom: 15px;
}

.unit-create-card .checkbox label {
    font-size: 13px;
    font-weight: 400;
}


/* ==========================================
   RESPONSIVE
   ========================================== */

@media (max-width: 768px) {

    .unit-create-header {
        padding: 20px 16px;
    }

    .unit-create-header h1 {
        font-size: 23px;
    }

    .unit-create-card {
        padding: 20px 15px;
    }

}

CSS
);

?>