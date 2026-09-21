<?php

/** @var yii\web\View $this */
/** @var app\models\Agenda[] $agendas */
/** @var int $menitSebelum */

use app\models\Agenda;
use yii\helpers\Html;

$this->title = 'Agenda Meeting Information';
$this->params['meta_description'] = 'Informasi jadwal rapat resmi Universitas Andalas dan presensi digital peserta.';

$hariIndo = [
    'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
    'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu',
];
$bulanIndo = [
    'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret', 'April' => 'April',
    'May' => 'Mei', 'June' => 'Juni', 'July' => 'Juli', 'August' => 'Agustus',
    'September' => 'September', 'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember',
];

$formatTanggal = static function (string $tanggal) use ($hariIndo, $bulanIndo): string {
    $ts = strtotime($tanggal);

    return $hariIndo[date('l', $ts)] . ', ' . date('d', $ts) . ' ' . $bulanIndo[date('F', $ts)] . ' ' . date('Y', $ts);
};

$ikon = [
    'kalender' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M7 2v2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-2V2h-2v2H9V2H7zM5 9h14v11H5V9z"/></svg>',
    'jam' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm1 10.6V6h-2v7.4l5.2 3.1 1-1.7-4.2-2.2z"/></svg>',
    'pin' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a7 7 0 0 0-7 7c0 5.25 7 13 7 13s7-7.75 7-13a7 7 0 0 0-7-7zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5z"/></svg>',
    'qr' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M3 3h8v8H3V3zm2 2v4h4V5H5zm8-2h8v8h-8V3zm2 2v4h4V5h-4zM3 13h8v8H3v-8zm2 2v4h4v-4H5zm10-2h2v2h-2v-2zm4 0h2v2h-2v-2zm-4 4h2v2h-2v-2zm4 0h2v4h-2v-4zm-2 4h2v2h-2v-2z"/></svg>',
    'perisai' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2 4 5v6c0 5 3.4 9.7 8 11 4.6-1.3 8-6 8-11V5l-8-3zm0 6a2 2 0 0 1 2 2c0 .7-.4 1.4-1 1.7V14h-2v-2.3c-.6-.3-1-1-1-1.7a2 2 0 0 1 2-2z"/></svg>',
];
?>

