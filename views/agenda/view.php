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
$canManageLampiran = $identity->can('manageLampiran');

$statusMap = [
    'terjadwal' => [
        'class' => 'badge-terjadwal',
        'label' => 'Akan Datang'
    ],
    'berlangsung' => [
        'class' => 'badge-berlangsung',
        'label' => 'Sedang Berlangsung'
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
$agendaMembers = array_values(array_filter($model->agendaMembers, static function ($agendaMember) {
    return $agendaMember->deleted_at === null && $agendaMember->member !== null;
}));
$members = array_map(static function ($agendaMember) {
    return $agendaMember->member;
}, $agendaMembers);

$attendeeMemberIds = [];
if (!empty($hadirRows)) {
    foreach ($hadirRows as $row) {
        if ($row['sumber'] === 'undangan' && $row['member_id'] !== null && $row['absensi_id'] !== null) {
            $attendeeMemberIds[(int) $row['member_id']] = true;
        }
    }
}

$lampirans = array_values(array_filter($model->lampirans, static function ($lampiran) {
    return $lampiran->deleted_at === null
        && is_file(Yii::getAlias('@webroot/' . $lampiran->file_path));
}));
$invitedCount = count($members);
$confirmedCount = count($attendeeMemberIds);
$pendingCount = max(0, $invitedCount - $confirmedCount);

?>

<div class="breadcrumb">
    <a href="<?= Yii::$app->urlManager->createUrl(['/dashboard/index']) ?>">Dashboard</a>
    &nbsp;›&nbsp;
    <?= Html::a('Kelola Agenda', ['/agenda/index']) ?>
    &nbsp;›&nbsp;
    <span class="current">Detail</span>
</div>

<!-- ==========================================
     HEADER AGENDA (judul + aksi + status proses)
     ========================================== -->
<div class="agenda-view-header">
    <div class="agenda-view-header-top">
        <div class="agenda-view-header-content">
            <h1><?= Html::encode($model->pembahasan) ?></h1>
            <p>
                <span class="badge-status <?= $status['class'] ?>"><?= $status['label'] ?></span>
                <?php if (!empty($model->nomor_surat)): ?>
                    &nbsp;&middot;&nbsp;
                    <?= Html::encode($model->nomor_surat) ?>
                <?php endif; ?>
            </p>
        </div>

        <?php if ($canManage): ?>
            <div class="agenda-view-header-action">
                <?= Html::a('Ubah', ['update', 'id' => $model->agenda_id], ['class' => 'btn-agenda-edit']) ?>
                <?= Html::a('Hapus', ['delete', 'id' => $model->agenda_id], [
                    'class' => 'btn-agenda-delete',
                    'data' => [
                        'confirm' => 'Yakin ingin menghapus agenda ini?',
                        'method' => 'post',
                    ],
                ]) ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="agenda-view-layout">

    <div class="agenda-main-column">

        <!-- ======================================
             DETAIL AGENDA
             ====================================== -->
        <div class="agenda-card">
            <div class="agenda-detail-grid">
                <div class="agenda-detail-item">
                    <div class="agenda-detail-label">Tanggal</div>
                    <div class="agenda-detail-value">
                        <?= Yii::$app->formatter->asDate($model->tanggal, 'php:d F Y') ?>
                    </div>
                </div>

                <div class="agenda-detail-item">
                    <div class="agenda-detail-label">Waktu</div>
                    <div class="agenda-detail-value">
                        <?= substr($model->waktu_mulai, 0, 5) ?> - <?= substr($model->waktu_selesai, 0, 5) ?> WIB
                    </div>
                </div>

                <div class="agenda-detail-item">
                    <div class="agenda-detail-label">Lokasi</div>
                    <div class="agenda-detail-value">
                        <?= Html::encode($model->lokasi->lokasi ?? '-') ?>
                    </div>
                </div>

                <div class="agenda-detail-item">
                    <div class="agenda-detail-label">Unit</div>
                    <div class="agenda-detail-value"><?= Html::encode($unitName) ?></div>
                </div>

                <div class="agenda-detail-item">
                    <div class="agenda-detail-label">Tahun Akademik</div>
                    <div class="agenda-detail-value"><?= Html::encode($model->tahun_akademik) ?></div>
                </div>

                <div class="agenda-detail-item">
                    <div class="agenda-detail-label">Dibuat Pada</div>
                    <div class="agenda-detail-value">
                        <?= Yii::$app->formatter->asDatetime($model->created_at, 'php:d M Y H:i') ?> WIB
                    </div>
                </div>
            </div>

            <!-- ================== DESKRIPSI ================== -->
            <div class="agenda-detail-full">
                <div class="agenda-detail-label">Deskripsi</div>
                <div class="agenda-detail-value">
                    <?php if (empty($model->deskripsi)): ?>
                        <span class="empty-text">Tidak ada deskripsi.</span>
                    <?php else: ?>
                        <?= nl2br(Html::encode($model->deskripsi)) ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ======================================
             DAFTAR HADIR
             ====================================== -->
        <div class="agenda-card hadir-card">
            <div class="agenda-card-header">
                <h2>Daftar Hadir</h2>
                <?= Html::a('Lihat Selengkapnya', ['/member/daftar-hadir', 'agenda_id' => $model->agenda_id], [
                    'class' => 'btn-card-secondary',
                ]) ?>
            </div>

            <?php if (isset($ringkasanHadir)): ?>
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
            <?php endif; ?>

            <?php if (empty($hadirRows)): ?>
                <p class="empty-text" style="margin-top:12px;">
                    Belum ada peserta yang diundang atau melakukan absensi untuk agenda ini.
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
                            <?php $preview = array_slice($hadirRows, 0, 5); ?>
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
                                            ? Html::encode(Yii::$app->formatter->asTime($row['waktu_scan']) . ' WIB')
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
                        <?= Html::a('lihat semua', ['/member/daftar-hadir', 'agenda_id' => $model->agenda_id]) ?>
                    </p>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- ======================================
             DAFTAR UNDANGAN
             ====================================== -->
        <section class="agenda-card agenda-participants-card">
            <div class="agenda-section-heading">
                <h2>Daftar Undangan <span>&middot; <?= $invitedCount ?> orang</span></h2>
                <div class="agenda-section-heading-right">
                    <span class="invitation-inline-stats">
                        <strong><?= $invitedCount ?></strong> terkirim
                        &nbsp;&middot;&nbsp;
                        <strong><?= $confirmedCount ?></strong> dikonfirmasi
                        &nbsp;&middot;&nbsp;
                        <strong><?= $pendingCount ?></strong> menunggu
                    </span>
                    <div class="agenda-section-heading-buttons">
                        <?= Html::a('+ Tambah &amp; Kirim Undangan', ['/agenda-member/compose', 'agenda_id' => $model->agenda_id], [
                            'class' => 'btn-card-primary',
                        ]) ?>
                        <?= Html::a('Lihat Daftar Undangan', ['/agenda-member/index', 'agenda_id' => $model->agenda_id], [
                            'class' => 'btn-card-secondary',
                        ]) ?>
                    </div>
                </div>
            </div>

            <div class="participant-list">
                <?php foreach ($members as $member): ?>
                    <?php $isPresent = isset($attendeeMemberIds[(int) $member->member_id]); ?>
                    <div class="participant-row">
                        <span class="participant-avatar"><?= Html::encode(strtoupper(substr($member->nama, 0, 2))) ?></span>
                        <span class="participant-info">
                            <strong><?= Html::encode($member->nama) ?></strong>
                            <small><?= Html::encode($member->email ?: '-') ?></small>
                        </span>
                        <span class="participant-status <?= $isPresent ? 'is-confirmed' : 'is-pending' ?>">
                            <?= $isPresent ? 'Hadir' : 'Menunggu' ?>
                        </span>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($members)): ?>
                    <div class="documentation-empty">Belum ada peserta terdaftar.</div>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($members)): ?>
                <div class="participant-summary">
                    <span>Ringkasan Kehadiran</span>
                    <strong><?= $confirmedCount ?> dari <?= $invitedCount ?> peserta hadir</strong>
                </div>
            <?php endif; ?>
        </section>

    </div>

    <div class="agenda-side-column">

        <!-- ======================================
             QR CODE ABSENSI
             ====================================== -->
        <div class="agenda-card qr-card">
            <div class="agenda-card-header">
                <h2>QR Code Absensi</h2>
            </div>

            <?php
            $qrFileExists = !empty($model->qr_code_path)
                && is_file(Yii::getAlias('@webroot/' . $model->qr_code_path));
            ?>

            <?php if ($qrFileExists): ?>
                <div class="qr-display">
                    <?= Html::img('@web/' . $model->qr_code_path, [
                        'alt' => 'QR Code Absensi',
                        'class' => 'qr-image',
                    ]) ?>
                    <div class="qr-title">SCAN UNTUK PRESENSI</div>
                    <div class="qr-description">Scan QR Code ini untuk melakukan presensi kehadiran rapat.</div>
                    <div class="qr-actions">
                        <?= Html::a('Download QR', '@web/' . $model->qr_code_path, [
                            'class' => 'btn-qr-download',
                            'target' => '_blank',
                            'download' => true,
                        ]) ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="qr-empty">
                    <div class="qr-empty-icon">QR</div>
                    <p>QR Code belum tersedia untuk agenda ini. QR Code dibuat otomatis saat agenda pertama kali disimpan.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- ======================================
             CETAK & EKSPOR
             ====================================== -->
        <aside class="agenda-side-card agenda-export-card">
            <h2>Cetak &amp; Ekspor</h2>
            <?= Html::a('▣&nbsp;&nbsp;Cetak Dokumen Agenda', ['/cetak/dokumen', 'agenda_id' => $model->agenda_id], [
                'class' => 'btn-export',
            ]) ?>
        </aside>

        <!-- ======================================
             DOKUMENTASI RAPAT
             ====================================== -->
        <section class="agenda-card agenda-documentation-card">
            <div class="agenda-section-heading">
                <div>
                    <h2>Dokumentasi Rapat <span><?= count($lampirans) ?> foto</span></h2>
                </div>
                <?php if ($canManageLampiran): ?>
                    <?= Html::a('+ Tambah', ['/lampiran/create', 'agenda_id' => $model->agenda_id], ['class' => 'btn-card-add']) ?>
                <?php endif; ?>
            </div>

            <div class="documentation-grid">
                <?php foreach (array_slice($lampirans, 0, 3) as $lampiran): ?>
                    <div class="documentation-item">
                        <a href="<?= Html::encode(Yii::getAlias('@web/' . $lampiran->file_path)) ?>" target="_blank" rel="noopener">
                            <span class="documentation-preview">
                                <?php if (preg_match('/\.(png|jpe?g|jfif|gif|webp)$/i', $lampiran->file_path)): ?>
                                    <?= Html::img('@web/' . $lampiran->file_path, ['alt' => $lampiran->jenis_lampiran]) ?>
                                <?php else: ?>
                                    <span class="documentation-file">FILE</span>
                                <?php endif; ?>
                            </span>
                            <span><?= Html::encode(ucwords(str_replace(['-', '_'], ' ', $lampiran->jenis_lampiran))) ?></span>
                        </a>
                        <?php if ($canManageLampiran): ?>
                            <?= Html::beginForm(['/lampiran/delete', 'id' => $lampiran->lampiran_id], 'post', ['class' => 'documentation-delete-form']) ?>
                                <?= Html::submitButton('Hapus foto', [
                                    'class' => 'documentation-delete',
                                    'data' => ['confirm' => 'Hapus foto dokumentasi ini?'],
                                ]) ?>
                            <?= Html::endForm() ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($lampirans)): ?>
                    <?php for ($slot = 0; $slot < 2; $slot++): ?>
                        <span class="documentation-preview documentation-empty-slot"></span>
                    <?php endfor; ?>
                    <?php if ($canManageLampiran): ?>
                        <?= Html::a('+', ['/lampiran/create', 'agenda_id' => $model->agenda_id], ['class' => 'documentation-item documentation-add-placeholder']) ?>
                    <?php endif; ?>
                <?php elseif (count($lampirans) < 3 && $canManageLampiran): ?>
                    <?= Html::a('+', ['/lampiran/create', 'agenda_id' => $model->agenda_id], ['class' => 'documentation-item documentation-add-placeholder']) ?>
                <?php endif; ?>
            </div>

            <?php if (empty($lampirans)): ?>
                <div class="documentation-empty-note">Belum ada dokumentasi diunggah untuk rapat ini.</div>
            <?php endif; ?>
        </section>

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
    box-sizing: border-box;
}

