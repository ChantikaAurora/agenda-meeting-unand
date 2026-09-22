<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\Agenda $agenda */
/** @var app\models\AgendaMember[] $invitations */

use yii\helpers\Html;

$this->title = 'Daftar Undangan';
$statusMeta = [
    'terjadwal' => ['class' => 'badge-terjadwal', 'label' => 'Akan Datang'],
    'berlangsung' => ['class' => 'badge-berlangsung', 'label' => 'Sedang Berlangsung'],
    'selesai' => ['class' => 'badge-selesai', 'label' => 'Selesai'],
    'dibatalkan' => ['class' => 'badge-dibatalkan', 'label' => 'Dibatalkan'],
];
$agendaStatus = $statusMeta[$agenda->statusSaatIni] ?? ['class' => '', 'label' => $agenda->statusSaatIni];
$statusCounts = [
    'terkirim' => 0,
    'belum_terkirim' => 0,
    'gagal' => 0,
];
foreach ($invitations as $invitation) {
    if (isset($statusCounts[$invitation->email_status])) {
        $statusCounts[$invitation->email_status]++;
    }
}
?>

<div class="breadcrumb">
    <?= Html::a('Dashboard', ['/dashboard/index']) ?>
    &nbsp;›&nbsp;
    <?= Html::a('Kelola Agenda', ['/agenda/index']) ?>
    &nbsp;›&nbsp;
    <?= Html::a('Detail', ['/agenda/view', 'id' => $agenda->agenda_id]) ?>
    &nbsp;›&nbsp;
    <span class="current">Daftar Undangan</span>
</div>

<div class="card">

    <div class="invitation-agenda-status-bar">
        <span class="badge-status <?= $agendaStatus['class'] ?>"><?= Html::encode($agendaStatus['label']) ?></span>
        <span><?= Html::encode($agenda->nomor_surat ?: 'Agenda Rapat') ?></span>
        <span>&middot;</span>
        <span><?= Html::encode(Yii::$app->formatter->asDate($agenda->tanggal, 'php:d M Y')) ?></span>
        <span>&middot;</span>
        <span><?= Html::encode($agenda->lokasi->lokasi ?? '-') ?></span>
    </div>

    <div class="card-header invitation-header">
        <h2>
            Daftar Undangan
            <span style="font-weight:400;color:#999;font-size:0.85rem;">
                &middot; <?= Html::encode($agenda->pembahasan) ?>
            </span>
        </h2>

        <?= Html::a('+ Tambah &amp; Kirim Undangan', ['/agenda-member/compose', 'agenda_id' => $agenda->agenda_id]) ?>
    </div>

    <div class="invitation-status-summary">
        <div class="invitation-status-item is-sent">
            <strong><?= $statusCounts['terkirim'] ?></strong>
            <span>Terkirim</span>
        </div>
        <div class="invitation-status-item is-pending">
            <strong><?= $statusCounts['belum_terkirim'] ?></strong>
            <span>Menunggu dikirim</span>
        </div>
        <div class="invitation-status-item is-failed">
            <strong><?= $statusCounts['gagal'] ?></strong>
            <span>Gagal</span>
        </div>
    </div>

    <table class="table-clean">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Peran</th>
                <th>Email</th>
                <th>Status Undangan</th>
                <th>Waktu Kirim</th>
                <th style="text-align:center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($invitations as $invitation): ?>
                <?php $member = $invitation->member; ?>
                <?php if ($member === null) continue; ?>

                <tr>
                    <td><?= Html::encode($member->nama) ?></td>
                    <td><?= Html::encode($invitation->displayPeran()) ?></td>
                    <td><?= Html::encode($member->email ?: '-') ?></td>
                    <td>
                        <span class="badge-status" style="<?= $invitation->emailStatusBadgeStyle() ?>">
                            <?= Html::encode($invitation->displayEmailStatus()) ?>
                        </span>
                    </td>
                    <td>
                        <?= $invitation->email_sent_at
                            ? Html::encode(Yii::$app->formatter->asDatetime($invitation->email_sent_at, 'php:d M Y H:i'))
                            : '-'
                        ?>
                    </td>
                    <td style="text-align:center;">
                        <?= Html::beginForm(
                            ['/agenda-member/delete', 'id' => $invitation->id],
                            'post',
                            ['style' => 'display:inline;']
                        ) ?>
                            <?= Html::submitButton('Hapus', [
                                'style' => 'background:none;border:none;color:#c0392b;font-size:0.8rem;cursor:pointer;',
                                'data' => ['confirm' => 'Hapus undangan untuk ' . $member->nama . '?'],
                            ]) ?>
                        <?= Html::endForm() ?>
                    </td>
                </tr>

            <?php endforeach; ?>

            <?php if (empty($invitations)): ?>
                <tr>
                    <td colspan="6" class="table-empty">
                        Belum ada undangan untuk agenda ini.
                        <?= Html::a('Tambah &amp; kirim undangan pertama', ['/agenda-member/compose', 'agenda_id' => $agenda->agenda_id]) ?>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</div>

<style>
    .invitation-header { gap: 16px; flex-wrap: wrap; }
    .invitation-agenda-status-bar {
        display: flex; align-items: center; flex-wrap: wrap; gap: 8px;
        padding: 13px 16px; border-bottom: 1px solid #edf0f2;
        color: #737981; font-size: .78rem;
    }
    .invitation-status-summary {
        display: flex; gap: 10px; padding: 14px 16px;
        border-bottom: 1px solid #edf0f2; background: #fbfcfd;
    }
    .invitation-status-item {
        min-width: 120px; padding: 9px 12px; border-radius: 8px;
        border: 1px solid #e5e7eb; background: #fff;
    }
    .invitation-status-item strong { display: block; font-size: 1.05rem; line-height: 1.1; }
    .invitation-status-item span { display: block; margin-top: 3px; font-size: .72rem; font-weight: 600; }
    .invitation-status-item.is-sent { border-color: #b9e5c5; color: #1f7a3d; background: #f2fbf4; }
    .invitation-status-item.is-pending { border-color: #d8dee5; color: #66717c; background: #f8f9fa; }
    .invitation-status-item.is-failed { border-color: #f1c1c1; color: #a12622; background: #fff5f5; }
    .invitation-status-badge {
        display: inline-flex; align-items: center; padding: 5px 9px;
        border-radius: 999px; font-size: .72rem; font-weight: 700; white-space: nowrap;
    }
    @media (max-width: 680px) {
        .invitation-status-summary { flex-wrap: wrap; }
        .invitation-status-item { flex: 1; min-width: 105px; }
    }
</style>
