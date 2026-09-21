<?php

/** @var yii\web\View $this */
/** @var app\models\Agenda $model */
/** @var app\models\AgendaMember[] $members */
/** @var string $subject */
/** @var string $body */

use yii\helpers\Html;

$this->title = 'Pratinjau Undangan Rapat';
?>

<div class="breadcrumb">
    <?= Html::a('Dashboard', ['/dashboard/index']) ?>
    &nbsp;›&nbsp;
    <?= Html::a('Kelola Agenda', ['/agenda/index']) ?>
    &nbsp;›&nbsp;
    <?= Html::a('Detail', ['/agenda/view', 'id' => $model->agenda_id]) ?>
    &nbsp;›&nbsp;
    <span class="current">Pratinjau Undangan</span>
</div>

<div class="invitation-preview-layout">
    <section class="agenda-card invitation-preview-card">
        <div class="agenda-section-heading">
            <div>
                <h1>Pratinjau Email Undangan</h1>
                <p>Email belum dikirim. Periksa isi email, lalu klik tombol kirim.</p>
            </div>
        </div>

        <div class="email-preview">
            <div class="email-preview-row">
                <strong>Subjek</strong>
                <span><?= Html::encode($subject) ?></span>
            </div>
            <div class="email-preview-row email-preview-body">
                <strong>Isi Email</strong>
                <pre><?= Html::encode($body) ?></pre>
            </div>
        </div>

        <div class="invitation-preview-actions">
            <?= Html::a('Kembali', ['/agenda/view', 'id' => $model->agenda_id], ['class' => 'btn-preview-cancel']) ?>
            <?= Html::beginForm(['/agenda/send-invitations', 'id' => $model->agenda_id], 'post') ?>
                <?= Html::submitButton('Kirim ke Semua Peserta', [
                    'class' => 'btn-preview-send',
                    'data' => ['confirm' => 'Kirim email ini ke semua peserta yang terdaftar?'],
                ]) ?>
            <?= Html::endForm() ?>
        </div>
    </section>

    <aside class="agenda-card invitation-recipient-card">
        <h2>Penerima (<?= count($members) ?>)</h2>
        <div class="invitation-recipient-list">
            <?php foreach ($members as $agendaMember): ?>
                <div class="invitation-recipient">
                    <strong><?= Html::encode($agendaMember->member->nama) ?></strong>
                    <small><?= Html::encode($agendaMember->member->email) ?></small>
                </div>
            <?php endforeach; ?>
        </div>
        <p class="invitation-recipient-note">Semua penerima di atas akan diproses dalam satu kali pengiriman.</p>
    </aside>
</div>

<style>
    .invitation-preview-layout {
        display: grid;
        grid-template-columns: minmax(0, 1.6fr) minmax(240px, .8fr);
        gap: 16px;
    }

    .invitation-preview-card h1 {
        margin: 0 0 5px;
        color: #111827;
        font-size: 20px;
    }

    .invitation-preview-card p {
        margin: 0;
        color: #6b7280;
        font-size: 12px;
    }

    .email-preview {
        margin-top: 20px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: #fafbfa;
    }

    .email-preview-row {
        display: grid;
        grid-template-columns: 80px 1fr;
        gap: 14px;
        padding: 13px 15px;
        border-bottom: 1px solid #e5e7eb;
        color: #374151;
        font-size: 12px;
    }

    .email-preview-row:last-child {
        border-bottom: 0;
    }

    .email-preview-row strong {
        color: #6b7280;
    }

    .email-preview-body {
        display: block;
    }

    .email-preview-body strong {
        display: block;
        margin-bottom: 10px;
    }

    .email-preview-body pre {
        margin: 0;
        white-space: pre-wrap;
        font: inherit;
        line-height: 1.65;
        color: #111827;
    }

    .invitation-preview-actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        margin-top: 20px;
    }

    .btn-preview-cancel,
    .btn-preview-send {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 34px;
        padding: 0 14px;
        border-radius: 7px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
    }

    .btn-preview-cancel {
        border: 1px solid #dfe3e8;
        color: #374151;
    }

    .btn-preview-send {
        border: 1px solid #247b59;
        background: #247b59;
        color: #fff;
        cursor: pointer;
    }

    .invitation-recipient-card h2 {
        margin: 0 0 14px;
        font-size: 15px;
        color: #111827;
    }

    .invitation-recipient {
        display: flex;
        flex-direction: column;
        gap: 3px;
        padding: 10px 0;
        border-bottom: 1px solid #f0f1f3;
    }

    .invitation-recipient strong {
        color: #111827;
        font-size: 12px;
    }

    .invitation-recipient small,
    .invitation-recipient-note {
        color: #6b7280;
        font-size: 11px;
    }

    .invitation-recipient-note {
        margin: 14px 0 0;
        line-height: 1.5;
    }

    @media (max-width: 800px) {
        .invitation-preview-layout {
            grid-template-columns: 1fr;
        }
    }
</style>