.agenda-view-header-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
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
   BUTTON -- aksi header (Ubah / Hapus)
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
   LAYOUT -- dua kolom (kiri lebar, kanan sempit)
   ========================================== */

.agenda-view-layout {
    display: flex;
    gap: 16px;
    align-items: start;
}

.agenda-main-column,
.agenda-side-column {
    display: flex;
    min-width: 0;
    flex-direction: column;
    gap: 16px;
}

.agenda-main-column {
    flex: 1.6 1 0;
}

.agenda-side-column {
    flex: .8 1 0;
    min-width: 300px;
    position: sticky;
    top: 82px;
    align-self: flex-start;
}

/* ==========================================
   CARD (kontainer dasar semua kartu)
   ========================================== */

.agenda-card {
    background: #ffffff;
    border: 1px solid #dfe3e8;
    border-radius: 12px;
    padding: 16px 18px;
    box-sizing: border-box;
}

.agenda-side-card {
    background: #ffffff;
    border: 1px solid #dfe3e8;
    border-radius: 12px;
    padding: 15px 17px;
    box-sizing: border-box;
}

.agenda-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 12px;
}

.agenda-card-header h2 {
    margin: 0;
    font-size: 16px;
    font-weight: 700;
    color: #111827;
}

.agenda-section-heading {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 14px;
    margin-bottom: 14px;
}

