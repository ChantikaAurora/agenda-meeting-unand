<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\PublicAsset;
use app\widgets\Alert;
use yii\helpers\Html;

PublicAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag([
    'name' => 'viewport',
    'content' => 'width=device-width, initial-scale=1',
]);
$this->registerLinkTag([
    'rel' => 'icon',
    'type' => 'image/x-icon',
    'href' => Yii::getAlias('@web/favicon.ico'),
]);

$identity = Yii::$app->user->isGuest ? null : Yii::$app->user->identity;
$dashboardRoute = $identity !== null && $identity->role === 'notulen'
    ? ['/notulis/dashboard']
    : ['/dashboard/index'];

// public.css membatasi .public-container ke 480px -- pas untuk form absensi
// satu kolom yang diisi dari HP, tapi terlalu sempit untuk halaman landing
// dengan beberapa kartu berdampingan. Halaman yang butuh lebar penuh
// mengaktifkan ini lewat $this->params['publicWide'] = true; sebelum render.
$containerClass = ($this->params['publicWide'] ?? false) ? 'public-container-wide' : 'public-container';

$this->beginPage();
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100" data-bs-theme="light">
<head>
    <?php $this->head() ?>
    <title><?= Html::encode($this->title ?: 'Agenda Meeting Information') ?></title>
    <style>
        .public-header-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        .public-brand { display: flex; align-items: center; gap: 12px; min-width: 0; }
        .public-header-actions { display: flex; align-items: center; gap: 12px; flex-shrink: 0; }
        .public-user-name { font-size: 0.85rem; color: #555; }
        .btn-public-login {
            background: #1f4d2c; color: #fff; border: none;
            border-radius: 8px; padding: 9px 18px;
            font-size: 0.85rem; font-weight: 600;
            text-decoration: none; display: inline-flex; align-items: center; gap: 8px;
            cursor: pointer;
        }
        .btn-public-login:hover { background: #17381f; color: #fff; }
        .btn-public-ghost {
            background: #fff; color: #1f4d2c; border: 1px solid #d1d5db;
            border-radius: 8px; padding: 9px 16px;
            font-size: 0.85rem; font-weight: 600;
            text-decoration: none; display: inline-flex; align-items: center;
            cursor: pointer;
        }
        .btn-public-ghost:hover { border-color: #1f4d2c; color: #1f4d2c; }
        @media (max-width: 575px) {
            .public-header-title { font-size: 1rem; }
            .public-user-name { display: none; }
        }
    </style>
</head>
<body class="d-flex flex-column h-100 bg-public">
<?php $this->beginBody() ?>

<header class="public-header">
    <div class="public-header-inner">
        <?= Html::a(
            Html::img('@web/images/logo-unand.png', [
                'alt' => 'Logo Universitas Andalas',
                'class' => 'public-header-logo',
            ]) . '<span class="public-header-title">Universitas Andalas</span>',
            ['/site/index'],
            ['class' => 'public-brand text-decoration-none']
        ) ?>

        <div class="public-header-actions">
            <?php if ($identity === null): ?>
                <?php // Satu-satunya pintu masuk ke sistem internal dari halaman publik. ?>
                <?= Html::a('Login', ['/site/login'], ['class' => 'btn-public-login']) ?>
            <?php else: ?>
                <span class="public-user-name"><?= Html::encode($identity->nama) ?></span>
                <?= Html::a('Dashboard', $dashboardRoute, ['class' => 'btn-public-ghost']) ?>
                <?php // Logout wajib POST (lihat VerbFilter di SiteController) agar tidak bisa dipicu lewat tautan/gambar dari situs lain. ?>
                <?= Html::beginForm(['/site/logout'], 'post', ['class' => 'm-0']) ?>
                <?= Html::submitButton('Keluar', ['class' => 'btn-public-ghost']) ?>
                <?= Html::endForm() ?>
            <?php endif; ?>
        </div>
    </div>
</header>

<main class="flex-grow-1 py-4">
    <div class="container <?= $containerClass ?>">
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<footer class="border-top mt-4 py-3">
    <div class="container d-flex flex-wrap justify-content-between gap-2 small text-secondary">
        <span>&copy; <?= date('Y') ?> Universitas Andalas. Sistem Penjadwalan Agenda Meeting.</span>
        <span><?= Html::a('Kontak', ['/site/contact'], ['class' => 'text-secondary text-decoration-none']) ?></span>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
