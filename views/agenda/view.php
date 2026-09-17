<?php

/** @var yii\web\View $this */
/** @var app\models\Agenda $model */
/** @var array $hadirRows */
/** @var array $ringkasanHadir */

use yii\helpers\Html;

$this->title = $model->pembahasan;

/** @var app\models\User $identity */
$identity = Yii::$app->user->identity;
$canManage = $identity->can('manageAgenda');

$statusMap = [
    'terjadwal' => [
        'class' => 'badge-terjadwal',
        'label' => 'Terjadwal'
    ],
    'berlangsung' => [
        'class' => 'badge-berlangsung',
        'label' => 'Berlangsung'
    ],
    'selesai' => [
        'class' => 'badge-selesai',
        'label' => 'Selesai'
    ],
    'dibatalkan' => [
        'class' => 'badge-dibatalkan',
        'label' => 'Dibatalkan'
    ],
];

$status = $statusMap[$model->status] ?? [
    'class' => '',
    'label' => Html::encode($model->status)
];

$unitName = $model->lokasi->unit->nama_unit ?? '-';

?>

<div class="breadcrumb">

    <a href="<?= Yii::$app->homeUrl ?>">
        Dashboard
    </a>

    &nbsp;›&nbsp;

    <?= Html::a(
        'Kelola Agenda',
        ['/agenda/index']
    ) ?>

    &nbsp;›&nbsp;

    <span class="current">
        Detail
    </span>

</div>

<div class="agenda-view-header">

    <div class="agenda-view-header-content">

        <h1>
            <?= Html::encode($model->pembahasan) ?>
        </h1>

        <p>

            <span class="badge-status <?= $status['class'] ?>">
                <?= $status['label'] ?>
            </span>

            <?php if (!empty($model->nomor_surat)): ?>

                &nbsp;&middot;&nbsp;

                <?= Html::encode($model->nomor_surat) ?>

            <?php endif; ?>

        </p>

    </div>


    <?php if ($canManage): ?>

        <div class="agenda-view-header-action">

            <?= Html::a(
                'Ubah',
                [
                    'update',
                    'id' => $model->agenda_id
                ],
                [
                    'class' => 'btn-agenda-edit'
                ]
            ) ?>


            <?= Html::a(
                'Hapus',
                [
                    'delete',
                    'id' => $model->agenda_id
                ],
                [
                    'class' => 'btn-agenda-delete',

                    'data' => [
                        'confirm' => 'Yakin ingin menghapus agenda ini?',
                        'method' => 'post',
                    ],
                ]
            ) ?>

        </div>

    <?php endif; ?>

</div>


