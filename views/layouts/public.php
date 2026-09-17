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
    'content' => 'width=device-width, initial-scale=1, maximum-scale=1',
]);
$this->registerLinkTag([
    'rel' => 'icon',
    'type' => 'image/x-icon',
    'href' => Yii::getAlias('@web/favicon.ico'),
]);

$this->beginPage();
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100" data-bs-theme="light">
<head>
    <?php $this->head() ?>
    <title><?= Html::encode($this->title ?: 'Absensi Digital') ?></title>
</head>
<body class="d-flex flex-column h-100 bg-public">
<?php $this->beginBody() ?>

<header class="public-header">
    <div class="public-header-inner">
        <?= Html::img('@web/images/logo-unand.png', [
            'alt' => 'Logo Universitas Andalas',
            'class' => 'public-header-logo',
        ]) ?>
        <span class="public-header-title">Universitas Andalas</span>
    </div>
</header>

<main class="flex-grow-1 py-4">
    <div class="container public-container">
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<footer class="text-center text-secondary small pb-4">
    &copy; <?= date('Y') ?> Sistem Penjadwalan Agenda Meeting
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
