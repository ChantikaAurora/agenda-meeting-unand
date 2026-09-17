<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var string $message */

use yii\helpers\Html;

$this->title = 'Absensi Tidak Dapat Diproses';
?>

<div class="public-card status-card status-error">

    <div class="status-icon-wrap status-icon-error">
        <svg viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.964 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.708c.889 0 1.438-.99.98-1.767zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5m.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
        </svg>
    </div>

    <h1 class="status-title">Absensi Tidak Dapat Diproses</h1>

    <p class="status-message"><?= Html::encode($message) ?></p>

    <p class="status-footnote">
        Jika Anda merasa ini keliru, silakan hubungi panitia atau administrasi rapat.
    </p>

</div>