<div class="beranda">

    <div class="text-center mb-4">
        <h1 class="beranda-judul">Agenda Meeting Information</h1>
        <p class="beranda-subjudul">Portal Informasi dan Absensi Rapat Resmi Universitas Andalas</p>
    </div>

    <?php if ($agendas === []): ?>
        <div class="beranda-kosong">
            <p class="mb-1 fw-semibold">Belum ada rapat yang dijadwalkan.</p>
            <p class="mb-0 small">Silakan kembali lagi nanti untuk melihat jadwal rapat terbaru.</p>
        </div>
    <?php endif; ?>

    <?php foreach ($agendas as $agenda): ?>
        <?php
        $status = $agenda->statusSaatIni;
        $sedangBerlangsung = $status === Agenda::STATUS_BERLANGSUNG;

        // Presensi hanya dibuka menjelang dan selama rapat berjalan. QR-nya
        // baru dimunculkan saat itu -- selengkapnya di catatan bawah halaman.
        $absensiTerbuka = $agenda->absensiTerbuka();
        $mulai = $agenda->jadwalMulai;

        $qrUrl = null;
        if ($absensiTerbuka && !empty($agenda->qr_code_path)) {
            $qrFile = Yii::getAlias('@webroot/' . $agenda->qr_code_path);
            if (is_file($qrFile)) {
                $qrUrl = Yii::getAlias('@web/' . $agenda->qr_code_path);
            }
        }
        ?>
        <div class="beranda-kartu">

            <div class="beranda-info">
                <span class="beranda-badge <?= $sedangBerlangsung ? 'is-berlangsung' : 'is-akan-datang' ?>">
                    <?= $sedangBerlangsung ? 'Sedang Berlangsung' : 'Akan Datang' ?>
                </span>

                <h2 class="beranda-nama-rapat"><?= Html::encode($agenda->pembahasan) ?></h2>

                <div class="beranda-meta">
                    <div class="beranda-meta-item">
                        <div class="beranda-meta-label"><?= $ikon['kalender'] ?> Tanggal</div>
                        <div class="beranda-meta-nilai"><?= Html::encode($formatTanggal($agenda->tanggal)) ?></div>
                    </div>
                    <div class="beranda-meta-item">
                        <div class="beranda-meta-label"><?= $ikon['jam'] ?> Waktu</div>
                        <div class="beranda-meta-nilai">
                            <?= Html::encode(substr($agenda->waktu_mulai, 0, 5)) ?> -
                            <?= Html::encode(substr($agenda->waktu_selesai, 0, 5)) ?> WIB
                        </div>
                    </div>
                    <div class="beranda-meta-item">
                        <div class="beranda-meta-label"><?= $ikon['pin'] ?> Tempat</div>
                        <div class="beranda-meta-nilai"><?= Html::encode($agenda->lokasi->lokasi ?? '-') ?></div>
                    </div>
                    <div class="beranda-meta-item">
                        <div class="beranda-meta-label"><?= $ikon['kalender'] ?> Unit</div>
                        <div class="beranda-meta-nilai"><?= Html::encode($agenda->lokasi->unit->nama_unit ?? '-') ?></div>
                    </div>
                </div>

                <?php if (!empty($agenda->deskripsi)): ?>
                    <h3 class="beranda-subjudul-blok">Topik Bahasan</h3>
                    <p class="beranda-deskripsi"><?= nl2br(Html::encode($agenda->deskripsi)) ?></p>
                <?php endif; ?>
            </div>

            <div class="beranda-absensi">
                <h3 class="beranda-absensi-judul">Absensi Digital</h3>

                <?php if ($qrUrl !== null): ?>
                    <div class="beranda-qr-box">
                        <div class="beranda-qr-caption">Presensi Rapat</div>
                        <?= Html::img($qrUrl, ['alt' => 'QR Code presensi rapat', 'class' => 'beranda-qr-img']) ?>
                    </div>
                    <p class="beranda-absensi-teks">
                        Scan QR code di atas dengan perangkat Anda, atau tekan tombol di bawah
                        untuk langsung mengisi daftar hadir.
                    </p>
                    <?= Html::a(
                        $ikon['qr'] . ' <span>Scan QR &amp; Isi Absensi</span>',
                        ['/absensi/scan', 'token' => $agenda->qr_code_value],
                        ['class' => 'beranda-tombol-absensi']
                    ) ?>
                <?php elseif ($absensiTerbuka): ?>
                    <p class="beranda-absensi-teks">
                        QR presensi untuk rapat ini belum tersedia. Silakan hubungi panitia rapat.
                    </p>
                <?php else: ?>
                    <div class="beranda-qr-box beranda-qr-terkunci">
                        <?= $ikon['qr'] ?>
                        <div class="beranda-qr-caption mt-2">Belum dibuka</div>
                    </div>
                    <p class="beranda-absensi-teks">
                        Presensi dibuka <?= (int) $menitSebelum ?> menit sebelum rapat dimulai
                        <?php if ($mulai !== null): ?>
                            (mulai pukul <?= Html::encode($mulai->modify('-' . (int) $menitSebelum . ' minutes')->format('H:i')) ?> WIB)
                        <?php endif; ?>.
                    </p>
                <?php endif; ?>
            </div>

        </div>
    <?php endforeach; ?>

    <div class="beranda-catatan">
        <div class="beranda-catatan-ikon"><?= $ikon['perisai'] ?></div>
        <div>
            <div class="beranda-catatan-judul">Catatan Keamanan</div>
            <p class="beranda-catatan-teks mb-0">
                Data yang Anda isi hanya digunakan untuk keperluan daftar hadir rapat dan
                disimpan sesuai kebijakan privasi institusi. QR presensi hanya aktif pada
                rentang waktu rapat berlangsung.
            </p>
        </div>
    </div>

