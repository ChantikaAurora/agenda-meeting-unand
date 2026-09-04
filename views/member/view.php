<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Member $model */

$this->title = $model->nama;

$inisial = strtoupper(mb_substr($model->nama, 0, 1));

?>

<div class="member-view">

    <!-- ==========================================
         BREADCRUMB
         ========================================== -->

    <div class="member-breadcrumb">

        <?= Html::a(
            'Dashboard',
            ['site/index'],
            [
                'class' => 'member-breadcrumb-link'
            ]
        ) ?>

        <span class="member-breadcrumb-separator">›</span>

        <?= Html::a(
            'Kelola Member',
            ['index'],
            [
                'class' => 'member-breadcrumb-link'
            ]
        ) ?>

        <span class="member-breadcrumb-separator">›</span>

        <span class="member-breadcrumb-current">
            Detail Member
        </span>

    </div>


    <!-- ==========================================
         HEADER
         ========================================== -->

    <div class="member-view-header">

        <div class="member-header-content">

            <!-- AVATAR -->

            <div class="member-avatar">

                <?= Html::encode($inisial) ?>

            </div>


            <!-- INFORMASI MEMBER -->

            <div class="member-header-info">

                <h1>
                    <?= Html::encode($model->nama) ?>
                </h1>

                <p>

                    <?= Html::encode(
                        $model->jabatan ?? '-'
                    ) ?>

                    <?php if (!empty($model->instansi)): ?>

                        <span class="member-dot">•</span>

                        <?= Html::encode(
                            $model->instansi
                        ) ?>

                    <?php endif; ?>

                </p>

            </div>

        </div>


        <!-- ==========================================
             TOMBOL
             ========================================== -->

        <div class="member-header-actions">

            <?= Html::a(
                'Ubah',
                [
                    'update',
                    'member_id' => $model->member_id
                ],
                [
                    'class' => 'btn-member-edit'
                ]
            ) ?>


            <?= Html::a(
                'Hapus',
                [
                    'delete',
                    'member_id' => $model->member_id
                ],
                [
                    'class' => 'btn-member-delete',

                    'data' => [

                        'confirm' =>
                            'Yakin ingin menghapus data ' .
                            Html::encode($model->nama) .
                            '?',

                        'method' => 'post',

                    ],
                ]
            ) ?>

        </div>

    </div>


    <!-- ==========================================
         DETAIL MEMBER
         ========================================== -->

    <div class="member-view-card">

        <div class="member-detail-grid">


            <!-- ==================================
                 TIPE IDENTITAS
                 ================================== -->

            <div class="member-detail-item">

                <div class="member-detail-label">
                    Tipe Identitas
                </div>

                <div class="member-detail-value">

                    <?= Html::encode(
                        $model->tipe_identitas ?? '-'
                    ) ?>

                </div>

            </div>


            <!-- ==================================
                 NOMOR IDENTITAS
                 ================================== -->

            <div class="member-detail-item">

                <div class="member-detail-label">
                    Nomor Identitas
                </div>

                <div class="member-detail-value">

                    <?= Html::encode(
                        $model->identitas_number ?? '-'
                    ) ?>

                </div>

            </div>


            <!-- ==================================
                 EMAIL
                 ================================== -->

            <div class="member-detail-item">

                <div class="member-detail-label">
                    Email
                </div>

                <div class="member-detail-value">

                    <?php if (!empty($model->email)): ?>

                        <?= Html::mailto(
                            $model->email,
                            $model->email
                        ) ?>

                    <?php else: ?>

                        -

                    <?php endif; ?>

                </div>

            </div>


            <!-- ==================================
                 TERDAFTAR SEJAK
                 ================================== -->

            <div class="member-detail-item">

                <div class="member-detail-label">
                    Terdaftar Sejak
                </div>

                <div class="member-detail-value">

                    <?php if (!empty($model->created_at)): ?>

                        <?= date(
                            'd M Y H:i',
                            strtotime($model->created_at)
                        ) ?>

                    <?php else: ?>

                        -

                    <?php endif; ?>

                </div>

            </div>


            <!-- ==================================
                 MEMBER ID
                 ================================== -->

            <div class="member-detail-item">

                <div class="member-detail-label">
                    Member ID
                </div>

                <div class="member-detail-value">

                    <?= Html::encode(
                        $model->member_id
                    ) ?>

                </div>

            </div>


            <!-- ==================================
                 INSTANSI
                 ================================== -->

            <div class="member-detail-item">

                <div class="member-detail-label">
                    Instansi
                </div>

                <div class="member-detail-value">

                    <?= Html::encode(
                        $model->instansi ?? '-'
                    ) ?>

                </div>

            </div>


        </div>

    </div>

</div>


<?php

