<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\Agenda $agenda */
/** @var app\models\Member[] $members */
/** @var array<int,app\models\AgendaMember> $existingByMemberId */
/** @var string $subject */
/** @var string $body */
/** @var int[] $checkedIds */

use app\models\AgendaMember;
use yii\helpers\Html;

$this->title = 'Tambah & Kirim Undangan';
$checkedIds = array_flip($checkedIds);
$statusSaatIni = $agenda->statusSaatIni;
$statusMeta = [
    'terjadwal' => ['class' => 'badge-terjadwal', 'label' => 'Akan Datang'],
    'berlangsung' => ['class' => 'badge-berlangsung', 'label' => 'Sedang Berlangsung'],
    'selesai' => ['class' => 'badge-selesai', 'label' => 'Selesai'],
    'dibatalkan' => ['class' => 'badge-dibatalkan', 'label' => 'Dibatalkan'],
];
$agendaStatus = $statusMeta[$statusSaatIni] ?? ['class' => '', 'label' => $statusSaatIni];
?>

<div class="breadcrumb">
    <?= Html::a('Dashboard', ['/dashboard/index']) ?>
    &nbsp;›&nbsp;
    <?= Html::a('Kelola Agenda', ['/agenda/index']) ?>
    &nbsp;›&nbsp;
    <?= Html::a('Detail', ['/agenda/view', 'id' => $agenda->agenda_id]) ?>
    &nbsp;›&nbsp;
    <span class="current">Tambah &amp; Kirim Undangan</span>
</div>

<h1 class="invite-page-title">Tambah &amp; Kirim Undangan</h1>
<p class="invite-page-subtitle"><?= Html::encode($agenda->pembahasan) ?></p>

<div class="invite-agenda-status-bar">
    <span class="badge-status <?= $agendaStatus['class'] ?>"><?= Html::encode($agendaStatus['label']) ?></span>
    <span class="invite-agenda-status-separator">&middot;</span>
    <span><?= Html::encode($agenda->nomor_surat ?: 'Agenda Rapat') ?></span>
    <span class="invite-agenda-status-separator">&middot;</span>
    <span><?= Html::encode(Yii::$app->formatter->asDate($agenda->tanggal, 'php:d M Y')) ?></span>
    <span class="invite-agenda-status-separator">&middot;</span>
    <span><?= Html::encode(substr($agenda->waktu_mulai, 0, 5)) ?> - <?= Html::encode(substr($agenda->waktu_selesai, 0, 5)) ?> WIB</span>
    <span class="invite-agenda-status-separator">&middot;</span>
    <span><?= Html::encode($agenda->lokasi->lokasi ?? '-') ?></span>
</div>

<?= Html::beginForm(['/agenda-member/compose', 'agenda_id' => $agenda->agenda_id], 'post') ?>

