<?php

use app\models\Unit;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var Unit[] $units */
/** @var array $rekap */
/** @var string $tanggalMulai */
/** @var string $tanggalSelesai */
/** @var string $unitId */
/** @var string $statusFilter */
/** @var int $totalAgenda */
/** @var int $rataRataKehadiran */
/** @var int $totalNotulen */

$this->title = 'Laporan & Rekapitulasi Rapat';
?>

<div class="laporan-page">

    <div class="laporan-breadcrumb">
        <?= Html::a('Dashboard', ['/dashboard/index'], ['class' => 'breadcrumb-link']) ?>
        <span>›</span>
        <span class="breadcrumb-current">Laporan</span>
    </div>

    <div class="laporan-header">
        <div class="laporan-header-content">
            <h1>Laporan & Rekapitulasi Rapat</h1>
            <p>Rekapitulasi pelaksanaan rapat dan persentase kehadiran peserta.</p>
        </div>
        <div class="laporan-header-action">
            <?= Html::a('Export Excel', ['export-excel', 'tanggal_mulai' => $tanggalMulai, 'tanggal_selesai' => $tanggalSelesai, 'unit_id' => $unitId, 'status_notulen' => $statusFilter], ['class' => 'btn-export-excel']) ?>
            <?= Html::a('Cetak PDF', ['export-pdf', 'tanggal_mulai' => $tanggalMulai, 'tanggal_selesai' => $tanggalSelesai, 'unit_id' => $unitId, 'status_notulen' => $statusFilter], ['class' => 'btn-cetak-pdf']) ?>
        </div>
    </div>

    <?= Html::beginForm(['index'], 'get', ['class' => 'laporan-filter-card']) ?>

        <div class="filter-item">
            <label>TANGGAL MULAI</label>
            <?= Html::input('date', 'tanggal_mulai', $tanggalMulai, ['class' => 'laporan-select']) ?>
        </div>

        <div class="filter-item">
            <label>TANGGAL SELESAI</label>
            <?= Html::input('date', 'tanggal_selesai', $tanggalSelesai, ['class' => 'laporan-select']) ?>
        </div>

        <div class="filter-item">
            <label>UNIT KERJA</label>
            <?= Html::dropDownList('unit_id', $unitId,
                ['' => 'Semua Unit Kerja'] + ArrayHelper::map($units, 'unit_id', 'nama_unit'),
                ['class' => 'laporan-select']
            ) ?>
        </div>

        <div class="filter-item">
            <label>STATUS</label>
            <?= Html::dropDownList('status_notulen', $statusFilter, [
                '' => 'Semua Status',
                'lengkap' => 'Lengkap',
                'belum' => 'Belum Lengkap',
            ], ['class' => 'laporan-select']) ?>
        </div>

        <div class="filter-button-wrapper">
            <?= Html::submitButton('Terapkan', ['class' => 'btn-terapkan']) ?>
        </div>

    <?= Html::endForm() ?>

    <div class="laporan-stat-grid">

        <div class="laporan-stat-card">
            <div class="stat-icon stat-green">
                <svg viewBox="0 0 24 24"><path d="M7 2v2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-2V2h-2v2H9V2H7zm-2 7h14v11H5V9z"/></svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">TOTAL AGENDA</div>
                <div class="stat-number"><?= $totalAgenda ?> Rapat</div>
            </div>
        </div>

        <div class="laporan-stat-card">
            <div class="stat-icon stat-blue">
                <svg viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">RATA-RATA KEHADIRAN</div>
                <div class="stat-number"><?= $rataRataKehadiran ?>%</div>
            </div>
        </div>

        <div class="laporan-stat-card">
            <div class="stat-icon stat-yellow">
                <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">TOTAL NOTULEN</div>
                <div class="stat-number"><?= $totalNotulen ?> Berkas</div>
            </div>
        </div>

    </div>

    <div class="laporan-table-card">

        <div class="laporan-table-header">
            <div>
                <h2>Rekapitulasi per Unit Kerja</h2>
                <p>Daftar rekapitulasi kehadiran dan notulensi per unit kerja.</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="laporan-table">
                <thead>
                <tr>
                    <th class="col-no">NO</th>
                    <th>PERIODE</th>
                    <th>UNIT/FAKULTAS</th>
                    <th>JUMLAH AGENDA</th>
                    <th>PESERTA HADIR</th>
                    <th>TINGKAT KEHADIRAN</th>
                    <th>STATUS NOTULEN</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($rekap)): ?>
                    <tr>
                        <td colspan="7" class="empty-data">Tidak ada data untuk filter ini.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($rekap as $i => $row): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= Html::encode($row['periode']) ?></td>
                            <td class="pembahasan"><?= Html::encode($row['unit']) ?></td>
                            <td><?= $row['jumlah_agenda'] ?></td>
                            <td><?= $row['peserta_hadir'] ?></td>
                            <td style="color:#1f7a3d; font-weight:600;"><?= $row['tingkat_kehadiran'] ?>%</td>
                            <td>
                                <?php if ($row['status_notulen'] === 'Lengkap'): ?>
                                    <span class="status-badge status-selesai"><?= $row['status_notulen'] ?></span>
                                <?php else: ?>
                                    <span class="status-badge status-terjadwal"><?= $row['status_notulen'] ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

</div>

