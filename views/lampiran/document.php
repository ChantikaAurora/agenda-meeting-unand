<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Agenda $agenda */
/** @var app\models\Lampiran $model */
/** @var string $fileUrl */

$this->title = 'Dokumen Notulen';
?>
<div class="nt-breadcrumb">
    <a href="<?= Html::encode(Url::to(['/notulis/dashboard'])) ?>">Dashboard</a>
    &nbsp;›&nbsp;
    <a href="<?= Html::encode(Url::to(['/notulis/index'])) ?>">Daftar Agenda</a>
    &nbsp;›&nbsp; <span>Dokumen Notulen</span>
</div>

<div style="display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:18px; flex-wrap:wrap;">
    <div>
        <h1 class="nt-page-title" style="margin-bottom:5px;">Dokumen Notulen</h1>
        <p class="nt-page-description" style="margin-bottom:0;"><?= Html::encode($agenda->pembahasan) ?></p>
    </div>
    <div class="form-actions" style="margin-top:0;">
        <?= Html::a('Kembali', ['/lampiran/preview', 'agenda_id' => $agenda->agenda_id, 'notulen' => 1], ['class' => 'nt-action-button muted']) ?>
        <?= Html::a('Download Berkas', ['/lampiran/download', 'agenda_id' => $agenda->agenda_id], ['class' => 'nt-action-button primary']) ?>
    </div>
</div>

<div class="nt-card" style="padding:12px;">
    <div style="padding:10px 14px; margin-bottom:12px; border:1px solid #d9dee5; border-radius:6px; background:#f8fafc; color:#475569; font-size:.84rem;">
        <?= Html::encode($model->original_name ?: basename($model->file_path)) ?>
    </div>
    <iframe src="<?= Html::encode($fileUrl . '#page=1&zoom=page-width') ?>" title="Dokumen notulen" style="display:block; width:100%; height:calc(100vh - 250px); min-height:650px; border:1px solid #d9dee5; border-radius:6px; background:#fff;"></iframe>
</div>
