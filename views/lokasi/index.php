<?php

use app\models\Unit;
use app\models\Lokasi;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $unitDataProvider */
/** @var yii\data\ActiveDataProvider $lokasiDataProvider */

$this->title = 'Kelola Unit & Lokasi';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="lokasi-index">

    <!-- ==========================================
         BREADCRUMB
         ========================================== -->

    <div class="lokasi-breadcrumb">
        <a href="<?= Yii::$app->homeUrl ?>">Dashboard</a>
        <span>›</span>
        <strong>Kelola Unit &amp; Lokasi</strong>
    </div>


    <!-- ==========================================
         HEADER
         ========================================== -->

    <div class="lokasi-header">

        <div class="lokasi-header-content">

            <h1><?= Html::encode($this->title) ?></h1>

            <p>
                Kelola data unit/fakultas dan ruang rapat universitas.
            </p>

        </div>

        <div class="lokasi-header-action">

            <?= Html::a('+ Tambah Unit', ['/unit/create'], [
                'class' => 'btn-tambah-lokasi lokasi-action-btn',
                'data-pane' => 'unit-pane',
            ]) ?>

            <?= Html::a('+ Tambah Lokasi', ['/lokasi/create'], [
                'class' => 'btn-tambah-lokasi lokasi-action-btn',
                'data-pane' => 'lokasi-pane',
                'style' => 'display:none;',
            ]) ?>

        </div>

    </div>


    <!-- ==========================================
         TABS
         ========================================== -->

    <div class="lokasi-tabs" id="unitLokasiTab" role="tablist">
        <button type="button" class="lokasi-tab active" data-target="unit-pane">
            Daftar Unit &amp; Fakultas
        </button>
        <button type="button" class="lokasi-tab" data-target="lokasi-pane">
            Daftar Ruang Rapat &amp; Lokasi
        </button>
    </div>

    <div class="lokasi-tab-content">

        <!-- ==========================================
             TAB: UNIT & FAKULTAS
             ========================================== -->

        <div class="lokasi-pane" id="unit-pane">

            <div class="lokasi-card">

                <?= GridView::widget([

                    'dataProvider' => $unitDataProvider,

                    'tableOptions' => [
                        'class' => 'lokasi-table'
                    ],

                    'layout' =>
                        "{items}\n" .
                        "<div class=\"lokasi-table-footer\">{summary}{pager}</div>",

                    'summary' =>
                        'Showing {begin}-{end} of {totalCount} items.',

                    'columns' => [

                        [
                            'class' => 'yii\grid\SerialColumn',
                            'header' => 'NO',
                        ],

                        [
                            'attribute' => 'nama_unit',
                            'label' => 'NAMA UNIT',
                        ],

                        [
                            'attribute' => 'kategori_unit',
                            'label' => 'KATEGORI UNIT',
                        ],

                        [
                            'attribute' => 'created_at',
                            'label' => 'DIBUAT PADA',
                        ],

                        [
                            'class' => ActionColumn::className(),
                            'header' => 'AKSI',
                            'headerOptions' => ['class' => 'lokasi-aksi-header'],
                            'contentOptions' => ['class' => 'lokasi-actions'],
                            'urlCreator' => function ($action, Unit $model, $key, $index, $column) {
                                return Url::toRoute(['/unit/' . $action, 'unit_id' => $model->unit_id]);
                            }
                        ],

                    ],

                ]); ?>

            </div>

        </div>


        <!-- ==========================================
             TAB: RUANG RAPAT & LOKASI
             ========================================== -->

        <div class="lokasi-pane" id="lokasi-pane" style="display:none;">

            <div class="lokasi-card">

                <?= GridView::widget([

                    'dataProvider' => $lokasiDataProvider,

                    'tableOptions' => [
                        'class' => 'lokasi-table'
                    ],

                    'layout' =>
                        "{items}\n" .
                        "<div class=\"lokasi-table-footer\">{summary}{pager}</div>",

                    'summary' =>
                        'Showing {begin}-{end} of {totalCount} items.',

                    'columns' => [

                        [
                            'class' => 'yii\grid\SerialColumn',
                            'header' => 'NO',
                        ],

                        [
                            'attribute' => 'kategori_lokasi',
                            'label' => 'KATEGORI LOKASI',
                        ],

                        [
                            'attribute' => 'lokasi',
                            'label' => 'LOKASI',
                        ],

                        [
                            'attribute' => 'unit_id',
                            'label' => 'UNIT',
                        ],

                        [
                            'class' => ActionColumn::className(),
                            'header' => 'AKSI',
                            'headerOptions' => ['class' => 'lokasi-aksi-header'],
                            'contentOptions' => ['class' => 'lokasi-actions'],
                            'urlCreator' => function ($action, Lokasi $model, $key, $index, $column) {
                                return Url::toRoute(['/lokasi/' . $action, 'lokasi_id' => $model->lokasi_id]);
                            }
                        ],

                    ],

                ]); ?>

            </div>

        </div>

    </div>

</div>


<?php

