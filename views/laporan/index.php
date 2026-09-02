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
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="laporan-index">

    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h1><?= Html::encode($this->title) ?></h1>
            <p>Rekapitulasi pelaksanaan rapat dan persentase kehadiran peserta.</p>
        </div>
        <div class="d-flex gap-2">
            <?= Html::a('Export Excel', ['export-excel', 'tanggal_mulai' => $tanggalMulai, 'tanggal_selesai' => $tanggalSelesai, 'unit_id' => $unitId, 'status_notulen' => $statusFilter], ['class' => 'btn btn-outline-secondary']) ?>
            <?= Html::a('Cetak PDF', ['export-pdf', 'tanggal_mulai' => $tanggalMulai, 'tanggal_selesai' => $tanggalSelesai, 'unit_id' => $unitId, 'status_notulen' => $statusFilter], ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?= Html::beginForm(['index'], 'get', ['class' => 'row g-2 mb-4']) ?>
        <div class="col-auto">
            <?= Html::input('date', 'tanggal_mulai', $tanggalMulai, ['class' => 'form-control']) ?>
        </div>
        <div class="col-auto">
            <?= Html::input('date', 'tanggal_selesai', $tanggalSelesai, ['class' => 'form-control']) ?>
        </div>
        <div class="col-auto">
            <?= Html::dropDownList('unit_id', $unitId,
                ['' => 'Semua Unit Kerja'] + ArrayHelper::map($units, 'unit_id', 'nama_unit'),
                ['class' => 'form-select']
            ) ?>
        </div>
        <div class="col-auto">
            <?= Html::dropDownList('status_notulen', $statusFilter, [
                '' => 'Semua Status',
                'lengkap' => 'Lengkap',
                'belum' => 'Belum Lengkap',
            ], ['class' => 'form-select']) ?>
        </div>
        <div class="col-auto">
            <?= Html::submitButton('Terapkan', ['class' => 'btn btn-primary']) ?>
        </div>
    <?= Html::endForm() ?>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card p-3 d-flex flex-row align-items-center gap-3" style="border-top: 3px solid #22c55e;">
                <div style="width:46px; height:46px; border-radius:50%; background:#dcfce7; display:flex; align-items:center; justify-content:center; font-size:20px;">📅</div>
                <div>
                    <div class="text-muted small">Total Agenda</div>
                    <div class="fs-3 fw-bold"><?= $totalAgenda ?> Rapat</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 d-flex flex-row align-items-center gap-3" style="border-top: 3px solid #3b82f6;">
                <div style="width:46px; height:46px; border-radius:50%; background:#dbeafe; display:flex; align-items:center; justify-content:center; font-size:20px;">👥</div>
                <div>
                    <div class="text-muted small">Rata-rata Kehadiran</div>
                    <div class="fs-3 fw-bold"><?= $rataRataKehadiran ?>%</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 d-flex flex-row align-items-center gap-3" style="border-top: 3px solid #eab308;">
                <div style="width:46px; height:46px; border-radius:50%; background:#fef9c3; display:flex; align-items:center; justify-content:center; font-size:20px;">📄</div>
                <div>
                    <div class="text-muted small">Total Notulen</div>
                    <div class="fs-3 fw-bold"><?= $totalNotulen ?> Berkas</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header fw-bold">Rekapitulasi per Unit Kerja</div>
        <table class="table mb-0">
            <thead>
            <tr>
                <th>No</th>
                <th>Periode</th>
                <th>Unit/Fakultas</th>
                <th>Jumlah Agenda</th>
                <th>Peserta Hadir</th>
                <th>Tingkat Kehadiran</th>
                <th>Status Notulen</th>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($rekap)): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">Tidak ada data untuk filter ini.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($rekap as $i => $row): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= Html::encode($row['periode']) ?></td>
                        <td><?= Html::encode($row['unit']) ?></td>
                        <td><?= $row['jumlah_agenda'] ?></td>
                        <td><?= $row['peserta_hadir'] ?></td>
                        <td><?= $row['tingkat_kehadiran'] ?>%</td>
                        <td>
                            <?php if ($row['status_notulen'] === 'Lengkap'): ?>
                                <span class="badge" style="background:#dcfce7; color:#166534; font-weight:500;"><?= $row['status_notulen'] ?></span>
                            <?php else: ?>
                                <span class="badge" style="background:#fef9c3; color:#854d0e; font-weight:500;"><?= $row['status_notulen'] ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>