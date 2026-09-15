<?php

/** @var yii\web\View $this */
/** @var int $totalAgenda */
/** @var int $agendaHariIni */
/** @var int $totalPeserta */
/** @var int $totalLampiran */
/** @var app\models\Agenda[] $agendaTerbaru */
/** @var app\models\Agenda[] $agendaMendatang */
/** @var string[] $tanggalDenganAgenda */
/** @var DateTime $calendarMonth */

use yii\helpers\Html;

$this->title = 'Dashboard';

/** @var app\models\User $identity */
$identity = Yii::$app->user->identity;
$roleLabel = $identity->role === 'administrasi' ? 'Administrasi' : 'Notulen';
$canManage = $identity->can('manageAgenda');

$statusMap = [
    'terjadwal' => ['class' => 'badge-terjadwal', 'label' => 'Terjadwal'],
    'berlangsung' => ['class' => 'badge-berlangsung', 'label' => 'Berlangsung'],
    'selesai' => ['class' => 'badge-selesai', 'label' => 'Selesai'],
    'dibatalkan' => ['class' => 'badge-dibatalkan', 'label' => 'Dibatalkan'],
];

// Kalender dan waktu sudah otomatis mengikuti di waktu aslinya dan bukan dari server
$daysInMonth = (int) $calendarMonth->format('t');
$startWeekday = (int) $calendarMonth->format('w');
$monthLabel = $calendarMonth->format('F Y');
$isCurrentMonth = $calendarMonth->format('Y-m') === date('Y-m');
$today = $isCurrentMonth ? (int) date('j') : 0;

$prevMonthParam = (clone $calendarMonth)->modify('-1 month')->format('Y-m');
$nextMonthParam = (clone $calendarMonth)->modify('+1 month')->format('Y-m');

$agendaDaySet = array_flip(array_map(
    static fn($tgl) => (int) date('j', strtotime($tgl)),
    $tanggalDenganAgenda
));
?>
<div class="breadcrumb">
    <a href="<?= Yii::$app->homeUrl ?>">Home</a> &nbsp;›&nbsp; <span class="current">Dashboard</span>
</div>

<div class="dash-banner">
    <div>
        <h1>Selamat Datang <?= Html::encode($roleLabel) ?> <span class="wave">👋🏻</span></h1>
        <p>Berikut adalah ringkasan agenda dan aktivitas hari ini.</p>
    </div>
    <?php if ($canManage): ?>
        <?= Html::a('+ Buat Agenda Baru', ['/agenda/create'], ['class' => 'btn-primary-sm']) ?>
    <?php endif; ?>
</div>

