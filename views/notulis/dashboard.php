<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var int $totalAgenda */
/** @var int $agendaHariIni */
/** @var int $belumDiunggah */
/** @var int $sudahDiunggah */
/** @var app\models\Agenda[] $agendas */

$this->title = 'Dashboard Notulen';
$statusLabels = [
    'Belum Diunggah' => ['class' => 'red', 'action' => 'Upload Notulen'],
    'Draft' => ['class' => 'gray', 'action' => 'Edit Notulen'],
    'Selesai Diunggah' => ['class' => 'green', 'action' => 'Lihat Berkas'],
    'Email Terkirim' => ['class' => 'blue', 'action' => 'Lihat Berkas'],
];
$statusFor = static function ($agenda): string {
    $lampirans = $agenda->lampirans;
    if (empty($lampirans)) {
        return 'Belum Diunggah';
    }
    $lampiran = end($lampirans);
    if ($lampiran->status === 'draft') {
        return 'Draft';
    }
    return !empty($lampiran->email_sent_at) ? 'Email Terkirim' : 'Selesai Diunggah';
};
?>
<div class="nt-breadcrumb">Home &nbsp;›&nbsp; <span>Dashboard</span></div>
<section class="nt-banner">
    <div>
        <h1>Selamat Datang Notulis <span class="wave">👋🏻</span></h1>
        <p>Berikut adalah ringkasan agenda dan aktivitas notulen hari ini.</p>
    </div>
    <a class="nt-button" href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/notulis/index'])) ?>">+ Daftar Agenda</a>
</section>
<section class="nt-stats nt-stats-two">
    <article class="nt-stat">
        <div class="nt-stat-icon red"><svg viewBox="0 0 24 24"><path d="M6 2h9l5 5v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2zm8 1.5V8h4.5L14 3.5zM8 12h8v1.5H8V12zm0 3h8v1.5H8V15z"/></svg></div>
        <div><div class="nt-stat-label">Agenda Menunggu Notulen</div><div class="nt-stat-value"><?= $belumDiunggah ?></div></div>
    </article>
    <article class="nt-stat">
        <div class="nt-stat-icon green"><svg viewBox="0 0 24 24"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm-1 15-4-4 1.4-1.4L11 14.2l4.6-4.6L17 11z"/></svg></div>
        <div><div class="nt-stat-label">Notulen Sudah Diunggah</div><div class="nt-stat-value"><?= $sudahDiunggah ?></div></div>
    </article>
</section>
<section class="nt-card">
    <div class="nt-card-head">
        <h2>Agenda Terbaru</h2>
        <div class="nt-filter">
            <?= Html::beginForm(['/notulis/index'], 'get', ['class' => 'nt-dashboard-search-form']) ?>
                <?= Html::textInput('search', '', ['class' => 'nt-dashboard-search-input', 'placeholder' => 'Cari agenda...', 'aria-label' => 'Cari agenda']) ?>
                <?= Html::submitButton('⌕', ['class' => 'nt-dashboard-search-button', 'aria-label' => 'Cari agenda']) ?>
            <?= Html::endForm() ?>
            <?= Html::a('☷&nbsp; Filter', ['/notulis/index']) ?>
        </div>
    </div>
    <div class="nt-table-wrap">
        <table class="nt-table nt-dashboard-table">
            <thead><tr><th>Judul Agenda</th><th>Tanggal &amp; Waktu</th><th>Lokasi</th><th>Status Notulensi</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php if (empty($agendas)): ?>
                <tr><td colspan="5" class="nt-empty">Belum ada agenda.</td></tr>
            <?php endif; ?>
            <?php foreach ($agendas as $agenda): $status = $statusFor($agenda); $statusInfo = $statusLabels[$status]; ?>
                <tr>
                    <td><strong><?= Html::encode($agenda->pembahasan) ?></strong><small><?= Html::encode($agenda->createdBy->nama ?? 'Agenda Universitas') ?></small></td>
                    <td><strong><?= Yii::$app->formatter->asDate($agenda->tanggal, 'php:d M Y') ?></strong><small><?= substr($agenda->waktu_mulai, 0, 5) ?> - <?= substr($agenda->waktu_selesai, 0, 5) ?> WIB</small></td>
                    <td><?= Html::encode($agenda->lokasi->lokasi ?? '-') ?></td>
                    <td><span class="nt-status <?= $statusInfo['class'] ?>"><?= Html::encode($status) ?></span></td>
                    <td>
                        <?php if ($status === 'Belum Diunggah'): ?>
                            <?= Html::a('<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M11 16h2V8l3 3 1.4-1.4L12 4.2 6.6 9.6 8 11l3-3v8zM5 20v-2h14v2H5z"/></svg><span>Upload</span>', ['/lampiran/create', 'agenda_id' => $agenda->agenda_id], ['class' => 'nt-action primary', 'title' => 'Upload Notulen', 'aria-label' => 'Upload Notulen']) ?>
                        <?php elseif ($status === 'Draft'): ?>
                            <?= Html::a('<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg><span>Edit</span>', ['/lampiran/update', 'agenda_id' => $agenda->agenda_id], ['class' => 'nt-action', 'title' => 'Edit Notulen', 'aria-label' => 'Edit Notulen']) ?>
                        <?php else: ?>
                            <?= Html::a('<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10zm0-2a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/></svg><span>Lihat</span>', ['/lampiran/index', 'agenda_id' => $agenda->agenda_id], ['class' => 'nt-action muted', 'title' => 'Lihat Berkas', 'aria-label' => 'Lihat Berkas']) ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
