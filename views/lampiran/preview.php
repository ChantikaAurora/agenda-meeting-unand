<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Agenda $agenda */
/** @var app\models\Lampiran $model */
/** @var string $fileUrl */
/** @var string $extension */
/** @var string|null $previewHtml */

$this->title = 'Isi Notulen';
?>
<div class="nt-breadcrumb">
    <a href="<?= Html::encode(Url::to(['/notulis/dashboard'])) ?>">Dashboard</a>
    &nbsp;›&nbsp;
    <a href="<?= Html::encode(Url::to(['/notulis/index'])) ?>">Daftar Agenda</a>
    &nbsp;›&nbsp; <span>Isi Notulen</span>
</div>

<h1 class="nt-page-title">Isi Notulen</h1>
<p class="nt-page-description"><?= Html::encode($agenda->pembahasan) ?></p>

<div class="nt-card" style="max-width: 900px; padding: 20px;">
    <?php if ($extension === 'pdf'): ?>
        <div style="padding: 36px 24px; border: 1px solid #d9dee5; border-radius: 8px; background: #f8fafc; text-align: center;">
            <div style="margin-bottom: 10px; font-size: 1.05rem; font-weight: 600; color: #1f2937;">Dokumen PDF siap dibuka</div>
            <div style="margin-bottom: 20px; color: #64748b; font-size: .86rem;">Dokumen tetap dibuka di dalam halaman aplikasi.</div>
            <div class="form-actions" style="justify-content: center; margin-top: 0;">
                <?= Html::a('Buka PDF', ['/lampiran/document', 'agenda_id' => $agenda->agenda_id, 'notulen' => 1], ['class' => 'nt-action-button primary']) ?>
                <?= Html::a('Kembali', ['/lampiran/index', 'agenda_id' => $agenda->agenda_id, 'notulen' => 1], ['class' => 'nt-action-button muted']) ?>
            </div>
        </div>
    <?php elseif ($extension === 'docx' && $previewHtml !== null): ?>
        <div style="padding: 24px; overflow-x: auto; border: 1px solid #e1e5e9; border-radius: 6px; background: #fff;">
            <?= $previewHtml ?>
        </div>
    <?php else: ?>
        <div class="nt-empty">Format <?= Html::encode(strtoupper($extension)) ?> belum dapat ditampilkan langsung. Silakan download berkas untuk membacanya.</div>
    <?php endif; ?>

    <div class="form-actions" style="margin-top: 18px;">
        <?= Html::a('Download Berkas', ['/lampiran/download', 'agenda_id' => $agenda->agenda_id], ['class' => 'nt-action-button primary']) ?>
        <?= Html::a('Edit Notulen', ['/lampiran/update', 'agenda_id' => $agenda->agenda_id, 'notulen' => 1], ['class' => 'nt-action-button']) ?>
        <?= Html::a('Kembali ke Daftar Agenda', ['/notulis/index'], ['class' => 'nt-action-button muted']) ?>
    </div>
</div>