<div class="invite-compose-layout">

    <!-- ==========================================
         KIRI: pilih penerima
         ========================================== -->
    <div class="card invite-recipients-card">

        <div class="card-header">
            <h2>Pilih Penerima <span style="font-weight:400;color:#999;font-size:0.85rem;">
                &middot; <?= count($members) ?> penerima aktif
            </span></h2>
        </div>

        <div class="invite-search-box">
            <input
                type="text"
                id="invite-search"
                placeholder="Cari nama, jabatan, atau instansi..."
                autocomplete="off"
            >
        </div>

        <div class="invite-recipient-list" id="invite-recipient-list">

            <?php foreach ($members as $member): ?>

                <?php
                $existing = $existingByMemberId[(int) $member->member_id] ?? null;
                $isChecked = isset($checkedIds[(int) $member->member_id]);
                $currentPeran = $existing->peran ?? AgendaMember::PERAN_PESERTA;
                $searchHaystack = mb_strtolower(implode(' ', array_filter([
                    $member->nama, $member->jabatan, $member->instansi, $member->email,
                ])));
                ?>

                <label class="invite-recipient-row" data-search="<?= Html::encode($searchHaystack) ?>">

                    <input
                        type="checkbox"
                        name="member_ids[]"
                        value="<?= (int) $member->member_id ?>"
                        <?= $isChecked ? 'checked' : '' ?>
                    >

                    <span class="invite-recipient-avatar">
                        <?= Html::encode(mb_strtoupper(mb_substr($member->nama, 0, 2))) ?>
                    </span>

                    <span class="invite-recipient-info">
                        <strong><?= Html::encode($member->nama) ?></strong>
                        <small>
                            <?= Html::encode($member->email ?: 'Email belum diisi') ?>
                            <?php if ($member->jabatan || $member->instansi): ?>
                                &middot; <?= Html::encode(implode(', ', array_filter([$member->jabatan, $member->instansi]))) ?>
                            <?php endif; ?>
                        </small>
                    </span>

                    <select name="peran[<?= (int) $member->member_id ?>]" class="invite-recipient-peran">
                        <?php foreach (AgendaMember::optsPeran() as $value => $label): ?>
                            <option value="<?= Html::encode($value) ?>" <?= $value === $currentPeran ? 'selected' : '' ?>>
                                <?= Html::encode($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <?php if ($existing !== null): ?>
                        <span class="invite-status-badge" style="<?= $existing->emailStatusBadgeStyle() ?>" title="Status pengiriman undangan">
                            <?= Html::encode($existing->displayEmailStatus()) ?>
                        </span>
                    <?php endif; ?>

                </label>

            <?php endforeach; ?>

            <?php if (empty($members)): ?>
                <p class="invite-empty-note">
                    Belum ada data narasumber. <?= Html::a('Tambah narasumber', ['/member/create']) ?>
                </p>
            <?php endif; ?>

        </div>

    </div>

    <!-- ==========================================
         KANAN: tulis email
         ========================================== -->
    <div class="card invite-compose-card">

        <div class="card-header">
            <h2>Isi Email</h2>
        </div>

        <div class="form-group">
            <label class="form-label">Subjek</label>
            <input type="text" name="subject" class="form-control" value="<?= Html::encode($subject) ?>" required>
        </div>

        <div class="form-group">
            <label class="form-label">Isi Email</label>
            <textarea name="body" class="form-control invite-body-textarea" rows="12" required><?= Html::encode($body) ?></textarea>
            <p class="invite-body-hint">
                Klik nama penerima untuk mengisi sapaan otomatis. Untuk beberapa penerima,
                gunakan <code>{nama}</code> agar nama masing-masing tetap dipakai saat email dikirim.
            </p>
        </div>

        <div class="invite-compose-actions">
            <?= Html::a('Batal', ['/agenda/view', 'id' => $agenda->agenda_id], ['class' => 'btn-secondary-sm']) ?>
            <button type="submit" class="btn-primary-sm" id="invite-send-btn">
                Kirim Email
            </button>
        </div>

        <p class="invite-recipient-counter">
            <span id="invite-selected-count">0</span> penerima dipilih
        </p>

    </div>

</div>

<?= Html::endForm() ?>

<style>
    .invite-page-title { margin: 0 0 4px; font-size: 1.5rem; font-weight: 700; }
    .invite-page-subtitle { margin: 0 0 20px; color: #777; font-size: 0.9rem; }
    .invite-agenda-status-bar {
        display: flex; align-items: center; flex-wrap: wrap; gap: 7px;
        margin: -8px 0 20px; color: #737981; font-size: 0.78rem;
    }
    .invite-agenda-status-separator { color: #b5bbc1; }

    .invite-compose-layout {
        display: grid;
        grid-template-columns: 1.1fr 1fr;
        gap: 20px;
        align-items: start;
    }
    @media (max-width: 900px) {
        .invite-compose-layout { grid-template-columns: 1fr; }
    }

    .invite-search-box { margin-bottom: 12px; }
    .invite-search-box input {
        width: 100%;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 9px 12px;
        font-size: 0.85rem;
        box-sizing: border-box;
    }

    .invite-recipient-list {
        max-height: 480px;
        overflow-y: auto;
        border: 1px solid #f0f0f0;
        border-radius: 10px;
    }

    .invite-recipient-row {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        border-bottom: 1px solid #f3f3f3;
        cursor: pointer;
    }
    .invite-recipient-row:last-child { border-bottom: none; }
    .invite-recipient-row:hover { background: #fafafa; }
    .invite-recipient-row.is-hidden { display: none; }

    .invite-recipient-row input[type="checkbox"] {
        width: 16px;
        height: 16px;
        flex-shrink: 0;
    }

    .invite-recipient-avatar {
        width: 32px; height: 32px; border-radius: 50%;
        background: #1f4d2c; color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 0.75rem; flex-shrink: 0;
    }

    .invite-recipient-info { flex: 1; min-width: 0; display: flex; flex-direction: column; }
    .invite-recipient-info strong { font-size: 0.85rem; }
    .invite-recipient-info small { color: #999; font-size: 0.72rem; }

    .invite-recipient-peran {
        font-size: 0.75rem;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        padding: 4px 6px;
        flex-shrink: 0;
    }

    .invite-status-badge {
        font-size: 0.7rem;
        font-weight: 600;
        padding: 4px 8px;
        border-radius: 999px;
        flex-shrink: 0;
        white-space: nowrap;
    }

    .invite-empty-note { padding: 16px; color: #999; font-size: 0.85rem; }

    .invite-compose-card .form-group { margin-bottom: 16px; }
    .invite-body-textarea { font-family: inherit; resize: vertical; }
    .invite-body-textarea code { background: #f1f2f4; padding: 1px 5px; border-radius: 4px; }
    .invite-body-hint { margin-top: 6px; color: #999; font-size: 0.75rem; }

    .invite-compose-actions {
        display: flex;
        gap: 10px;
        margin-top: 8px;
    }

    .invite-recipient-counter {
        margin: 10px 0 0;
        font-size: 0.78rem;
        color: #999;
    }
</style>

<script>
(function () {
    var searchInput = document.getElementById('invite-search');
    var rows = document.querySelectorAll('.invite-recipient-row');
    var counter = document.getElementById('invite-selected-count');
    var bodyTextarea = document.querySelector('textarea[name="body"]');

    function updateGreeting() {
        if (!bodyTextarea) return;

        var selectedNames = [];
        rows.forEach(function (row) {
            var box = row.querySelector('input[type="checkbox"]');
            var name = row.querySelector('.invite-recipient-info strong');
            if (box && box.checked && name) selectedNames.push(name.textContent.trim());
        });

        var greeting = selectedNames.length === 1
            ? 'Yth. ' + selectedNames[0] + ','
            : 'Yth. {nama},';
        var lines = bodyTextarea.value.split(/\r?\n/);

        if (lines.length > 0 && /^Yth\.\s.*,$/.test(lines[0].trim())) {
            lines[0] = greeting;
            bodyTextarea.value = lines.join('\n');
        }
    }

    function updateCounter() {
        var checked = 0;
        rows.forEach(function (row) {
            var box = row.querySelector('input[type="checkbox"]');
            if (box && box.checked) checked++;
        });
        counter.textContent = String(checked);
    }

    rows.forEach(function (row) {
        var box = row.querySelector('input[type="checkbox"]');
        if (box) box.addEventListener('change', function () {
            updateCounter();
            updateGreeting();
        });
    });
    updateCounter();
    updateGreeting();

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            var q = searchInput.value.trim().toLowerCase();
            rows.forEach(function (row) {
                var haystack = row.getAttribute('data-search') || '';
                row.classList.toggle('is-hidden', q !== '' && haystack.indexOf(q) === -1);
            });
        });
    }
})();
</script>