$this->registerCss(<<<CSS

/* ==========================================
   MEMBER VIEW
   ========================================== */

.member-view {

    padding: 0;

}


/* ==========================================
   BREADCRUMB
   ========================================== */

.member-breadcrumb {

    display: flex;

    align-items: center;

    gap: 8px;

    margin-bottom: 12px;

    font-size: 11px;

    line-height: 18px;

}


.member-breadcrumb-link {

    color: #718096;

    text-decoration: none;

    transition: .2s;

}


.member-breadcrumb-link:hover {

    color: #185c37;

    text-decoration: none;

}


.member-breadcrumb-separator {

    color: #9ca3af;

    font-size: 13px;

}


.member-breadcrumb-current {

    color: #185c37;

    font-weight: 600;

}


/* ==========================================
   HEADER
   ========================================== */

.member-view-header {

    background: #ffffff;

    border: 1px solid #e5e7eb;

    border-radius: 12px;

    padding: 18px 20px;

    margin-bottom: 18px;

    display: flex;

    align-items: center;

    justify-content: space-between;

    box-sizing: border-box;

}


/* ==========================================
   HEADER CONTENT
   ========================================== */

.member-header-content {

    display: flex;

    align-items: center;

    gap: 14px;

    min-width: 0;

}


.member-avatar {

    width: 48px;

    height: 48px;

    border-radius: 50%;

    background: #0f6e56;

    color: #ffffff;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 19px;

    font-weight: 600;

    flex-shrink: 0;

}


.member-header-info {

    min-width: 0;

}


.member-header-info h1 {

    margin: 0 0 4px 0;

    font-size: 20px;

    font-weight: 700;

    color: #111827;

}


.member-header-info p {

    margin: 0;

    font-size: 12px;

    color: #6b7280;

}


.member-dot {

    margin: 0 5px;

    color: #9ca3af;

}


/* ==========================================
   HEADER ACTION
   ========================================== */

.member-header-actions {

    display: flex;

    align-items: center;

    gap: 8px;

    flex-shrink: 0;

    margin-left: 20px;

}


/* ==========================================
   BUTTON
   ========================================== */

.btn-member-edit,
.btn-member-delete {

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

    box-sizing: border-box;

}


/* ==========================================
   UBAH
   ========================================== */

.btn-member-edit {

    background: #185c37;

    border: 1px solid #185c37;

    color: #ffffff !important;

}


.btn-member-edit:hover {

    background: #12482b;

    border-color: #12482b;

    color: #ffffff !important;

}


/* ==========================================
   HAPUS
   ========================================== */

.btn-member-delete {

    background: #ffffff;

    border: 1px solid #e5e7eb;

    color: #dc2626 !important;

}


.btn-member-delete:hover {

    background: #fef2f2;

    border-color: #fecaca;

    color: #dc2626 !important;

}


/* ==========================================
   DETAIL CARD
   ========================================== */

.member-view-card {

    background: #ffffff;

    border: 1px solid #dfe3e8;

    border-radius: 12px;

    padding: 20px 20px;

    box-sizing: border-box;

}


/* ==========================================
   DETAIL GRID
   ========================================== */

.member-detail-grid {

    display: grid;

    grid-template-columns: 1fr 1fr;

    column-gap: 32px;

    row-gap: 0;

}


/* ==========================================
   DETAIL ITEM
   ========================================== */

.member-detail-item {

    padding: 13px 0;

    border-bottom: 1px solid #eeeeee;

    min-height: 54px;

    box-sizing: border-box;

}


/* ==========================================
   DETAIL LABEL
   ========================================== */

.member-detail-label {

    font-size: 9px;

    font-weight: 600;

    color: #8a94a6;

    text-transform: uppercase;

    letter-spacing: .35px;

    margin-bottom: 5px;

}


/* ==========================================
   DETAIL VALUE
   ========================================== */

.member-detail-value {

    font-size: 13px;

    font-weight: 500;

    color: #111827;

    line-height: 1.4;

}


.member-detail-value a {

    color: #2563eb;

    text-decoration: none;

}


.member-detail-value a:hover {

    text-decoration: underline;

}


/* ==========================================
   RESPONSIVE
   ========================================== */

@media (max-width: 800px) {

    .member-view-header {

        flex-direction: column;

        align-items: flex-start;

        gap: 16px;

    }


    .member-header-actions {

        width: 100%;

        margin-left: 0;

    }

}


@media (max-width: 600px) {

    .member-detail-grid {

        grid-template-columns: 1fr;

    }


    .member-header-content {

        width: 100%;

    }


    .member-header-actions {

        width: 100%;

    }


    .btn-member-edit,
    .btn-member-delete {

        flex: 1;

    }

}

CSS

);

?>