<div class="dash-layout">
    <div>
        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-icon blue"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M7 2v2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-2V2h-2v2H9V2H7zM5 9h14v11H5V9z"/></svg></div>
                <div>
                    <div class="stat-label">Total Agenda</div>
                    <div class="stat-value"><?= $totalAgenda ?></div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M7 2v2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-2V2h-2v2H9V2H7zM5 9h14v11H5V9zm2 2h5v5H9v-5z"/></svg></div>
                <div>
                    <div class="stat-label">Agenda Hari Ini</div>
                    <div class="stat-value"><?= $agendaHariIni ?></div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon yellow"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg></div>
                <div>
                    <div class="stat-label">Peserta Terdaftar</div>
                    <div class="stat-value"><?= $totalPeserta ?></div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon gray"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M6 2a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6H6zm7 1.5L18.5 9H13V3.5zM8 13h8v1.5H8V13zm0 3.5h8V18H8v-1.5z"/></svg></div>
                <div>
                    <div class="stat-label">Lampiran Tersedia</div>
                    <div class="stat-value"><?= $totalLampiran ?></div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2>Agenda Terbaru</h2>
                <?= Html::a('Lihat Semua', ['/agenda/index']) ?>
            </div>
            <table class="table-clean">
                <thead>
                    <tr>
                        <th>Judul Agenda</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($agendaTerbaru)): ?>
                    <tr><td colspan="5" class="table-empty">Belum ada agenda.</td></tr>
                <?php endif; ?>
                <?php foreach ($agendaTerbaru as $a):
                    $status = $statusMap[$a->status] ?? ['class' => '', 'label' => Html::encode($a->status)];
                ?>
                    <tr>
                        <td><?= Html::a(Html::encode($a->pembahasan), ['/agenda/view', 'id' => $a->agenda_id], ['class' => 'row-link']) ?></td>
                        <td><?= Yii::$app->formatter->asDate($a->tanggal, 'php:d M Y') ?></td>
                        <td><?= substr($a->waktu_mulai, 0, 5) ?> - <?= substr($a->waktu_selesai, 0, 5) ?></td>
                        <td><span class="badge-status <?= $status['class'] ?>"><?= $status['label'] ?></span></td>
                        <td>
                            <div class="action-icons">
                                <?= Html::a(
                                    '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10zm0-8a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/></svg>',
                                    ['/agenda/view', 'id' => $a->agenda_id],
                                    ['title' => 'Lihat']
                                ) ?>
                                <?php if ($canManage): ?>
                                    <?= Html::a(
                                        '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>',
                                        ['/agenda/update', 'id' => $a->agenda_id],
                                        ['title' => 'Ubah']
                                    ) ?>
                                    <?= Html::a(
                                        '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M6 7h12l-1 14H7L6 7zm3-4h6l1 2H8l1-2z"/></svg>',
                                        ['/agenda/delete', 'id' => $a->agenda_id],
                                        [
                                            'title' => 'Hapus',
                                            'class' => 'danger',
                                            'data' => ['confirm' => 'Yakin ingin menghapus agenda ini?', 'method' => 'post'],
                                        ]
                                    ) ?>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div>
        <div class="card" style="margin-bottom:20px;">
            <div class="mini-calendar-nav">
                <span class="mini-calendar-title"><?= Html::encode($monthLabel) ?></span>
                <div>
                    <?= Html::a('&lsaquo;', ['/dashboard/index', 'month' => $prevMonthParam], ['class' => 'cal-nav-btn', 'title' => 'Bulan sebelumnya']) ?>
                    <?= Html::a('&rsaquo;', ['/dashboard/index', 'month' => $nextMonthParam], ['class' => 'cal-nav-btn', 'title' => 'Bulan berikutnya']) ?>
                </div>
            </div>
            <table class="mini-calendar">
                <thead>
                    <tr><th>Min</th><th>Sen</th><th>Sel</th><th>Rab</th><th>Kam</th><th>Jum</th><th>Sab</th></tr>
                </thead>
                <tbody>
                <?php
                $day = 1;
                $cell = 0;
                echo '<tr>';
                for ($i = 0; $i < $startWeekday; $i++) {
                    echo '<td class="empty">&nbsp;</td>';
                    $cell++;
                }
                while ($day <= $daysInMonth) {
                    if ($cell > 0 && $cell % 7 === 0) {
                        echo '</tr><tr>';
                    }
                    $isToday = $day === $today;
                    $hasAgenda = isset($agendaDaySet[$day]);
                    echo '<td class="' . ($isToday ? 'today' : '') . '">' . $day;
                    if ($hasAgenda && !$isToday) {
                        echo '<div class="agenda-dot"></div>';
                    }
                    echo '</td>';
                    $day++;
                    $cell++;
                }
                echo '</tr>';
                ?>
                </tbody>
            </table>
        </div>

        <div class="card">
            <div class="card-header"><h2>Agenda Mendatang</h2></div>
            <?php if (empty($agendaMendatang)): ?>
                <p style="color:#999;font-size:0.85rem;">Tidak ada agenda mendatang.</p>
            <?php endif; ?>
            <?php foreach ($agendaMendatang as $a): ?>
                <div class="upcoming-item">
                    <div class="upcoming-date">
                        <?= date('d', strtotime($a->tanggal)) ?>
                        <span><?= mb_strtoupper(date('M', strtotime($a->tanggal))) ?></span>
                    </div>
                    <div>
                        <?= Html::a(Html::encode($a->pembahasan), ['/agenda/view', 'id' => $a->agenda_id], ['class' => 'upcoming-title']) ?>
                        <div class="upcoming-time">⏱ <?= substr($a->waktu_mulai, 0, 5) ?> - <?= substr($a->waktu_selesai, 0, 5) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?= Html::a('Lihat Kalender Penuh', ['/agenda/index'], ['style' => 'color:var(--sirat-green);font-size:0.82rem;text-decoration:none;font-weight:600;']) ?>
        </div>
    </div>
</div>