$this->registerCss(<<<CSS

/* =====================================================
   LOKASI PAGE
   ===================================================== */

.lokasi-index {
    padding: 0;
    margin: 0;
    color: #111827;
}


/* =====================================================
   BREADCRUMB
   ===================================================== */

.lokasi-breadcrumb {
    margin-bottom: 14px;
    font-size: 12px;
    line-height: 1.5;
    color: #6b7280;
}

.lokasi-breadcrumb a {
    color: #6b7280;
    text-decoration: none;
}

.lokasi-breadcrumb a:hover {
    color: #185c37;
    text-decoration: underline;
}

.lokasi-breadcrumb span {
    margin: 0 5px;
    color: #9ca3af;
}

.lokasi-breadcrumb strong {
    color: #185c37;
    font-weight: 600;
}


/* =====================================================
   HEADER
   ===================================================== */

.lokasi-header {
    background: #ffffff;
    border: 1px solid #eeeeee;
    border-radius: 12px;
    padding: 26px 22px;
    margin-bottom: 18px;
    box-sizing: border-box;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.lokasi-header-content h1 {
    margin: 0 0 6px 0;
    font-size: 26px;
    line-height: 1.25;
    font-weight: 700;
    color: #111827;
}

.lokasi-header-content p {
    margin: 0;
    font-size: 13px;
    line-height: 1.5;
    color: #6b7280;
}

.lokasi-header-action {
    margin-left: 20px;
    flex-shrink: 0;
}


/* =====================================================
   TABS
   ===================================================== */

.lokasi-tabs {
    display: flex;
    gap: 4px;
    margin-bottom: 16px;
    border-bottom: 1px solid #e5e7eb;
}

.lokasi-tab {
    appearance: none;
    background: none;
    border: none;
    border-bottom: 2px solid transparent;
    padding: 10px 16px;
    font-size: 13px;
    font-weight: 600;
    color: #6b7280;
    cursor: pointer;
}

.lokasi-tab:hover {
    color: #185c37;
}

.lokasi-tab.active {
    color: #185c37;
    border-bottom-color: #185c37;
}


/* =====================================================
   CARD
   ===================================================== */

.lokasi-card {
    background: #ffffff;
    border: 1px solid #dfe3e8;
    border-radius: 12px;
    padding: 14px 18px 0 18px;
    box-sizing: border-box;
    overflow: hidden;
}


/* =====================================================
   BUTTON TAMBAH
   ===================================================== */

.btn-tambah-lokasi {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 38px;
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

.btn-tambah-lokasi:hover {
    background: #12482b;
    border-color: #12482b;
    color: #ffffff !important;
}


/* =====================================================
   TABLE
   ===================================================== */

.lokasi-table {
    width: 100% !important;
    margin: 0 !important;
    border-collapse: separate;
    border-spacing: 0;
    background: #ffffff;
}

.lokasi-table thead th {
    background: #f7f8f9;
    border-top: none !important;
    border-bottom: 1px solid #e5e7eb !important;
    padding: 11px 10px !important;
    font-size: 9px;
    font-weight: 700;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: .2px;
}

.lokasi-table tbody td {
    background: #ffffff;
    border-top: none !important;
    border-bottom: 1px solid #eeeeee !important;
    padding: 14px 10px !important;
    font-size: 12px;
    color: #111827;
}

.lokasi-table tbody tr:last-child td {
    border-bottom: none !important;
}

.lokasi-table tbody tr:hover td {
    background: #fafcfb;
}

.lokasi-actions {
    text-align: center !important;
    white-space: nowrap;
    width: 100px;
}

.lokasi-aksi-header {
    text-align: center !important;
    width: 100px;
}

.lokasi-actions a {
    display: inline-block;
    margin: 0 4px;
    font-size: 14px;
    text-decoration: none !important;
    color: #8b8f94;
}

.lokasi-actions a:hover {
    color: #185c37;
}


/* =====================================================
   FOOTER TABEL
   ===================================================== */

.lokasi-table-footer {
    min-height: 52px;
    padding: 12px 2px;
    border-top: 1px solid #eeeeee;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 11px;
    color: #6b7280;
}


/* =====================================================
   RESPONSIVE
   ===================================================== */

@media (max-width: 600px) {

    .lokasi-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
    }

    .lokasi-header-action {
        width: 100%;
        margin-left: 0;
    }

    .btn-tambah-lokasi {
        width: 100%;
    }

}

CSS
);

$this->registerJs(<<<JS

(function () {
    var tabs = document.querySelectorAll('.lokasi-tab');
    var panes = document.querySelectorAll('.lokasi-pane');
    var actionBtns = document.querySelectorAll('.lokasi-action-btn');

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            var target = tab.getAttribute('data-target');

            // Toggle tab active state
            tabs.forEach(function (t) { t.classList.remove('active'); });
            tab.classList.add('active');

            // Toggle pane visibility
            panes.forEach(function (pane) {
                pane.style.display = (pane.id === target) ? '' : 'none';
            });

            // Toggle action button visibility
            actionBtns.forEach(function (btn) {
                btn.style.display = (btn.getAttribute('data-pane') === target) ? '' : 'none';
            });
        });
    });
})();

JS
);

?>