<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Lokasi $model */

$this->title = $model->lokasi;

?>

<div class="breadcrumb">

    <?= Html::a('Dashboard', ['/site/index']) ?>

    <span>›</span>

    <?= Html::a('Kelola Unit & Lokasi', ['/lokasi/index']) ?>

    <span>›</span>

    <span class="current">Detail Lokasi</span>

</div>


<div class="lokasi-view">

    <!-- ==========================================
         HEADER
         ========================================== -->

    <div class="lokasi-view-header">

        <div class="lokasi-view-header-content">

            <h1>
                <?= Html::encode($this->title) ?>
            </h1>

            <p>
                Detail data lokasi rapat.
            </p>

        </div>


        <div class="lokasi-view-header-action">

            <?= Html::a(
                'Ubah',
                [
                    'update',
                    'lokasi_id' => $model->lokasi_id
                ],
                [
                    'class' => 'btn-lokasi-edit'
                ]
            ) ?>


            <?= Html::a(
                'Hapus',
                [
                    'delete',
                    'lokasi_id' => $model->lokasi_id
                ],
                [
                    'class' => 'btn-lokasi-delete',

                    'data' => [
                        'confirm' => 'Yakin ingin menghapus lokasi ini?',
                        'method' => 'post',
                    ],
                ]
            ) ?>

        </div>

    </div>


    <!-- ==========================================
         DETAIL CARD
         ========================================== -->

    <div class="lokasi-view-card">

        <div class="lokasi-detail-grid">


            <!-- ==================================
                 LOKASI ID
                 ================================== -->

            <div class="lokasi-detail-item">

                <div class="lokasi-detail-label">
                    Lokasi ID
                </div>

                <div class="lokasi-detail-value">
                    <?= Html::encode($model->lokasi_id) ?>
                </div>

            </div>


            <!-- ==================================
                 UNIT ID
                 ================================== -->

            <div class="lokasi-detail-item">

                <div class="lokasi-detail-label">
                    Unit ID
                </div>

                <div class="lokasi-detail-value">
                    <?= Html::encode($model->unit_id) ?>
                </div>

            </div>


            <!-- ==================================
                 KATEGORI LOKASI
                 ================================== -->

            <div class="lokasi-detail-item">

                <div class="lokasi-detail-label">
                    Kategori Lokasi
                </div>

                <div class="lokasi-detail-value">
                    <?= Html::encode($model->kategori_lokasi) ?>
                </div>

            </div>


            <!-- ==================================
                 NAMA LOKASI
                 ================================== -->

            <div class="lokasi-detail-item">

                <div class="lokasi-detail-label">
                    Nama Lokasi
                </div>

                <div class="lokasi-detail-value">
                    <?= Html::encode($model->lokasi) ?>
                </div>

            </div>


            <!-- ==================================
                 DIBUAT OLEH
                 ================================== -->

            <div class="lokasi-detail-item">

                <div class="lokasi-detail-label">
                    Dibuat Oleh
                </div>

                <div class="lokasi-detail-value">
                    <?= $model->created_by !== null
                        ? Html::encode($model->created_by)
                        : '-' ?>
                </div>

            </div>


            <!-- ==================================
                 DIBUAT PADA
                 ================================== -->

            <div class="lokasi-detail-item">

                <div class="lokasi-detail-label">
                    Dibuat Pada
                </div>

                <div class="lokasi-detail-value">
                    <?= $model->created_at !== null
                        ? Html::encode($model->created_at)
                        : '-' ?>
                </div>

            </div>


            <!-- ==================================
                 DIUBAH OLEH
                 ================================== -->

            <div class="lokasi-detail-item">

                <div class="lokasi-detail-label">
                    Diubah Oleh
                </div>

                <div class="lokasi-detail-value">
                    <?= $model->updated_by !== null
                        ? Html::encode($model->updated_by)
                        : '-' ?>
                </div>

            </div>


            <!-- ==================================
                 DIUBAH PADA
                 ================================== -->

            <div class="lokasi-detail-item">

                <div class="lokasi-detail-label">
                    Diubah Pada
                </div>

                <div class="lokasi-detail-value">
                    <?= $model->updated_at !== null
                        ? Html::encode($model->updated_at)
                        : '-' ?>
                </div>

            </div>


            <!-- ==================================
                 STATUS
                 ================================== -->

            <div class="lokasi-detail-item">

                <div class="lokasi-detail-label">
                    Status
                </div>

                <div class="lokasi-detail-value">

                    <?php if ($model->is_active): ?>

                        <span class="badge-status badge-selesai">
                            Aktif
                        </span>

                    <?php else: ?>

                        <span class="badge-status badge-dibatalkan">
                            Nonaktif
                        </span>

                    <?php endif; ?>

                </div>

            </div>


            <!-- ==================================
                 DIHAPUS PADA
                 ================================== -->

            <div class="lokasi-detail-item">

                <div class="lokasi-detail-label">
                    Dihapus Pada
                </div>

                <div class="lokasi-detail-value">
                    <?= $model->deleted_at !== null
                        ? Html::encode($model->deleted_at)
                        : '-' ?>
                </div>

            </div>


        </div>

    </div>

