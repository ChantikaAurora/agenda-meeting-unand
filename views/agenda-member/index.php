<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\Agenda $agenda */
/** @var app\models\AgendaMember[] $invitations */

use yii\helpers\Html;

$this->title = 'Daftar Undangan';
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

    <div class="card-header">
        <h2>
            Daftar Undangan
            <span style="font-weight:400;color:#999;font-size:0.85rem;">
                &middot; <?= Html::encode($agenda->pembahasan) ?>
            </span>
        </h2>

        <?= Html::a('+ Tambah &amp; Kirim Undangan', ['/agenda-member/compose', 'agenda_id' => $agenda->agenda_id]) ?>
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