<div class="agenda-view-layout">

    <div class="agenda-card">

        <div class="agenda-detail-grid">


            <div class="agenda-detail-item">

                <div class="agenda-detail-label">
                    Tanggal
                </div>

                <div class="agenda-detail-value">

                    <?= Yii::$app->formatter->asDate(
                        $model->tanggal,
                        'php:d F Y'
                    ) ?>

                </div>

            </div>


            <div class="agenda-detail-item">

                <div class="agenda-detail-label">
                    Waktu
                </div>

                <div class="agenda-detail-value">

                    <?= substr($model->waktu_mulai, 0, 5) ?>

                    -

                    <?= substr($model->waktu_selesai, 0, 5) ?>

                    WIB

                </div>

            </div>


            <div class="agenda-detail-item">

                <div class="agenda-detail-label">
                    Lokasi
                </div>

                <div class="agenda-detail-value">

                    <?= Html::encode(
                        $model->lokasi->lokasi ?? '-'
                    ) ?>

                </div>

            </div>


            <div class="agenda-detail-item">

                <div class="agenda-detail-label">
                    Unit
                </div>

                <div class="agenda-detail-value">

                    <?= Html::encode($unitName) ?>

                </div>

            </div>


            <div class="agenda-detail-item">

                <div class="agenda-detail-label">
                    Tahun Akademik
                </div>

                <div class="agenda-detail-value">

                    <?= Html::encode(
                        $model->tahun_akademik
                    ) ?>

                </div>

            </div>


            <div class="agenda-detail-item">

                <div class="agenda-detail-label">
                    Dibuat Pada
                </div>

                <div class="agenda-detail-value">

                    <?= Yii::$app->formatter->asDatetime(
                        $model->created_at,
                        'php:d M Y H:i'
                    ) ?>

                    WIB

                </div>

            </div>

        </div>


        <!-- ==================================
             DESKRIPSI
             ================================== -->

        <div class="agenda-detail-full">

            <div class="agenda-detail-label">
                Deskripsi
            </div>

            <div class="agenda-detail-value">

                <?php if (empty($model->deskripsi)): ?>

                    <span class="empty-text">
                        Tidak ada deskripsi.
                    </span>

                <?php else: ?>

                    <?= nl2br(
                        Html::encode($model->deskripsi)
                    ) ?>

                <?php endif; ?>

            </div>

        </div>

    </div>

    <!-- ======================================
         DAFTAR HADIR
         ====================================== -->

    <div class="agenda-card hadir-card">

        <div class="agenda-card-header">

            <h2>
                Daftar Hadir
            </h2>

            <?= Html::a(
                'Lihat Selengkapnya',
                [
                    '/member/daftar-hadir',
                    'agenda_id' => $model->agenda_id,
                ]
            ) ?>

        </div>


        <div class="hadir-summary">

            <div class="hadir-summary-item">
                <span class="hadir-summary-value"><?= $ringkasanHadir['hadir'] ?></span>
                <span class="hadir-summary-label">Hadir</span>
            </div>

            <div class="hadir-summary-item">
                <span class="hadir-summary-value"><?= $ringkasanHadir['tidak_hadir'] ?></span>
                <span class="hadir-summary-label">Belum Hadir</span>
            </div>

            <?php if ($ringkasanHadir['walk_in'] > 0): ?>
                <div class="hadir-summary-item">
                    <span class="hadir-summary-value"><?= $ringkasanHadir['walk_in'] ?></span>
                    <span class="hadir-summary-label">Tanpa Undangan</span>
                </div>
            <?php endif; ?>

        </div>


        <?php if (empty($hadirRows)): ?>

            <p class="empty-text" style="margin-top:12px;">
                Belum ada peserta yang diundang atau melakukan absensi
                untuk agenda ini.
            </p>

        <?php else: ?>

            <div class="table-responsive">

                <table class="table hadir-mini-table">

                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Waktu Scan</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php
                        // Cukup tampilkan 5 baris teratas di kartu ringkas ini;
                        // daftar lengkap ada di link "Lihat Selengkapnya" di atas.
                        $preview = array_slice($hadirRows, 0, 5);
                        ?>

                        <?php foreach ($preview as $row): ?>

                            <?php $hadir = $row['absensi_id'] !== null; ?>

                            <tr>
                                <td>
                                    <?= Html::encode($row['nama']) ?>

                                    <?php if ($row['sumber'] === 'walk_in'): ?>
                                        <span class="hadir-tag-walkin">Tanpa undangan</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?= $hadir
                                        ? Html::encode(
                                            Yii::$app->formatter->asTime($row['waktu_scan']) . ' WIB'
                                        )
                                        : '-'
                                    ?>
                                </td>

                                <td>
                                    <?php if ($hadir): ?>
                                        <span class="status-badge badge-hadir">&#10003; Hadir</span>
                                    <?php else: ?>
                                        <span class="status-badge badge-tidak">&#10005; Belum</span>
                                    <?php endif; ?>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

            <?php if (count($hadirRows) > 5): ?>
                <p class="hadir-more-hint">
                    dan <?= count($hadirRows) - 5 ?> peserta lainnya —
                    <?= Html::a('lihat semua', [
                        '/member/daftar-hadir',
                        'agenda_id' => $model->agenda_id,
                    ]) ?>
                </p>
            <?php endif; ?>

        <?php endif; ?>

    </div>

    <!-- ======================================
         QR CODE
         ====================================== -->

    <div class="agenda-card qr-card">

        <div class="agenda-card-header">

            <h2>
                QR Code Absensi
            </h2>

        </div>


        <?php

        $qrFileExists = !empty($model->qr_code_path)
            && is_file(
                Yii::getAlias(
                    '@webroot/' . $model->qr_code_path
                )
            );

        ?>


        <?php if ($qrFileExists): ?>

            <div class="qr-display">

                <?= Html::img(
                    '@web/' . $model->qr_code_path,
                    [
                        'alt' => 'QR Code Absensi',
                        'class' => 'qr-image'
                    ]
                ) ?>


                <div class="qr-title">
                    SCAN UNTUK PRESENSI
                </div>


                <div class="qr-description">
                    Scan QR Code ini untuk melakukan presensi
                    kehadiran rapat.
                </div>


                <div class="qr-actions">

                    <?= Html::a(
                        'Download QR',
                        Yii::getAlias(
                            '@web/' . $model->qr_code_path
                        ),
                        [
                            'class' => 'btn-qr-download',
                            'target' => '_blank',
                            'download' => true,
                        ]
                    ) ?>

                </div>

            </div>


        <?php elseif ($canManage): ?>

            <div class="qr-empty">

                <div class="qr-empty-icon">
                    QR
                </div>

                <p>
                    QR Code belum dibuat untuk agenda ini.
                </p>

                <?= Html::beginForm(
                    [
                        'generate-qr',
                        'id' => $model->agenda_id
                    ],
                    'post'
                ) ?>

                    <?= Html::submitButton(
                        '+ Generate QR Code',
                        [
                            'class' => 'btn-generate-qr'
                        ]
                    ) ?>

                <?= Html::endForm() ?>

            </div>


        <?php else: ?>

            <div class="qr-empty">

                <p>
                    QR Code belum dibuat oleh administrasi.
                </p>

            </div>

        <?php endif; ?>

    </div>

