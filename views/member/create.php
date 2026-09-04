<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Member $model */

$this->title = 'Buat Member Baru';

?>
<div class="breadcrumb">
    <a href="<?= Yii::$app->homeUrl ?>">Dashboard</a> &nbsp;›&nbsp;
    <?= Html::a('Kelola Member', ['/member/index']) ?> &nbsp;›&nbsp;
    <span class="current">Buat Baru</span>
</div>

<div class="member-create">

    <!-- ==========================================
         HEADER
         ========================================== -->

    <div class="member-create-header">

        <h1>
            <?= Html::encode($this->title) ?>
        </h1>

        <p>
            Isi data member yang akan digunakan dalam agenda rapat.
        </p>

    </div>


    <!-- ==========================================
         FORM
         ========================================== -->

    <div class="member-create-card">

        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>

    </div>

</div>


<?php

$this->registerCss(<<<CSS

/* ==========================================
   MEMBER CREATE
   ========================================== */

.member-create {
    padding: 0;
}


/* ==========================================
   HEADER
   Sama seperti "Buat Agenda Baru"
   ========================================== */

.member-create-header {
    background: #ffffff;

    border-radius: 12px;

    padding: 22px 22px;

    margin-bottom: 18px;

    border: 1px solid #eeeeee;
}

.member-create-header h1 {
    margin: 0 0 5px 0;

    font-size: 26px;

    font-weight: 700;

    color: #111827;
}

.member-create-header p {
    margin: 0;

    font-size: 14px;

    color: #6b7280;
}


/* ==========================================
   FORM CARD
   ========================================== */

.member-create-card {
    background: #ffffff;

    border: 1px solid #dfe3e8;

    border-radius: 12px;

    padding: 24px 18px;

    margin-bottom: 20px;
}


/* ==========================================
   LABEL FORM
   ========================================== */

.member-create-card label {
    font-size: 13px;

    font-weight: 500;

    color: #111827;

    margin-bottom: 7px;
}


/* ==========================================
   INPUT
   ========================================== */

.member-create-card .form-control,
.member-create-card select,
.member-create-card textarea {

    border: 1px solid #d6d9dd;

    border-radius: 7px;

    min-height: 40px;

    font-size: 13px;

    color: #111827;

    box-shadow: none;
}

.member-create-card .form-control:focus,
.member-create-card select:focus,
.member-create-card textarea:focus {

    border-color: #185c37;

    box-shadow: 0 0 0 2px rgba(24, 92, 55, 0.08);
}


/* ==========================================
   BUTTON
   ========================================== */

.member-create-card .btn-success {

    background-color: #185c37;

    border-color: #185c37;

    border-radius: 7px;

    padding: 9px 18px;

    font-size: 13px;

    font-weight: 600;
}

.member-create-card .btn-success:hover {

    background-color: #12482b;

    border-color: #12482b;
}


/* ==========================================
   ERROR
   ========================================== */

.member-create-card .help-block {

    font-size: 12px;

    color: #dc3545;

    margin-top: 5px;
}


/* ==========================================
   CHECKBOX
   ========================================== */

.member-create-card .checkbox {

    margin-top: 15px;

    margin-bottom: 15px;
}

.member-create-card .checkbox label {

    font-size: 13px;

    font-weight: 400;
}


/* ==========================================
   RESPONSIVE
   ========================================== */

@media (max-width: 768px) {

    .member-create-header {

        padding: 20px 16px;

    }

    .member-create-header h1 {

        font-size: 23px;

    }

    .member-create-card {

        padding: 20px 15px;

    }

}

CSS
);

?>