.agenda-section-heading h2,
.agenda-side-card h2 {
    margin: 0;
    color: #111827;
    font-size: 15px;
    font-weight: 700;
}

.agenda-section-heading h2 span {
    color: #6b7280;
    font-size: 11px;
    font-weight: 500;
    text-transform: uppercase;
}

.agenda-section-heading-right {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 8px;
    flex-shrink: 0;
}

.agenda-section-heading-buttons {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    justify-content: flex-end;
}

.invitation-inline-stats {
    font-size: 11px;
    color: #9ca3af;
    white-space: nowrap;
}

.invitation-inline-stats strong {
    color: #374151;
    font-weight: 700;
}

.btn-card-add,
.btn-card-primary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 30px;
    padding: 0 12px;
    border: 1px solid #247b59;
    border-radius: 6px;
    background: #247b59;
    color: #ffffff !important;
    font-size: 11px;
    font-weight: 600;
    text-decoration: none !important;
    white-space: nowrap;
}

.btn-card-add:hover,
.btn-card-primary:hover {
    border-color: #185c37;
    background: #185c37;
}

.btn-card-secondary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 30px;
    padding: 0 12px;
    border: 1px solid #247b59;
    border-radius: 6px;
    background: #ffffff;
    color: #247b59 !important;
    font-size: 11px;
    font-weight: 600;
    text-decoration: none !important;
    white-space: nowrap;
}

