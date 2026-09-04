<?php

use app\models\Member;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Kelola Member';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="member-index">

    <!-- ==========================================
         BREADCRUMB
         ========================================== -->

    <div class="member-breadcrumb">
        <a href="<?= Yii::$app->homeUrl ?>">Dashboard</a>
        <span>›</span>
        <strong>Kelola Member</strong>
    </div>


    <!-- ==========================================
         HEADER
         ========================================== -->

    <div class="member-header">

        <div class="member-header-content">

            <h1><?= Html::encode($this->title) ?></h1>

            <p>
                Kelola data member dan pantau kehadiran rapat.
            </p>

        </div>

        <div class="member-header-action">

            <?= Html::a(
                '+ Tambah Member',
                ['create'],
                [
                    'class' => 'btn-tambah-member'
                ]
            ) ?>

        </div>

    </div>


    <!-- ==========================================
         DATA MEMBER
         ========================================== -->

    <div class="member-card">

        <?= GridView::widget([

            'dataProvider' => $dataProvider,

            'filterModel' => null,

            'tableOptions' => [
                'class' => 'member-table'
            ],

            'layout' =>
                "{items}\n" .
                "<div class=\"member-table-footer\">{summary}{pager}</div>",

            'summary' =>
                'Showing {begin}-{end} of {totalCount} items.',

            'pager' => [

                'options' => [
                    'class' => 'pagination member-pagination'
                ],

                'linkOptions' => [
                    'class' => 'page-link'
                ],

                'activePageCssClass' => 'active',

                'prevPageLabel' => '‹',

                'nextPageLabel' => '›',

                'firstPageLabel' => '‹',

                'lastPageLabel' => '›',

                'hideOnSinglePage' => true,

            ],

            'columns' => [

                // ==================================
                // NO
                // ==================================

                [
                    'class' => 'yii\grid\SerialColumn',

                    'header' => 'NO',

                    'headerOptions' => [
                        'class' => 'column-no'
                    ],

                    'contentOptions' => [
                        'class' => 'column-no'
                    ],
                ],


                // ==================================
                // NAMA
                // ==================================

                [
                    'attribute' => 'nama',

                    'label' => 'NAMA',

                    'contentOptions' => [
                        'class' => 'column-nama'
                    ],
                ],


                // ==================================
                // JABATAN
                // ==================================

                [
                    'attribute' => 'jabatan',

                    'label' => 'JABATAN',
                ],


                // ==================================
                // INSTANSI
                // ==================================

                [
                    'attribute' => 'instansi',

                    'label' => 'INSTANSI',
                ],


                // ==================================
                // TIPE IDENTITAS
                // ==================================

                [
                    'attribute' => 'tipe_identitas',

                    'label' => 'TIPE IDENTITAS',
                ],


                // ==================================
                // AKSI
                // ==================================

                [
                    'class' => ActionColumn::className(),

                    'header' => 'AKSI',

                    'template' => '{view} {update} {delete}',

                    'headerOptions' => [
                        'class' => 'column-aksi'
                    ],

                    'contentOptions' => [
                        'class' => 'member-actions'
                    ],

                    'urlCreator' => function (
                        $action,
                        Member $model,
                        $key,
                        $index,
                        $column
                    ) {

                        return Url::toRoute([
                            $action,
                            'member_id' => $model->member_id
                        ]);

                    },

                ],

            ],

        ]); ?>

    </div>

</div>


<?php