</div>


<?php

$this->registerCss(<<<CSS


/* ==========================================
   AGENDA VIEW
   ========================================== */

.agenda-view-header {

    background: #ffffff;

    border: 1px solid #eeeeee;

    border-radius: 12px;

    padding: 22px;

    margin-bottom: 18px;

    display: flex;

    align-items: center;

    justify-content: space-between;

    box-sizing: border-box;
}


.agenda-view-header-content h1 {

    margin: 0 0 5px 0;

    font-size: 22px;

    font-weight: 700;

    color: #111827;

}


.agenda-view-header-content p {

    margin: 0;

    font-size: 13px;

    color: #6b7280;

}


.agenda-view-header-action {

    display: flex;

    gap: 10px;

    flex-shrink: 0;

    margin-left: 20px;

}


/* ==========================================
   BUTTON
   ========================================== */

.btn-agenda-edit,
.btn-agenda-delete {

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


.btn-agenda-edit {

    background: #185c37;

    border-color: #185c37;

    color: #ffffff !important;

}


.btn-agenda-edit:hover {

    background: #12482b;

    border-color: #12482b;

    color: #ffffff !important;

}


.btn-agenda-delete {

    background: #ffffff;

    border-color: #e5e7eb;

    color: #c0392b !important;

}


.btn-agenda-delete:hover {

    background: #fbe4e4;

    border-color: #f3b8b8;

    color: #a12622;

}


/* ==========================================
   LAYOUT
   ========================================== */

.agenda-view-layout {

    display: grid;

    grid-template-columns: minmax(0, 1.6fr) minmax(300px, .8fr);

    gap: 18px;

    align-items: start;

}


/* ==========================================
   CARD
   ========================================== */

.agenda-card {

    background: #ffffff;

    border: 1px solid #dfe3e8;

    border-radius: 12px;

    padding: 20px 22px;

    box-sizing: border-box;

}


/* ==========================================
   DETAIL GRID
   ========================================== */

.agenda-detail-grid {

    display: grid;

    grid-template-columns: 1fr 1fr;

    column-gap: 32px;

    row-gap: 14px;

}


.agenda-detail-item {

    padding-bottom: 10px;

    border-bottom: 1px solid #f3f4f6;

}


.agenda-detail-label {

    font-size: 10px;

    font-weight: 700;

    color: #9ca3af;

    text-transform: uppercase;

    letter-spacing: .03em;

    margin-bottom: 3px;

}


.agenda-detail-value {

    font-size: 13px;

    font-weight: 500;

    color: #111827;

    line-height: 1.5;

}


/* ==========================================
   DESKRIPSI
   ========================================== */

.agenda-detail-full {

    margin-top: 18px;

    padding-top: 16px;

    border-top: 1px solid #f3f4f6;

}


.empty-text {

    color: #999;

    font-weight: 400;

}


/* ==========================================
   CARD HEADER
   ========================================== */

.agenda-card-header {

    margin-bottom: 18px;

}


.agenda-card-header h2 {

    margin: 0;

    font-size: 16px;

    font-weight: 700;

    color: #111827;

}


/* ==========================================
   QR DISPLAY
   ========================================== */

.qr-display {

    text-align: center;

    padding: 4px 0 0;

}


.qr-image {

    width: 200px;

    height: 200px;

    object-fit: contain;

    display: block;

    margin: 0 auto 14px;

}


.qr-title {

    font-size: 12px;

    font-weight: 700;

    color: #111827;

    letter-spacing: .05em;

    margin-bottom: 5px;

}


.qr-description {

    font-size: 12px;

    color: #6b7280;

    line-height: 1.5;

    margin-bottom: 15px;

}


.qr-actions {

    display: flex;

    justify-content: center;

}


.btn-qr-download {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    height: 34px;

    padding: 0 15px;

    border-radius: 7px;

    background: #185c37;

    border: 1px solid #185c37;

    color: #ffffff !important;

    font-size: 12px;

    font-weight: 600;

    text-decoration: none !important;

}


.btn-qr-download:hover {

    background: #12482b;

    border-color: #12482b;

}


/* ==========================================
   QR EMPTY
   ========================================== */

.qr-empty {

    text-align: center;

    padding: 20px 10px;

}


.qr-empty-icon {

    width: 52px;

    height: 52px;

    margin: 0 auto 12px;

    border-radius: 10px;

    background: #f3f4f6;

    color: #6b7280;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 14px;

    font-weight: 700;

}


.qr-empty p {

    color: #999;

    font-size: 12px;

    margin-bottom: 14px;

}


.btn-generate-qr {

    height: 34px;

    padding: 0 15px;

    border: none;

    border-radius: 7px;

    background: #185c37;

    color: #ffffff;

    font-size: 12px;

    font-weight: 600;

}


/* ==========================================
   RESPONSIVE
   ========================================== */

@media (max-width: 900px) {

    .agenda-view-layout {

        grid-template-columns: 1fr;

    }

}


@media (max-width: 700px) {

    .agenda-view-header {

        flex-direction: column;

        align-items: flex-start;

        gap: 16px;

    }


    .agenda-view-header-action {

        width: 100%;

        margin-left: 0;

    }


    .btn-agenda-edit,
    .btn-agenda-delete {

        flex: 1;

    }


    .agenda-detail-grid {

        grid-template-columns: 1fr;

    }

}


/* ==========================================
   DAFTAR HADIR (kartu ringkas)
   ========================================== */

.hadir-summary {
    display: flex;
    gap: 20px;
    margin: 4px 0 14px;
}

.hadir-summary-item {
    display: flex;
    flex-direction: column;
}

.hadir-summary-value {
    font-size: 20px;
    font-weight: 700;
    color: #111827;
}

.hadir-summary-label {
    font-size: 12px;
    color: #6b7280;
}

.hadir-mini-table th {
    font-size: 12px;
    color: #6b7280;
    font-weight: 600;
    text-align: left;
    padding-bottom: 6px;
}

.hadir-mini-table td {
    font-size: 13px;
    padding: 6px 0;
    border-top: 1px solid #f1f1f1;
}

.hadir-tag-walkin {
    display: inline-block;
    margin-left: 6px;
    font-size: 10px;
    background: #fef3c7;
    color: #92400e;
    padding: 1px 6px;
    border-radius: 4px;
}

.hadir-more-hint {
    margin: 10px 0 0;
    font-size: 12px;
    color: #6b7280;
}

CSS
);

?>