.btn-card-secondary:hover {
    background: #f0f7f3;
    border-color: #185c37;
    color: #185c37 !important;
}

/* ==========================================
   DETAIL GRID
   ========================================== */

.agenda-detail-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    column-gap: 32px;
    row-gap: 10px;
}

.agenda-detail-item {
    padding-bottom: 7px;
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

.agenda-detail-full {
    margin-top: 13px;
    padding-top: 12px;
    border-top: 1px solid #f3f4f6;
}

.empty-text {
    color: #999;
    font-weight: 400;
}

/* ==========================================
   QR CODE
   ========================================== */

.qr-card {
    padding: 14px 16px;
}

.qr-display {
    text-align: center;
    padding: 4px 0 0;
}

.qr-image {
    width: 110px;
    height: 110px;
    object-fit: contain;
    display: block;
    margin: 0 auto 10px;
    padding: 8px;
    box-sizing: border-box;
    border: 1px solid #f0f1f2;
    border-radius: 8px;
    background: #f8f9f8;
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
    margin: 0 auto 10px;
    max-width: 220px;
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
    line-height: 1.6;
    margin: 0;
}

/* ==========================================
   CETAK & EKSPOR
   ========================================== */

.agenda-export-card {
    text-align: center;
}

.btn-export {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    min-height: 31px;
    margin-top: 7px;
    border: 1px solid #dfe3e8;
    border-radius: 6px;
    box-sizing: border-box;
    color: #374151 !important;
    font-size: 10px;
    font-weight: 600;
    text-decoration: none !important;
}

.btn-export:hover {
    background: #f6f8f6;
    border-color: #247b59;
}

.btn-export:disabled {
    background: #f3f4f6;
    border-color: #e5e7eb;
    color: #9ca3af !important;
    cursor: not-allowed;
    opacity: .75;
}

/* ==========================================
   DOKUMENTASI RAPAT
   ========================================== */

.agenda-documentation-card,
.agenda-participants-card {
    width: 100%;
}

.documentation-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 10px;
}