$this->registerCss(<<<CSS

/* =====================================================
   MEMBER PAGE
   ===================================================== */

.member-index {
    padding: 0;
    margin: 0;
    color: #111827;
}


/* =====================================================
   BREADCRUMB
   ===================================================== */

.member-breadcrumb {
    margin-bottom: 14px;

    font-size: 12px;

    line-height: 1.5;

    color: #6b7280;
}

.member-breadcrumb a {
    color: #6b7280;

    text-decoration: none;
}

.member-breadcrumb a:hover {
    color: #185c37;

    text-decoration: underline;
}

.member-breadcrumb span {
    margin: 0 5px;

    color: #9ca3af;
}

.member-breadcrumb strong {
    color: #185c37;

    font-weight: 600;
}


/* =====================================================
   HEADER
   ===================================================== */

.member-header {
    background: #ffffff;

    border: 1px solid #eeeeee;

    border-radius: 12px;

    padding: 26px 22px;

    margin-bottom: 18px;

    min-height: 96px;

    display: flex;

    align-items: center;

    justify-content: space-between;

    box-sizing: border-box;
}


/* =====================================================
   HEADER CONTENT
   ===================================================== */

.member-header-content {
    margin: 0;
    padding: 0;
}

.member-header-content h1 {
    margin: 0 0 6px 0;

    font-size: 26px;

    line-height: 1.25;

    font-weight: 700;

    color: #111827;
}

.member-header-content p {
    margin: 0;

    font-size: 13px;

    line-height: 1.5;

    color: #6b7280;
}


/* =====================================================
   BUTTON TAMBAH MEMBER
   ===================================================== */

.member-header-action {
    margin-left: 20px;
}

.btn-tambah-member {
    display: inline-flex;

    align-items: center;

    justify-content: center;

    height: 38px;

    min-width: 136px;

    padding: 0 17px;

    border-radius: 7px;

    background: #185c37;

    border: 1px solid #185c37;

    color: #ffffff !important;

    font-size: 12px;

    font-weight: 600;

    text-decoration: none !important;

    transition: all .2s ease;
}

.btn-tambah-member:hover {
    background: #12482b;

    border-color: #12482b;

    color: #ffffff !important;
}


/* =====================================================
   CARD TABEL
   ===================================================== */

.member-card {
    background: #ffffff;

    border: 1px solid #dfe3e8;

    border-radius: 12px;

    /*
     * Jarak tabel dari pinggir card
     * agar mirip Kelola Agenda
     */
    padding: 14px 18px 0 18px;

    box-sizing: border-box;

    overflow: hidden;
}


/* =====================================================
   TABLE
   ===================================================== */

.member-table {
    width: 100% !important;

    margin: 0 !important;

    border-collapse: separate;

    border-spacing: 0;

    background: #ffffff;

    border-radius: 9px 9px 0 0;

    overflow: hidden;
}


/* =====================================================
   TABLE HEADER
   ===================================================== */

.member-table thead th {
    background: #f7f8f9;

    border-top: none !important;

    border-bottom: 1px solid #e5e7eb !important;

    padding: 11px 10px !important;

    height: 34px;

    vertical-align: middle;

    font-size: 9px;

    line-height: 1.3;

    font-weight: 700;

    color: #6b7280;

    text-transform: uppercase;

    letter-spacing: .2px;
}


/* Header pertama */

.member-table thead th:first-child {
    border-radius: 9px 0 0 0;
}


/* Header terakhir */

.member-table thead th:last-child {
    border-radius: 0 9px 0 0;
}


/* Link pada header */

.member-table thead th a {
    color: #6b7280;

    text-decoration: none;
}


/* =====================================================
   TABLE BODY
   ===================================================== */

.member-table tbody td {
    background: #ffffff;

    border-top: none !important;

    border-bottom: 1px solid #eeeeee !important;

    padding: 14px 10px !important;

    height: 48px;

    vertical-align: middle;

    font-size: 12px;

    line-height: 1.4;

    color: #111827;
}


/* Baris terakhir */

.member-table tbody tr:last-child td {
    border-bottom: none !important;
}


/* =====================================================
   HOVER
   ===================================================== */

.member-table tbody tr:hover td {
    background: #fafcfb;
}


/* =====================================================
   KOLOM NO
   ===================================================== */

.member-table .column-no {
    width: 45px;

    text-align: left;

    color: #374151;
}


/* =====================================================
   KOLOM NAMA
   ===================================================== */

.member-table .column-nama {
    color: #111827;

    font-weight: 500;
}


/* =====================================================
   KOLOM AKSI
   ===================================================== */

.member-table .column-aksi {
    width: 100px;

    text-align: center;
}

.member-actions {
    text-align: center !important;

    white-space: nowrap;

    padding-left: 10px !important;

    padding-right: 10px !important;
}


/* =====================================================
   ICON AKSI
   ===================================================== */

.member-actions a {
    display: inline-block;

    margin: 0 4px;

    font-size: 14px;

    text-decoration: none !important;

    color: #8b8f94;
}

.member-actions a:hover {
    color: #185c37;
}


/* =====================================================
   FOOTER TABEL
   ===================================================== */

.member-table-footer {
    min-height: 52px;

    padding: 0 2px;

    border-top: 1px solid #eeeeee;

    display: flex;

    align-items: center;

    justify-content: space-between;

    box-sizing: border-box;

    font-size: 11px;

    color: #6b7280;
}


/* =====================================================
   SUMMARY
   ===================================================== */

.member-table-footer .summary {
    margin: 0;

    font-size: 11px;

    color: #6b7280;
}


/* =====================================================
   PAGINATION
   ===================================================== */

.member-table-footer .pagination {
    display: flex;

    align-items: center;

    gap: 4px;

    margin: 0;

    padding: 0;
}

.member-table-footer .pagination > li {
    margin: 0;
}

.member-table-footer .pagination > li > a,
.member-table-footer .pagination > li > span {
    min-width: 30px;

    height: 30px;

    padding: 5px 9px;

    display: inline-flex;

    align-items: center;

    justify-content: center;

    border: 1px solid #e5e7eb;

    border-radius: 6px;

    background: #ffffff;

    color: #6b7280;

    font-size: 12px;

    text-decoration: none;
}


/* Pagination aktif */

.member-table-footer .pagination > li.active > a,
.member-table-footer .pagination > li.active > span {
    background: #185c37;

    border-color: #185c37;

    color: #ffffff;
}


/* Pagination hover */

.member-table-footer .pagination > li > a:hover {
    background: #f3f6f4;

    color: #185c37;

    border-color: #d5ddd8;
}


/* =====================================================
   RESPONSIVE
   ===================================================== */

@media (max-width: 900px) {

    .member-header {
        padding: 22px 18px;
    }

    .member-header-content h1 {
        font-size: 23px;
    }

    .member-card {
        overflow-x: auto;
    }

    .member-table {
        min-width: 750px;
    }

}


@media (max-width: 600px) {

    .member-header {
        flex-direction: column;

        align-items: flex-start;

        gap: 16px;
    }

    .member-header-action {
        width: 100%;

        margin-left: 0;
    }

    .btn-tambah-member {
        width: 100%;
    }

    .member-table-footer {
        flex-direction: column;

        align-items: flex-start;

        gap: 10px;

        padding: 12px 15px;
    }

}

CSS
);

?>