<?php

use app\assets\AppAsset;
use yii\helpers\Html;

AppAsset::register($this);
$this->beginPage();
$identity = Yii::$app->user->isGuest ? null : Yii::$app->user->identity;
$initials = $identity ? mb_strtoupper(mb_substr($identity->nama, 0, 1)) : '?';
$currentAction = Yii::$app->controller->action->id;
$icons = [
    'grid' => '<svg viewBox="0 0 24 24"><path d="M4 4h7v7H4V4zm9 0h7v7h-7V4zM4 13h7v7H4v-7zm9 0h7v7h-7v-7z"/></svg>',
    'calendar' => '<svg viewBox="0 0 24 24"><path d="M7 2v2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-2V2h-2v2H9V2H7zM5 9h14v11H5V9z"/></svg>',
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
        :root { --green: #287b45; --green-soft: #e9f5ed; --gold: #c9a227; --bg: #f6f8fa; --ink: #202124; --muted: #737981; }
        * { box-sizing: border-box; }
        body { margin: 0; background: var(--bg); color: var(--ink); font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }
        .nt-topbar { height: 64px; padding: 0 24px; background: #fff; border-bottom: 3px solid var(--gold); display: flex; align-items: center; justify-content: space-between; }
        .nt-brand { display: flex; align-items: center; gap: 10px; color: #1f5d34; font-weight: 700; }
        .nt-brand img { width: 30px; height: 30px; }
        .nt-profile { display: flex; align-items: center; gap: 10px; }
        .nt-topbar-icon { width: 20px; height: 20px; color: #666; }
        .nt-profile-info { text-align: right; line-height: 1.25; font-size: 12px; }
        .nt-profile-info strong { display: block; font-size: 13px; }
        .nt-avatar { width: 36px; height: 36px; display: grid; place-items: center; border-radius: 50%; background: #2b7f48; color: #fff; font-weight: 700; }
        .nt-shell { min-height: calc(100vh - 64px); display: flex; }
        .nt-sidebar { width: 220px; padding: 18px 0; background: #fff; border-right: 1px solid #e5e7eb; display: flex; flex-direction: column; justify-content: space-between; }
        .nt-menu, .nt-menu-bottom { list-style: none; padding: 0; margin: 0; }
        .nt-menu a, .nt-menu-bottom a, .nt-logout { width: 100%; padding: 11px 20px; display: flex; align-items: center; gap: 12px; color: #4e5358; background: none; border: 0; text-decoration: none; font-size: 0.88rem; text-align: left; }
        .nt-menu a svg, .nt-menu-bottom svg, .nt-logout svg { width: 18px; height: 18px; fill: currentColor; }
        .nt-menu a.active { color: var(--green); background: var(--green-soft); border-left: 3px solid var(--green); font-weight: 600; }
        .nt-menu a.disabled { color: #b5b9bd; cursor: default; }
        .nt-menu-bottom { border-top: 1px solid #edf0f2; padding-top: 10px; }
        .nt-logout { color: #c0392b; cursor: pointer; }
        .nt-main { flex: 1; padding: 22px 34px 28px; min-width: 0; }
        .nt-breadcrumb { margin-bottom: 16px; color: #7b8188; font-size: 12px; }
        .nt-breadcrumb a { color: var(--green); text-decoration: none; font-weight: 600; }
        .nt-breadcrumb span { color: var(--green); font-weight: 600; }
        .nt-banner { padding: 21px 16px; margin-bottom: 18px; display: flex; align-items: center; justify-content: space-between; background: #fff; border: 1px solid #d9dee5; border-radius: 8px; }
        .nt-banner h1 { margin: 0 0 4px; font-size: 1.7rem; font-weight: 700; }
        .nt-banner p { margin: 0; color: var(--muted); font-size: 0.9rem; }
        .nt-button { display: inline-flex; align-items: center; gap: 7px; padding: 10px 15px; border-radius: 6px; background: var(--green); color: #fff; text-decoration: none; font-size: 12px; font-weight: 600; }
        .nt-button svg { width: 15px; height: 15px; fill: currentColor; }
        .nt-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 18px; }
        .nt-stats-two { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .nt-stat { min-height: 102px; padding: 16px; display: flex; align-items: center; gap: 13px; background: #fff; border: 1px solid #e1e5e9; border-radius: 8px; }
        .nt-stat-icon { width: 40px; height: 40px; display: grid; place-items: center; border-radius: 7px; }
        .nt-stat-icon svg { width: 19px; height: 19px; fill: currentColor; }
        .nt-stat-icon.red { color: #b4423e; background: #fbe8e7; }
        .nt-stat-icon.green { color: #2c8a4e; background: #dcf5e4; }
        .nt-stat-icon.blue { color: #2865d8; background: #e1eaff; }
        .nt-stat-label { color: #777d84; font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.02em; }
        .nt-stat-value { margin-top: 2px; font-size: 1.5rem; font-weight: 700; }
        .nt-card { background: #fff; border: 1px solid #e1e5e9; border-radius: 8px; overflow: hidden; }
        .nt-card-head { padding: 14px 16px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #edf0f2; }
        .nt-card-head h2 { margin: 0; font-size: 1.05rem; font-weight: 700; }
        .nt-filter { display: flex; gap: 8px; }
        .nt-filter a { padding: 7px 10px; border: 1px solid #d6dce3; border-radius: 5px; color: #626970; text-decoration: none; font-size: 11px; }
        .nt-filter .nt-dashboard-search { min-width: 140px; background: #f8f9fa; text-align: left; }
        .nt-table-wrap { overflow-x: auto; }
        .nt-table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
        .nt-table th { padding: 8px 10px; background: #f8f9fa; color: #70767d; font-size: 0.7rem; text-align: left; text-transform: uppercase; letter-spacing: 0.03em; }
        .nt-table td { padding: 12px 10px; border-top: 1px solid #f0f1f3; vertical-align: middle; }
        .nt-dashboard-table th { text-align: center; }
        .nt-table strong { display: block; margin-bottom: 3px; font-size: 0.85rem; font-weight: 500; }
        .nt-table small { color: #777d84; font-size: 0.75rem; }
        .nt-status { display: inline-block; padding: 5px 9px; border-radius: 15px; font-size: 10px; font-weight: 600; white-space: nowrap; }
        .nt-status.red { color: #a23834; background: #fbe8e7; }
        .nt-status.gray { color: #606872; background: #eef0f2; }
        .nt-status.green { color: #21703d; background: #ddf5e5; }
        .nt-status.blue { color: #2059b2; background: #e4edff; }
        .nt-action { color: #2865d8; text-decoration: none; font-size: 11px; font-weight: 600; white-space: nowrap; }
        .nt-empty { padding: 30px; color: #8b9299; text-align: center; }
        .nt-page-title { margin: 0 0 5px; font-size: 1.7rem; font-weight: 700; }
        .nt-page-description { margin: 0 0 18px; color: var(--muted); font-size: 0.9rem; }
        .nt-filter-card { padding: 12px; margin-bottom: 16px; }
        .nt-filter-form { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .nt-filter-input, .nt-filter-select { height: 36px; border: 1px solid #d7dce2; border-radius: 6px; background: #fff; color: #4e5358; font: inherit; font-size: 0.8rem; }
        .nt-filter-input { width: 270px; padding: 0 12px; }
        .nt-filter-select { min-width: 150px; padding: 0 10px; }
        .nt-filter-button { height: 36px; padding: 0 13px; border: 1px solid #d7dce2; border-radius: 6px; background: #fff; color: #4e5358; cursor: pointer; font: inherit; font-size: 0.8rem; }
        .nt-table-card { overflow: hidden; }
        .nt-table-card .nt-table th:first-child, .nt-table-card .nt-table td:first-child { text-align: center; width: 52px; }
        .nt-table-card .nt-table th:last-child, .nt-table-card .nt-table td:last-child { text-align: center; }
        .nt-action-button { display: inline-flex; align-items: center; justify-content: center; min-width: 88px; padding: 7px 10px; border: 1px solid #287b45; border-radius: 6px; color: #287b45; text-decoration: none; font-size: 0.75rem; font-weight: 600; }
        .nt-action-button svg { width: 14px; height: 14px; margin-right: 5px; fill: currentColor; }
        .nt-action-button.primary { border-color: #2865d8; background: #2865d8; color: #fff; }
        .nt-action-button.muted { border-color: #d7dce2; color: #646b73; }
        .nt-action-icon { display: inline-flex; align-items: center; justify-content: center; width: 27px; height: 27px; margin-left: 4px; border: 1px solid #d7dce2; border-radius: 6px; color: #646b73; text-decoration: none; font-size: 0.8rem; }
        .nt-pagination { display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; color: #737981; font-size: 0.75rem; }
        .nt-pagination-links { display: flex; align-items: center; gap: 4px; }
        .nt-pagination-links a, .nt-pagination-links span { min-width: 27px; height: 27px; display: grid; place-items: center; border: 1px solid #d7dce2; border-radius: 5px; color: #646b73; text-decoration: none; }
        .nt-pagination-links .active { border-color: #2865d8; background: #2865d8; color: #fff; }
        .nt-pagination-links .disabled { color: #b7bdc4; }
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; margin-bottom: 6px; color: #333; font-size: 0.85rem; font-weight: 600; }
        .form-control { width: 100%; border: 1px solid #d7dce2; border-radius: 6px; padding: 10px 12px; background: #fff; color: #202124; font: inherit; font-size: 0.85rem; }
        .form-control:focus { outline: none; border-color: var(--green); box-shadow: 0 0 0 3px rgba(40,123,69,.1); }
        textarea.form-control { resize: vertical; }
        .form-actions { display: flex; align-items: center; gap: 10px; margin-top: 8px; }
        @media (max-width: 760px) { .nt-sidebar { width: 64px; } .nt-menu a span, .nt-menu-bottom span, .nt-logout span { display: none; } .nt-menu a, .nt-menu-bottom a, .nt-logout { justify-content: center; padding: 13px 8px; } .nt-main { padding: 18px 14px; } .nt-stats, .nt-stats-two { grid-template-columns: 1fr; } .nt-banner { align-items: flex-start; gap: 14px; flex-direction: column; } .nt-profile-info { display: none; } .nt-filter-input { width: 100%; } .nt-filter-form > * { flex: 1; } .nt-pagination { align-items: flex-start; gap: 10px; flex-direction: column; } }
    </style>
</head>
<body>
<?php $this->beginBody() ?>
<header class="nt-topbar">
    <div class="nt-brand">
        <?= Html::img('@web/images/logo-unand.png', ['alt' => '', 'onerror' => "this.style.display='none'"]) ?>
        <span>Universitas Andalas</span>
    </div>
    <div class="nt-profile">
        <svg class="nt-topbar-icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 22a2.5 2.5 0 0 0 2.45-2h-4.9A2.5 2.5 0 0 0 12 22zm7-6v-5a7 7 0 0 0-5.5-6.83V3a1.5 1.5 0 0 0-3 0v1.17A7 7 0 0 0 5 11v5l-1.7 1.7A1 1 0 0 0 4 19h16a1 1 0 0 0 .7-1.71L19 16z"/></svg>
        <svg class="nt-topbar-icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M19.4 13a7.9 7.9 0 0 0 0-2l2.1-1.6-2-3.5-2.5 1a8 8 0 0 0-1.7-1L14.9 3h-4l-.4 2.9a8 8 0 0 0-1.7 1l-2.5-1-2 3.5L6.4 11a7.9 7.9 0 0 0 0 2l-2.1 1.6 2 3.5 2.5-1a8 8 0 0 0 1.7 1l.4 2.9h4l.4-2.9a8 8 0 0 0 1.7-1l2.5 1 2-3.5L19.4 13zM12 15.5A3.5 3.5 0 1 1 12 8.5a3.5 3.5 0 0 1 0 7z"/></svg>
        <div class="nt-profile-info"><strong><?= Html::encode($identity->nama ?? 'Notulis') ?></strong><span>Notulis</span></div>
        <div class="nt-avatar"><?= Html::encode($initials) ?></div>
    </div>
</header>
<div class="nt-shell">
    <aside class="nt-sidebar">
        <ul class="nt-menu">
            <li><a class="<?= $currentAction === 'dashboard' ? 'active' : '' ?>" href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/notulis/dashboard'])) ?>"><?= $icons['grid'] ?><span>Dashboard</span></a></li>
            <li><a class="<?= $currentAction === 'index' ? 'active' : '' ?>" href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/notulis/index'])) ?>"><?= $icons['calendar'] ?><span>Kelola Agenda</span></a></li>
        </ul>
        <div class="nt-menu-bottom">
            <a href="#" onclick="return false;"><span>?</span><span>Bantuan</span></a>
            <?= Html::beginForm(['/site/logout'], 'post') ?>
            <?= Html::submitButton('<svg viewBox="0 0 24 24"><path d="M16 13v-2H7V8l-5 4 5 4v-3h9zm3-10H11a2 2 0 0 0-2 2v4h2V5h8v14h-8v-4H9v4a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V5z"/></svg><span>Keluar</span>', ['class' => 'nt-logout']) ?>
            <?= Html::endForm() ?>
        </div>
    </aside>
    <main class="nt-main"><?= $content ?></main>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
