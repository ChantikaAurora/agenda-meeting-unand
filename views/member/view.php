<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Member $model */

$this->title = $model->nama;
$this->params['breadcrumbs'][] = ['label' => 'Kelola Peserta', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$inisial = strtoupper(mb_substr($model->nama, 0, 1));
$statusBadge = $model->is_active
    ? '<span style="background:#dcfce7; color:#166534; padding:4px 12px; border-radius:12px; font-size:12px;">Aktif</span>'
    : '<span style="background:#f1f5f9; color:#475569; padding:4px 12px; border-radius:12px; font-size:12px;">Non-Aktif</span>';
?>
<div class="member-view">

    <p>
        <?= Html::a('&larr; Kembali ke Daftar', ['index'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
    </p>

    <div style="background:white; border-radius:12px; box-shadow:0 1px 4px rgba(0,0,0,0.08); padding:28px; max-width:640px;">

        <div style="display:flex; align-items:center; gap:16px; margin-bottom:20px; padding-bottom:20px; border-bottom:1px solid #eee;">
            <div style="width:56px; height:56px; border-radius:50%; background:#0f6e56; color:white; display:flex; align-items:center; justify-content:center; font-size:22px; font-weight:600; flex-shrink:0;">
                <?= Html::encode($inisial) ?>
            </div>
            <div>
                <h2 style="margin:0; font-size:20px;"><?= Html::encode($model->nama) ?></h2>
                <p style="margin:2px 0 0; color:#666; font-size:14px;">
                    <?= Html::encode($model->jabatan ?? '-') ?>
                    <?php if ($model->instansi): ?>
                        &middot; <?= Html::encode($model->instansi) ?>
                    <?php endif; ?>
                </p>
            </div>
            <div style="margin-left:auto;">
                <?= $statusBadge ?>
            </div>
        </div>

        <table style="width:100%; font-size:14px; border-collapse:collapse;">
            <tr>
                <td style="padding:8px 0; width:160px; color:#888;">Tipe Identitas</td>
                <td style="padding:8px 0;"><?= Html::encode($model->tipe_identitas ?? '-') ?></td>
            </tr>
            <tr>
                <td style="padding:8px 0; color:#888;">Nomor Identitas</td>
                <td style="padding:8px 0;"><?= Html::encode($model->identitas_number ?? '-') ?></td>
            </tr>
            <tr>
                <td style="padding:8px 0; color:#888;">Email</td>
                <td style="padding:8px 0;">
                    <?= $model->email ? Html::mailto(Html::encode($model->email)) : '-' ?>
                </td>
            </tr>
            <tr>
                <td style="padding:8px 0; color:#888;">Terdaftar Sejak</td>
                <td style="padding:8px 0;"><?= date('d M Y H:i', strtotime($model->created_at)) ?></td>
            </tr>
        </table>

        <div style="margin-top:24px; padding-top:16px; border-top:1px solid #eee; display:flex; gap:8px;">
            <?= Html::a('Edit', ['update', 'member_id' => $model->member_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Hapus', ['delete', 'member_id' => $model->member_id], [
                'class' => 'btn btn-outline-danger',
                'data' => [
                    'confirm' => 'Yakin ingin menghapus data ' . Html::encode($model->nama) . '?',
                    'method' => 'post',
                ],
            ]) ?>
        </div>

    </div>

</div>