<?php
$this->registerCss(<<<CSS

.laporan-page { padding: 0; }

.laporan-breadcrumb {
    display: flex; align-items: center; gap: 7px;
    margin-bottom: 16px; font-size: 12px; color: #9ca3af;
}
.laporan-breadcrumb .breadcrumb-link { color: #6b7280; text-decoration: none; }
.laporan-breadcrumb .breadcrumb-current { color: #185c37; font-weight: 600; }

.laporan-header {
    background: #fff; border: 1px solid #eee; border-radius: 12px;
    padding: 22px 20px; margin-bottom: 16px;
    display: flex; align-items: center; justify-content: space-between;
}
.laporan-header-content h1 { margin: 0 0 5px; font-size: 22px; font-weight: 700; color: #111827; }
.laporan-header-content p { margin: 0; font-size: 13px; color: #6b7280; }
.laporan-header-action { display: flex; gap: 8px; }

.btn-cetak-pdf, .btn-export-excel {
    display: inline-flex; align-items: center; justify-content: center;
    height: 34px; padding: 0 15px; border-radius: 7px;
    font-size: 12px; font-weight: 600; text-decoration: none !important;
}
.btn-cetak-pdf { background: #185c37; border: 1px solid #185c37; color: #fff !important; }
.btn-cetak-pdf:hover { background: #12482b; }
.btn-export-excel { background: #fff; border: 1px solid #dfe3e8; color: #374151 !important; }
.btn-export-excel:hover { background: #f8f9fa; }

.laporan-filter-card {
    background: #fff; border: 1px solid #e5e7eb; border-radius: 12px;
    padding: 14px 16px; margin-bottom: 16px;
    display: flex; align-items: flex-end; gap: 12px; flex-wrap: wrap;
}
.filter-item { display: flex; flex-direction: column; gap: 5px; }
.filter-item label { font-size: 10px; font-weight: 700; color: #9ca3af; letter-spacing: .04em; }
.laporan-select {
    width: 180px; height: 34px; border: 1px solid #dfe3e8; border-radius: 7px;
    background: #fff; color: #374151; padding: 0 10px; font-size: 12px;
}
.laporan-select:focus { outline: none; border-color: #185c37; }
.filter-button-wrapper { display: flex; }
.btn-terapkan {
    height: 34px; padding: 0 16px; border: none; border-radius: 7px;
    background: #185c37; color: #fff; font-size: 12px; font-weight: 600; cursor: pointer;
}
.btn-terapkan:hover { background: #12482b; }

.laporan-stat-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; margin-bottom: 16px; }
.laporan-stat-card {
    background: #fff; border: 1px solid #e5e7eb; border-radius: 12px;
    padding: 18px; display: flex; align-items: center; gap: 12px; min-height: 90px;
}
.stat-icon { width: 40px; height: 40px; border-radius: 9px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; }
.stat-icon svg { width: 18px; height: 18px; fill: currentColor; }
.stat-green { background: #e3f5e7; color: #1f7a3d; }
.stat-yellow { background: #fff6d9; color: #a8821a; }
.stat-blue { background: #e4eeff; color: #1a56b0; }
.stat-label { font-size: 9px; font-weight: 700; color: #9ca3af; letter-spacing: .04em; text-transform: uppercase; }
.stat-number { font-size: 21px; line-height: 1.2; font-weight: 700; color: #111827; margin-top: 2px; }

.laporan-table-card { background: #fff; border: 1px solid #dfe3e8; border-radius: 12px; overflow: hidden; }
.laporan-table-header { padding: 18px 18px 14px; border-bottom: 1px solid #eee; }
.laporan-table-header h2 { margin: 0 0 3px; font-size: 14px; font-weight: 700; color: #111827; }
.laporan-table-header p { margin: 0; font-size: 11px; color: #9ca3af; }

.table-responsive { width: 100%; overflow-x: auto; }
.laporan-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.laporan-table thead th {
    background: #f8f9fa; padding: 10px 12px; border-bottom: 1px solid #e5e7eb;
    color: #6b7280; font-size: 9px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .03em; text-align: left; white-space: nowrap;
}
.laporan-table tbody td { padding: 14px 12px; border-bottom: 1px solid #eee; color: #111827; vertical-align: middle; }
.laporan-table tbody tr:last-child td { border-bottom: none; }
.laporan-table tbody tr:hover { background: #fafafa; }
.laporan-table .col-no { width: 50px; }
.laporan-table .pembahasan { font-weight: 500; color: #111827; }

.status-badge { display: inline-flex; align-items: center; justify-content: center; padding: 4px 10px; border-radius: 12px; font-size: 10px; font-weight: 600; white-space: nowrap; }
.status-terjadwal { background: #fff6d9; color: #8a6d00; }
.status-selesai { background: #e3f5e7; color: #1f7a3d; }

.empty-data { text-align: center; padding: 30px !important; color: #9ca3af !important; }

@media (max-width: 800px) {
    .laporan-stat-grid { grid-template-columns: 1fr; }
    .laporan-header { flex-direction: column; align-items: flex-start; gap: 15px; }
    .laporan-select { width: 100%; }
    .filter-item { flex: 1; min-width: 180px; }
}

@media print {
    .sidebar, .topbar, .laporan-breadcrumb, .laporan-header-action, .laporan-filter-card { display: none !important; }
    .main-content { padding: 0 !important; }
}

CSS
);
?>