</div>

<style>
.beranda { max-width: 1040px; margin: 0 auto; }

.beranda-judul { font-size: 2.1rem; font-weight: 800; color: #111827; margin-bottom: 6px; }
.beranda-subjudul { color: #6b7280; font-size: 1rem; margin: 0; }

.beranda-kosong {
    background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
    padding: 40px 24px; text-align: center; color: #6b7280;
}

.beranda-kartu {
    display: grid; grid-template-columns: 1fr 320px; gap: 20px;
    margin-bottom: 20px; align-items: start;
}

.beranda-info, .beranda-absensi {
    background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; padding: 24px;
}

.beranda-badge {
    display: inline-block; padding: 4px 12px; border-radius: 999px;
    font-size: 0.75rem; font-weight: 700; margin-bottom: 10px;
}
.beranda-badge.is-berlangsung { background: #E3F5E7; color: #1f7a3d; }
.beranda-badge.is-akan-datang { background: #E4EEFF; color: #1a56b0; }

.beranda-nama-rapat { font-size: 1.5rem; font-weight: 800; color: #111827; margin: 0 0 18px; }

.beranda-meta {
    display: grid; grid-template-columns: 1fr 1fr; gap: 16px;
    padding-bottom: 18px; border-bottom: 1px solid #f0f0f0; margin-bottom: 18px;
}
.beranda-meta-item { min-width: 0; }
.beranda-meta-label {
    display: flex; align-items: center; gap: 6px;
    font-size: 0.78rem; color: #9ca3af; margin-bottom: 3px;
}
.beranda-meta-label svg { width: 14px; height: 14px; flex-shrink: 0; }
.beranda-meta-nilai { font-size: 0.95rem; font-weight: 600; color: #111827; }

.beranda-subjudul-blok { font-size: 1.05rem; font-weight: 700; color: #111827; margin: 0 0 8px; }
.beranda-deskripsi { color: #4b5563; font-size: 0.95rem; line-height: 1.65; margin: 0; }

.beranda-absensi { text-align: center; }
.beranda-absensi-judul { font-size: 1.05rem; font-weight: 700; color: #111827; margin: 0 0 16px; }

.beranda-qr-box {
    border: 1px dashed #d1d5db; border-radius: 10px;
    padding: 12px; display: inline-block; background: #fff;
}
.beranda-qr-caption { font-size: 0.75rem; font-weight: 600; color: #6b7280; margin-bottom: 8px; }
.beranda-qr-img { width: 180px; height: 180px; display: block; }
.beranda-qr-terkunci { color: #d1d5db; padding: 40px 34px; }
.beranda-qr-terkunci svg { width: 56px; height: 56px; }

.beranda-absensi-teks { font-size: 0.85rem; color: #6b7280; line-height: 1.55; margin: 14px 0 0; }

.beranda-tombol-absensi {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    width: 100%; margin-top: 14px; padding: 11px 16px;
    background: #1f4d2c; color: #fff; border-radius: 8px;
    font-size: 0.9rem; font-weight: 600; text-decoration: none;
}
.beranda-tombol-absensi:hover { background: #17381f; color: #fff; }
.beranda-tombol-absensi svg { width: 17px; height: 17px; }

.beranda-catatan {
    display: flex; gap: 14px; align-items: flex-start;
    background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 14px;
    padding: 18px 20px; margin-top: 8px;
}
.beranda-catatan-ikon svg { width: 22px; height: 22px; color: #6b7280; }
.beranda-catatan-judul { font-weight: 700; color: #111827; margin-bottom: 4px; }
.beranda-catatan-teks { font-size: 0.85rem; color: #6b7280; line-height: 1.6; }

@media (max-width: 860px) {
    .beranda-kartu { grid-template-columns: 1fr; }
    .beranda-judul { font-size: 1.6rem; }
    .beranda-meta { grid-template-columns: 1fr; gap: 12px; }
}
</style>
