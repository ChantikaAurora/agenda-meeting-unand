<?php

/** @var yii\web\View $this */
/** @var string $content */

use yii\helpers\Html;
use app\assets\AppAsset;

AppAsset::register($this);

$this->beginPage();

$identity = Yii::$app->user->isGuest ? null : Yii::$app->user->identity;
$roleLabel = $identity && $identity->role === 'administrasi' ? 'Administrator' : 'Notulen';
$initials = $identity ? mb_strtoupper(mb_substr($identity->nama, 0, 1)) : '?';

$currentController = Yii::$app->controller->id;


$menuItems = [
    ['label' => 'Dashboard', 'controller' => 'dashboard', 'route' => ['/dashboard/index'], 'icon' => 'grid'],
    ['label' => 'Kelola Agenda', 'controller' => 'agenda', 'route' => ['/agenda/index'], 'icon' => 'calendar'],
    ['label' => 'Unit & Lokasi', 'controller' => 'lokasi', 'route' => ['/lokasi/index'], 'icon' => 'pin'],
    ['label' => 'Peserta', 'controller' => 'member', 'route' => ['/member/index'], 'icon' => 'users'],
    ['label' => 'Laporan', 'controller' => 'laporan', 'route' => null, 'icon' => 'chart'],
    ['label' => 'Lampiran', 'controller' => 'lampiran', 'route' => null, 'icon' => 'paperclip'],
];

