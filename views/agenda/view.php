<?php

/** @var yii\web\View $this */
/** @var app\models\Agenda $model */

use yii\helpers\Html;

$this->title = $model->pembahasan;

/** @var app\models\User $identity */
$identity = Yii::$app->user->identity;
$canManage = $identity->can('manageAgenda');

$statusMap = [
    'terjadwal' => ['class' => 'badge-terjadwal', 'label' => 'Terjadwal'],
    'berlangsung' => ['class' => 'badge-berlangsung', 'label' => 'Berlangsung'],
    'selesai' => ['class' => 'badge-selesai', 'label' => 'Selesai'],
    'dibatalkan' => ['class' => 'badge-dibatalkan', 'label' => 'Dibatalkan'],
];
$status = $statusMap[$model->status] ?? ['class' => '', 'label' => Html::encode($model->status)];
$unitName = $model->lokasi->unit->nama_unit ?? '-';
?>
<div class="breadcrumb">
    <a href="<?= Yii::$app->homeUrl ?>">Dashboard</a> &nbsp;›&nbsp;
    <?= Html::a('Kelola Agenda', ['/agenda/index']) ?> &nbsp;›&nbsp;
    <span class="current">Detail</span>
</div>

<div class="dash-banner">
    <div>
        <h1><?= Html::encode($model->pembahasan) ?></h1>
        <p>
            <span class="badge-status <?= $status['class'] ?>"><?= $status['label'] ?></span>
            <?php if (!empty($model->nomor_surat)): ?>
                &nbsp;&middot;&nbsp; <?= Html::encode($model->nomor_surat) ?>
            <?php endif; ?>
        </p>
    </div>
    <?php if ($canManage): ?>
        <div class="form-actions" style="margin-top:0;">
            <?= Html::a('Ubah', ['update', 'id' => $model->agenda_id], ['class' => 'btn-primary-sm']) ?>
            <?= Html::a('Hapus', ['delete', 'id' => $model->agenda_id], [
                'class' => 'btn-secondary-sm',
                'style' => 'color:#c0392b;',
                'data' => [
                    'confirm' => 'Yakin ingin menghapus agenda ini?',
                    'method' => 'post',
                ],
            ]) ?>
        </div>
    <?php endif; ?>
</div>

<div class="dash-layout">
    <div class="card">
        <div class="detail-grid">
            <div class="detail-item">
                <div class="detail-label">Tanggal</div>
                <div class="detail-value"><?= Yii::$app->formatter->asDate($model->tanggal, 'php:d F Y') ?></div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Waktu</div>
                <div class="detail-value"><?= substr($model->waktu_mulai, 0, 5) ?> - <?= substr($model->waktu_selesai, 0, 5) ?> WIB</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Lokasi</div>
                <div class="detail-value"><?= Html::encode($model->lokasi->lokasi ?? '-') ?></div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Unit</div>
                <div class="detail-value"><?= Html::encode($unitName) ?></div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Tahun Akademik</div>
                <div class="detail-value"><?= Html::encode($model->tahun_akademik) ?></div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Dibuat Pada</div>
                <div class="detail-value"><?= Yii::$app->formatter->asDatetime($model->created_at, 'php:d M Y H:i') ?> WIB</div>
            </div>
        </div>

        <div class="detail-full">
            <div class="detail-label">Deskripsi</div>
            <div class="detail-value">
                <?php if (empty($model->deskripsi)): ?>
                    <span style="color:#999;">Tidak ada deskripsi.</span>
                <?php else: ?>
                    <?php /* Html::encode dulu baru nl2br -- urutan ini wajib, supaya baris baru
                             tetap rapi jadi <br> tanpa membuka celah XSS dari isi deskripsi. */ ?>
                    <?= nl2br(Html::encode($model->deskripsi)) ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>QR Code Absensi</h2>
        </div>

        <?php
        $qrFileExists = !empty($model->qr_code_path)
            && is_file(Yii::getAlias('@webroot/' . $model->qr_code_path));
        ?>

        <?php if ($qrFileExists): ?>
            <div class="qr-display">
                <?= Html::img('@web/' . $model->qr_code_path, ['alt' => 'QR Code Absensi', 'class' => 'qr-image']) ?>
                <div class="qr-actions">
                    <?= Html::a('Download QR', Yii::getAlias('@web/' . $model->qr_code_path), [
                        'class' => 'btn-secondary-sm', 'target' => '_blank', 'download' => true,
                    ]) ?>
                </div>
                <?php if ($canManage): ?>
                    <?php echo Html::beginForm(['generate-qr', 'id' => $model->agenda_id], 'post'); ?>
                        <?= Html::submitButton('Generate Ulang', ['class' => 'btn-secondary-sm', 'style' => 'margin-top:8px;']) ?>
                    <?php echo Html::endForm(); ?>
                <?php endif; ?>
            </div>
        <?php elseif ($canManage): ?>
            <p style="color:#999;font-size:0.85rem;margin-bottom:12px;">QR Code belum dibuat untuk agenda ini.</p>
            <?php echo Html::beginForm(['generate-qr', 'id' => $model->agenda_id], 'post'); ?>
                <?= Html::submitButton('+ Generate QR Code', ['class' => 'btn-primary-sm']) ?>
            <?php echo Html::endForm(); ?>
        <?php else: ?>
            <p style="color:#999;font-size:0.85rem;">QR Code belum dibuat oleh administrasi.</p>
        <?php endif; ?>
    </div>
</div>