</div>


<?php

$this->registerCss(<<<CSS

/* ==========================================
   LOKASI VIEW
   ========================================== */

.lokasi-view {
    padding: 0;
}


/* ==========================================
   BREADCRUMB
   ========================================== */

.breadcrumb {

    display: flex;

    align-items: center;

    gap: 8px;

    margin-bottom: 12px;

    font-size: 11px;

    line-height: 18px;

    color: #718096;

}


.breadcrumb a {

    color: #718096;

    text-decoration: none;

}


.breadcrumb a:hover {

    color: #185c37;

    text-decoration: none;

}


.breadcrumb span {

    color: #9ca3af;

}


.breadcrumb .current {

    color: #185c37;

    font-weight: 600;

}


/* ==========================================
   HEADER
   ========================================== */

.lokasi-view-header {

    background: #ffffff;

    border: 1px solid #eeeeee;

    border-radius: 12px;

    padding: 22px 22px;

    margin-bottom: 18px;

    display: flex;

    align-items: center;

    justify-content: space-between;

    box-sizing: border-box;

}


.lokasi-view-header-content h1 {

    margin: 0 0 5px 0;

    font-size: 22px;

    font-weight: 700;

    color: #111827;

}


.lokasi-view-header-content p {

    margin: 0;

    font-size: 13px;

    color: #6b7280;

}


.lokasi-view-header-action {

    display: flex;

    gap: 10px;

    flex-shrink: 0;

    margin-left: 20px;

}


/* ==========================================
   DETAIL CARD
   ========================================== */

.lokasi-view-card {

    background: #ffffff;

    border: 1px solid #dfe3e8;

    border-radius: 12px;

    padding: 20px 22px;

    box-sizing: border-box;

}


/* ==========================================
   DETAIL GRID
   ========================================== */

.lokasi-detail-grid {

    display: grid;

    grid-template-columns: 1fr 1fr;

    column-gap: 32px;

    row-gap: 14px;

}


/* ==========================================
   DETAIL ITEM
   ========================================== */

.lokasi-detail-item {

    padding-bottom: 10px;

    border-bottom: 1px solid #f3f4f6;

}


.lokasi-detail-label {

    font-size: 10px;

    font-weight: 700;

    color: #9ca3af;

    text-transform: uppercase;

    letter-spacing: .03em;

    margin-bottom: 3px;

}


.lokasi-detail-value {

    font-size: 13px;

    font-weight: 500;

    color: #111827;

    line-height: 1.4;

}


/* ==========================================
   STATUS
   ========================================== */

.badge-status {

    display: inline-block;

    padding: 3px 10px;

    border-radius: 20px;

    font-size: 11px;

    font-weight: 600;

}


.badge-selesai {

    background: #dcfce7;

    color: #15803d;

}


.badge-dibatalkan {

    background: #fee2e2;

    color: #dc2626;

}


/* ==========================================
   BUTTONS
   ========================================== */

.btn-lokasi-edit,
.btn-lokasi-delete {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    height: 34px;

    padding: 0 15px;

    border-radius: 7px;

    font-size: 12px;

    font-weight: 600;

    text-decoration: none !important;

    transition: all .2s ease;

    border: 1px solid transparent;

}


/* UBAH */

.btn-lokasi-edit {

    background: #185c37;

    border-color: #185c37;

    color: #ffffff !important;

}


.btn-lokasi-edit:hover {

    background: #12482b;

    border-color: #12482b;

    color: #ffffff !important;

}


/* HAPUS */

.btn-lokasi-delete {

    background: #ffffff;

    border-color: #e5e7eb;

    color: #c0392b !important;

}


.btn-lokasi-delete:hover {

    background: #fbe4e4;

    border-color: #f3b8b8;

    color: #a12622 !important;

}


/* ==========================================
   RESPONSIVE
   ========================================== */

@media (max-width: 700px) {

    .lokasi-view-header {

        flex-direction: column;

        align-items: flex-start;

        gap: 16px;

    }


    .lokasi-view-header-action {

        width: 100%;

        margin-left: 0;

    }


    .btn-lokasi-edit,
    .btn-lokasi-delete {

        flex: 1;

    }


    .lokasi-detail-grid {

        grid-template-columns: 1fr;

    }

}

CSS

);

?>