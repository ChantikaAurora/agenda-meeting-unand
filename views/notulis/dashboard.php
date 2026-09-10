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
        <h1>Selamat Datang Notulis</h1>
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
        <div class="nt-filter"><a class="nt-dashboard-search" href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/notulis/index'])) ?>">⌕&nbsp; Cari agenda...</a><a href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/notulis/index'])) ?>">☷&nbsp; Filter</a></div>
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
                    <td><?= Html::a($statusInfo['action'], ['/notulis/index'], ['class' => 'nt-action']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
