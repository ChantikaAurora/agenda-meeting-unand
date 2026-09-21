<?php

declare(strict_types=1);

namespace app\assets;

use yii\bootstrap5\BootstrapAsset;
use yii\web\AssetBundle;

/**
 * Asset bundle untuk halaman-halaman publik (scan QR, form absensi).
 * Sengaja terpisah dari AppAsset agar tidak ikut menarik dependency admin.
 */
class PublicAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/public.css',
    ];
    public $js = [
        'js/absensi-signature.js',
    ];
    public $depends = [
        BootstrapAsset::class,
    ];
}