.documentation-item {
    min-width: 0;
    color: #374151 !important;
    font-size: 10px;
    font-weight: 600;
    text-decoration: none !important;
}

.documentation-item > a {
    display: block;
    color: #374151 !important;
    text-decoration: none !important;
}

.documentation-preview {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 56px;
    margin-bottom: 5px;
    overflow: hidden;
    border-radius: 7px;
    background: #e6e9e4;
}

.documentation-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.documentation-file {
    color: #6b7280;
    font-size: 10px;
}

.documentation-add-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 56px;
    border: 1px dashed #d1d5db;
    border-radius: 7px;
    color: #9ca3af !important;
    font-size: 20px;
    font-weight: 400;
}

.documentation-delete-form {
    margin-top: 5px;
}

.documentation-delete {
    padding: 0;
    border: 0;
    background: transparent;
    color: #c0392b;
    font-size: 10px;
    cursor: pointer;
}

.documentation-empty {
    grid-column: 1 / -1;
    color: #9ca3af;
    font-size: 12px;
}

.documentation-empty-note {
    margin-top: 10px;
    color: #9ca3af;
    font-size: 11px;
}

.agenda-documentation-card .agenda-section-heading {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
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

/* ==========================================
   DAFTAR UNDANGAN (kartu peserta)
   ========================================== */

.participant-list {
    border-top: 1px solid #f0f1f3;
}

.participant-summary {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-top: 14px;
    padding-top: 14px;
    border-top: 1px solid #f0f1f3;
    color: #9ca3af;
    font-size: 10px;
}

.participant-summary strong {
    color: #247b59;
    font-size: 10px;
}

.participant-row {
    display: flex;
    align-items: center;
    gap: 9px;
    min-height: 54px;
    border-bottom: 1px solid #f0f1f3;
}

.participant-row:last-child {
    border-bottom: none;
}

.participant-avatar {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    flex-shrink: 0;
    border-radius: 50%;
    background: #277a59;
    color: #ffffff;
    font-size: 9px;
    font-weight: 700;
}

.participant-info {
    display: flex;
    flex: 1;
    min-width: 0;
    flex-direction: column;
}

.participant-info strong {
    overflow: hidden;
    color: #111827;
    font-size: 11px;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.participant-info small,
.participant-status {
    color: #9ca3af;
    font-size: 9px;
}

.participant-status {
    padding: 3px 8px;
    border-radius: 10px;
    background: #f0f1f2;
    color: #6b7280;
    white-space: nowrap;
}

.participant-status.is-confirmed {
    background: #dff3e7;
    color: #247b59;
}

/* ==========================================
   RESPONSIVE
   ========================================== */

@media (max-width: 900px) {
    .agenda-view-layout {
        flex-direction: column;
    }

    .agenda-main-column,
    .agenda-side-column {
        width: 100%;
        position: static;
    }

    .documentation-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}

@media (max-width: 700px) {
    .agenda-view-header-top {
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

    .documentation-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .agenda-section-heading {
        align-items: flex-start;
        flex-direction: column;
    }

    .agenda-section-heading-right {
        align-items: flex-start;
        width: 100%;
    }

    .agenda-section-heading-buttons {
        justify-content: flex-start;
        width: 100%;
    }
}

CSS
);

?>