$icons = [
    'grid' => '<svg viewBox="0 0 24 24"><path d="M4 4h7v7H4V4zm9 0h7v7h-7V4zM4 13h7v7H4v-7zm9 0h7v7h-7v-7z"/></svg>',
    'calendar' => '<svg viewBox="0 0 24 24"><path d="M7 2v2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-2V2h-2v2H9V2H7zM5 9h14v11H5V9z"/></svg>',
    'pin' => '<svg viewBox="0 0 24 24"><path d="M12 2a7 7 0 0 0-7 7c0 5.25 7 13 7 13s7-7.75 7-13a7 7 0 0 0-7-7zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5z"/></svg>',
    'users' => '<svg viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>',
    'chart' => '<svg viewBox="0 0 24 24"><path d="M5 9h3v11H5V9zm6-6h3v17h-3V3zm6 10h3v7h-3v-7z"/></svg>',
    'paperclip' => '<svg viewBox="0 0 24 24"><path d="M16.5 6v11a4 4 0 1 1-8 0V5a2.5 2.5 0 0 1 5 0v10a1 1 0 1 1-2 0V6H9.5v9a2.5 2.5 0 0 0 5 0V5a4 4 0 0 0-8 0v12a5.5 5.5 0 0 0 11 0V6h-1.5z"/></svg>',
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <style>
        /* ===== Variabel warna & shell utama (navbar, sidebar, konten) ===== */
        :root {
            --sirat-green: #1f4d2c;
            --sirat-green-light: #e9f2ec;
            --sirat-gold: #c9a227;
            --sirat-bg: #f5f6f8;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: var(--sirat-bg);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #1a1a1a;
        }
        .topbar {
            height: 64px;
            background: #fff;
            border-bottom: 3px solid var(--sirat-gold);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            position: sticky;
            top: 0;
            z-index: 20;
        }
        .topbar-brand { display: flex; align-items: center; gap: 10px; }
        .topbar-brand img { width: 30px; height: 30px; }
        .topbar-brand span { font-weight: 700; color: var(--sirat-green); font-size: 1.05rem; }
        .topbar-search { flex: 1; max-width: 420px; margin: 0 24px; }
        .topbar-search input {
            width: 100%; border: none; background: #f1f2f4; border-radius: 8px;
            padding: 9px 14px; font-size: 0.85rem;
        }
        .topbar-search input:focus { outline: 2px solid var(--sirat-green-light); }
        .topbar-right { display: flex; align-items: center; gap: 18px; }
        .topbar-icon { color: #666; width: 20px; height: 20px; cursor: pointer; }
        .topbar-user { display: flex; align-items: center; gap: 10px; }
        .topbar-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: var(--sirat-green); color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 0.9rem;
        }
        .topbar-user-info { line-height: 1.2; }
        .topbar-user-name { font-weight: 600; font-size: 0.85rem; }
        .topbar-user-role { font-size: 0.75rem; color: #888; }

        .app-shell { display: flex; min-height: calc(100vh - 64px); }
        .sidebar {
            width: 220px; background: #fff; border-right: 1px solid #eee;
            padding: 16px 0; display: flex; flex-direction: column; justify-content: space-between;
        }
        .sidebar-menu { list-style: none; margin: 0; padding: 0; }
        .sidebar-menu li a {
            display: flex; align-items: center; gap: 12px; padding: 11px 20px;
            color: #444; text-decoration: none; font-size: 0.88rem; border-left: 3px solid transparent;
        }
        .sidebar-menu li a svg { width: 18px; height: 18px; fill: currentColor; opacity: 0.75; }
        .sidebar-menu li a.active {
            background: var(--sirat-green-light); color: var(--sirat-green);
            border-left-color: var(--sirat-green); font-weight: 600;
        }
        .sidebar-menu li a.disabled { color: #bbb; cursor: default; }
        .sidebar-menu li a .badge-soon {
            margin-left: auto; font-size: 0.65rem; background: #f0f0f0; color: #999;
            padding: 2px 6px; border-radius: 6px;
        }
        .sidebar-footer { border-top: 1px solid #eee; padding-top: 8px; }
        .sidebar-footer a {
            display: flex; align-items: center; gap: 12px; padding: 11px 20px;
            color: #666; text-decoration: none; font-size: 0.85rem;
        }
        .sidebar-footer a.logout { color: #c0392b; }

        .main-content { flex: 1; padding: 24px 32px; }
        .breadcrumb { font-size: 0.8rem; color: #888; margin-bottom: 16px; }
        .breadcrumb a { color: #888; text-decoration: none; }
        .breadcrumb .current { color: var(--sirat-green); font-weight: 600; }

        /* ===== Dashboard ===== */
        .dash-banner {
            background: #fff; border-radius: 12px; padding: 24px;
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 20px;
        }
        .dash-banner h1 { margin: 0 0 4px; font-size: 1.7rem; font-weight: 700;}
        .dash-banner p { margin: 0; color: #777; font-size: 0.9rem; }
        .btn-primary-sm {
            background: var(--sirat-green); color: #fff; padding: 10px 18px;
            border-radius: 8px; text-decoration: none; font-size: 0.85rem;
            font-weight: 600; white-space: nowrap; border: none; cursor: pointer;
        }
        .wave {
            display: inline-block;
            animation: wave-animation 2.2s infinite;
            transform-origin: 70% 70%;
        }
        @keyframes wave-animation {
            0% { transform: rotate(0deg); }
            10% { transform: rotate(14deg); }
            20% { transform: rotate(-8deg); }
            30% { transform: rotate(14deg); }
            40% { transform: rotate(-4deg); }
            50% { transform: rotate(10deg); }
            60% { transform: rotate(0deg); }
            100% { transform: rotate(0deg); }
        }
        .dash-layout { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; align-items: start; }
        .stat-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; }
        .stat-card { background: #fff; border-radius: 12px; padding: 18px; display: flex; align-items: center; gap: 14px; }
        .stat-icon {
            width: 44px; height: 44px; border-radius: 10px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
        }
        .stat-icon svg { width: 20px; height: 20px; }
        .stat-icon.blue { background: #E4EEFF; color: #1a56b0; }
        .stat-icon.green { background: #E3F5E7; color: #1f7a3d; }
        .stat-icon.yellow { background: #FFF6D9; color: #a8821a; }
        .stat-icon.gray { background: #EFEFEF; color: #666; }
        .stat-label { font-size: 0.72rem; color: #999; text-transform: uppercase; letter-spacing: 0.02em; }
        .stat-value { font-size: 1.5rem; font-weight: 700; margin-top: 2px; }

        .card { background: #fff; border-radius: 12px; padding: 20px; }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; }
        .card-header h2 { margin: 0; font-size: 1.05rem; font-weight: 700;}
        .card-header a { color: var(--sirat-green); font-size: 0.82rem; text-decoration: none; font-weight: 600; }

        .table-clean { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
        .table-clean thead th {
            text-align: left; padding: 8px 10px; background: #f7f8f9;
            color: #888; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.03em;
            border-bottom: 1px solid #eee;
        }
        .table-clean thead th:first-child { border-radius: 8px 0 0 8px; }
        .table-clean thead th:last-child { border-radius: 0 8px 8px 0; text-align: center; }
        .table-clean tbody td { padding: 12px 10px; border-bottom: 1px solid #f3f3f3; }
        .table-clean tbody tr:last-child td { border-bottom: none; }
        .table-clean a.row-link { color: #1a1a1a; text-decoration: none; font-weight: 500; }
        .table-clean a.row-link:hover { color: var(--sirat-green); }
        .table-empty { padding: 16px 10px; color: #999; text-align: center; }

        .badge-status { padding: 3px 10px; border-radius: 12px; font-size: 0.72rem; font-weight: 600; display: inline-block; }
        .badge-terjadwal { background: #FFF6D9; color: #8a6d00; }
        .badge-berlangsung { background: #E4EEFF; color: #1a56b0; }
        .badge-selesai { background: #E3F5E7; color: #1f7a3d; }
        .badge-dibatalkan { background: #FBE4E4; color: #a12622; }

        .action-icons { display: flex; gap: 8px; justify-content: center; }
        .action-icons a { color: #999; display: inline-flex; }
        .action-icons a svg { width: 15px; height: 15px; }
        .action-icons a.danger:hover { color: #c0392b; }
        .action-icons a:hover { color: var(--sirat-green); }

        .mini-calendar-title { font-weight: 700; margin-bottom: 12px; font-size: 1.05rem;}
        .mini-calendar-nav {
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;
        }
        .cal-nav-btn {
            display: inline-flex; align-items: center; justify-content: center;
            width: 24px; height: 24px; border-radius: 6px;
            color: #666; text-decoration: none; font-size: 1rem; font-weight: 700;
        }
        .cal-nav-btn:hover { background: var(--sirat-green-light); color: var(--sirat-green); }
        .mini-calendar { width: 100%; border-collapse: collapse; text-align: center; font-size: 0.75rem; }
        .mini-calendar th { color: #999; font-weight: 500; padding-bottom: 6px; }
        .mini-calendar td { padding: 6px 0; border-radius: 6px; }
        .mini-calendar td.empty { color: #ddd; }
        .mini-calendar td.today { background: var(--sirat-green); color: #fff; font-weight: 700; }
        .mini-calendar .agenda-dot { width: 4px; height: 4px; background: var(--sirat-green); border-radius: 50%; margin: 2px auto 0; }

        .upcoming-item { display: flex; gap: 12px; margin-bottom: 14px; }
        .upcoming-date {
            background: var(--sirat-green-light); color: var(--sirat-green); border-radius: 8px;
            width: 44px; height: 44px; flex-shrink: 0;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            font-size: 0.7rem; font-weight: 700; line-height: 1.1;
        }
        .upcoming-date span { font-size: 0.6rem; font-weight: 600; }
        .upcoming-title { color: #1a1a1a; text-decoration: none; font-weight: 500; font-size: 0.85rem; }
        .upcoming-time { color: #999; font-size: 0.75rem; }

        /* ===== Filter bar (halaman Kelola Agenda) ===== */
        .filter-card { margin-bottom: 20px; }
        .filter-bar { display: flex; gap: 12px; align-items: center; flex-wrap: wrap; }
        .filter-search {
            flex: 1; min-width: 220px; display: flex; align-items: center; gap: 8px;
            background: #f1f2f4; border-radius: 8px; padding: 0 12px;
        }
        .filter-search svg { width: 16px; height: 16px; color: #999; flex-shrink: 0; }
        .filter-search-input {
            border: none; background: none; padding: 10px 0; font-size: 0.85rem; width: 100%; outline: none;
        }
        .filter-select {
            border: 1px solid #e0e0e0; border-radius: 8px; padding: 9px 12px;
            font-size: 0.85rem; background: #fff; color: #444;
        }

        .wt-line { display: flex; align-items: center; gap: 6px; font-size: 0.82rem; margin-bottom: 2px; }
        .wt-line svg { width: 13px; height: 13px; color: #888; flex-shrink: 0; }
        .wt-line.muted { color: #888; }

        .pagination-wrap { display: flex; justify-content: flex-end; margin-top: 16px; }
        .pager { list-style: none; display: flex; gap: 4px; margin: 0; padding: 0; }
        .pager-link {
            display: flex; align-items: center; justify-content: center;
            min-width: 30px; height: 30px; border-radius: 6px;
            color: #555; text-decoration: none; font-size: 0.82rem; padding: 0 6px;
        }
        .pager-link:hover { background: var(--sirat-green-light); color: var(--sirat-green); }
        .pager-active .pager-link { background: var(--sirat-green); color: #fff; font-weight: 600; }

        /* ===== Form (Buat/Ubah Agenda) ===== */
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: 0.85rem; font-weight: 600; color: #333; margin-bottom: 6px; }
        .form-control {
            width: 100%; border: 1px solid #ddd; border-radius: 8px;
            padding: 10px 14px; font-size: 0.9rem; background: #fff; color: #1a1a1a;
        }
        .form-control:focus {
            outline: none; border-color: var(--sirat-green);
            box-shadow: 0 0 0 3px rgba(31,77,44,0.1);
        }
        textarea.form-control { resize: vertical; }
        .form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }
        .form-error { color: #c0392b; font-size: 0.78rem; margin-top: 4px; }
        .form-actions { display: flex; gap: 10px; margin-top: 8px; }
        .btn-secondary-sm {
            background: #eee; color: #444; padding: 10px 18px; border-radius: 8px;
            text-decoration: none; font-size: 0.85rem; font-weight: 600; border: none; cursor: pointer;
        }
        @media (max-width: 700px) {
            .form-row-3 { grid-template-columns: 1fr; }
        }

        /* ===== Detail (Lihat Agenda) ===== */
        .detail-grid {
            display: grid; grid-template-columns: 1fr 1fr; gap: 20px;
            margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid #f0f0f0;
        }
        .detail-label { font-size: 0.72rem; color: #999; text-transform: uppercase; letter-spacing: 0.02em; margin-bottom: 4px; }
        .detail-value { font-size: 0.95rem; color: #1a1a1a; font-weight: 500; }
        .detail-full .detail-value { font-weight: 400; line-height: 1.7; color: #333; }
        @media (max-width: 700px) {
            .detail-grid { grid-template-columns: 1fr; gap: 16px; }
        }
        /* ===== QR Code ===== */
        .qr-display { text-align: center; }
        .qr-image { width: 220px; height: 220px; border: 1px solid #eee; border-radius: 8px; padding: 12px; }
        .qr-actions { display: flex; gap: 10px; justify-content: center; margin-top: 12px; }
        .qr-note { color: #999; font-size: 0.78rem; margin-top: 12px; }
    </style>
</head>
<body>
<?php $this->beginBody() ?>

<div class="topbar">
    <div class="topbar-brand">
        <?= Html::img('@web/images/logo-unand.png', ['alt' => '', 'onerror' => "this.style.display='none'"]) ?>
        <span>Universitas Andalas</span>
    </div>
    <div class="topbar-right">
        <svg class="topbar-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M12 22a2.5 2.5 0 0 0 2.45-2h-4.9A2.5 2.5 0 0 0 12 22zm7-6v-5a7 7 0 0 0-5.5-6.83V3a1.5 1.5 0 0 0-3 0v1.17A7 7 0 0 0 5 11v5l-1.7 1.7A1 1 0 0 0 4 19h16a1 1 0 0 0 .7-1.71L19 16z"/></svg>
        <svg class="topbar-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M19.4 13a7.9 7.9 0 0 0 0-2l2.1-1.6-2-3.5-2.5 1a8 8 0 0 0-1.7-1L14.9 3h-4l-.4 2.9a8 8 0 0 0-1.7 1l-2.5-1-2 3.5L6.4 11a7.9 7.9 0 0 0 0 2l-2.1 1.6 2 3.5 2.5-1a8 8 0 0 0 1.7 1l.4 2.9h4l.4-2.9a8 8 0 0 0 1.7-1l2.5 1 2-3.5L19.4 13zM12 15.5A3.5 3.5 0 1 1 12 8.5a3.5 3.5 0 0 1 0 7z"/></svg>
        <?php if ($identity): ?>
        <div class="topbar-user">
            <div class="topbar-avatar"><?= Html::encode($initials) ?></div>
            <div class="topbar-user-info">
                <div class="topbar-user-name"><?= Html::encode($identity->nama) ?></div>
                <div class="topbar-user-role"><?= Html::encode($roleLabel) ?></div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="app-shell">
    <div class="sidebar">
        <ul class="sidebar-menu">
            <?php foreach ($menuItems as $item): ?>
                <li>
                    <?php if ($item['route'] !== null): ?>
                        <?= Html::a(
                            $icons[$item['icon']] . '<span>' . Html::encode($item['label']) . '</span>',
                            $item['route'],
                            ['class' => 'active-check' . ($currentController === $item['controller'] ? ' active' : '')]
                        ) ?>
                    <?php else: ?>
                        <a href="#" class="disabled" onclick="return false;">
                            <?= $icons[$item['icon']] ?>
                            <span><?= Html::encode($item['label']) ?></span>
                            <span class="badge-soon">Segera</span>
                        </a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="sidebar-footer">
            <a href="#" onclick="return false;">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm.5 15h-1.5v-1.5h1.5V17zm1.55-6.13c-.4.4-.65.7-.75 1.2-.05.25-.08.5-.08.93h-1.4c0-.53.04-.98.17-1.35.15-.43.42-.8.85-1.23l.6-.6c.28-.28.42-.65.42-1.08 0-.9-.73-1.5-1.65-1.5-.9 0-1.65.6-1.65 1.5H8.16c0-1.66 1.5-3 3.34-3 1.85 0 3.34 1.34 3.34 3 0 .78-.32 1.4-.79 1.88z"/></svg>
                <span>Bantuan</span>
            </a>
            <?= Html::beginForm(['/site/logout'], 'post') ?>
            <?= Html::submitButton(
                '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M16 13v-2H7V8l-5 4 5 4v-3h9zm3-10H11a2 2 0 0 0-2 2v4h2V5h8v14h-8v-4H9v4a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2z"/></svg><span>Keluar</span>',
                ['class' => 'logout', 'style' => 'background:none;border:none;display:flex;align-items:center;gap:12px;padding:11px 20px;color:#c0392b;font-size:0.85rem;cursor:pointer;width:100%;text-align:left;']
            ) ?>
            <?= Html::endForm() ?>
        </div>
    </div>

    <div class="main-content">
        <?= $content ?>
    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
