<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Unit $model */

$this->title = $model->nama_unit;

?>
<div class="breadcrumb">
    <a href="<?= Yii::$app->homeUrl ?>">Dashboard</a> &nbsp;›&nbsp;
    <?= Html::a('Kelola Unit & Lokasi', ['/lokasi/index']) ?> &nbsp;›&nbsp;
    <span class="current">Detail Unit</span>
</div>

<div class="unit-view">

    <!-- ==========================================
         HEADER
         ========================================== -->

    <div class="unit-view-header">

        <div class="unit-view-header-content">

            <h1><?= Html::encode($this->title) ?></h1>

            <p>Detail data unit/fakultas.</p>

        </div>

        <div class="unit-view-header-action">

            <?= Html::a('Ubah', ['update', 'unit_id' => $model->unit_id], [
                'class' => 'btn-unit-edit'
            ]) ?>

            <?= Html::a('Hapus', ['delete', 'unit_id' => $model->unit_id], [
                'class' => 'btn-unit-delete',
                'data' => [
                    'confirm' => 'Yakin ingin menghapus unit ini?',
                    'method' => 'post',
                ],
            ]) ?>

        </div>

    </div>


    <!-- ==========================================
         DETAIL CARD
         ========================================== -->

    <div class="unit-view-card">

        <div class="unit-detail-grid">

            <div class="unit-detail-item">
                <div class="unit-detail-label">Unit ID</div>
                <div class="unit-detail-value"><?= Html::encode($model->unit_id) ?></div>
            </div>

            <div class="unit-detail-item">
                <div class="unit-detail-label">Nama Unit</div>
                <div class="unit-detail-value"><?= Html::encode($model->nama_unit) ?></div>
            </div>

            <div class="unit-detail-item">
                <div class="unit-detail-label">Kategori Unit</div>
                <div class="unit-detail-value"><?= Html::encode($model->kategori_unit) ?></div>
            </div>

            <div class="unit-detail-item">
                <div class="unit-detail-label">Status</div>
                <div class="unit-detail-value">
                    <?= $model->is_active
                        ? '<span class="badge-status badge-selesai">Aktif</span>'
                        : '<span class="badge-status badge-dibatalkan">Nonaktif</span>' ?>
                </div>
            </div>

            <div class="unit-detail-item">
                <div class="unit-detail-label">Dibuat Oleh</div>
                <div class="unit-detail-value"><?= $model->created_by !== null ? Html::encode($model->created_by) : '-' ?></div>
            </div>

            <div class="unit-detail-item">
                <div class="unit-detail-label">Dibuat Pada</div>
                <div class="unit-detail-value"><?= $model->created_at !== null ? Html::encode($model->created_at) : '-' ?></div>
            </div>

            <div class="unit-detail-item">
                <div class="unit-detail-label">Diubah Oleh</div>
                <div class="unit-detail-value"><?= $model->updated_by !== null ? Html::encode($model->updated_by) : '-' ?></div>
            </div>

            <div class="unit-detail-item">
                <div class="unit-detail-label">Diubah Pada</div>
                <div class="unit-detail-value"><?= $model->updated_at !== null ? Html::encode($model->updated_at) : '-' ?></div>
            </div>

        </div>

    </div>

</div>


<?php

$this->registerCss(<<<CSS

/* ==========================================
   UNIT VIEW
   ========================================== */

.unit-view {
    padding: 0;
}


/* ==========================================
   HEADER
   ========================================== */

.unit-view-header {
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

.unit-view-header-content h1 {
    margin: 0 0 5px 0;
    font-size: 22px;
    font-weight: 700;
    color: #111827;
}

.unit-view-header-content p {
    margin: 0;
    font-size: 13px;
    color: #6b7280;
}

.unit-view-header-action {
    display: flex;
    gap: 10px;
    flex-shrink: 0;
    margin-left: 20px;
}


/* ==========================================
   DETAIL CARD
   ========================================== */

.unit-view-card {
    background: #ffffff;
    border: 1px solid #dfe3e8;
    border-radius: 12px;
    padding: 20px 22px;
    box-sizing: border-box;
}

.unit-detail-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    column-gap: 32px;
    row-gap: 14px;
}

.unit-detail-item {
    padding-bottom: 10px;
    border-bottom: 1px solid #f3f4f6;
}

.unit-detail-label {
    font-size: 10px;
    font-weight: 700;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: .03em;
    margin-bottom: 3px;
}

.unit-detail-value {
    font-size: 13px;
    font-weight: 500;
    color: #111827;
    line-height: 1.4;
}

.unit-detail-value .badge-status {
    font-size: 11px;
}


/* ==========================================
   BUTTONS
   ========================================== */

.btn-unit-edit,
.btn-unit-delete {
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

.btn-unit-edit {
    background: #185c37;
    border-color: #185c37;
    color: #ffffff !important;
}

.btn-unit-edit:hover {
    background: #12482b;
    border-color: #12482b;
    color: #ffffff !important;
}

.btn-unit-delete {
    background: #ffffff;
    border-color: #e5e7eb;
    color: #c0392b !important;
}

.btn-unit-delete:hover {
    background: #fbe4e4;
    border-color: #f3b8b8;
    color: #a12622 !important;
}


/* ==========================================
   RESPONSIVE
   ========================================== */

@media (max-width: 700px) {

    .unit-view-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
    }

    .unit-view-header-action {
        width: 100%;
        margin-left: 0;
    }

    .btn-unit-edit,
    .btn-unit-delete {
        flex: 1;
    }

    .unit-detail-grid {
        grid-template-columns: 1fr;
    }

}

CSS
);

?>