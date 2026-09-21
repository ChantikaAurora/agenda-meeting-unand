<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Agenda $model */
/** @var bool $forPdf */

$forPdf = $forPdf ?? false;

$this->title = 'Cetak Dokumen Agenda';


/* =========================================================
   QR CODE
   ========================================================= */

$qrValue = (string) ($model->qr_code_value ?? '');

// QR dibaca dari file yang sudah digenerate aplikasi sendiri.
// Sebelumnya gambar diambil dari api.qrserver.com -- itu berarti token presensi
// dikirim ke server pihak ketiga setiap halaman dibuka, dan siapa pun di sana
// bisa memakainya untuk memalsukan kehadiran.
$qrImageUrl = null;
if (!empty($model->qr_code_path)) {
    $qrFile = Yii::getAlias('@webroot/' . $model->qr_code_path);
    if (is_file($qrFile)) {
        // Dompdf tidak punya sesi login, jadi untuk PDF gambarnya ditanam
        // langsung sebagai data URI, bukan lewat URL yang harus diambil ulang.
        $qrImageUrl = $forPdf
            ? 'data:image/png;base64,' . base64_encode((string) file_get_contents($qrFile))
            : Yii::getAlias('@web/' . $model->qr_code_path);
    }
}


/* =========================================================
   HARI INDONESIA
   ========================================================= */

$hariIndo = [
    'Sunday'    => 'Minggu',
    'Monday'    => 'Senin',
    'Tuesday'   => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday'  => 'Kamis',
    'Friday'    => 'Jumat',
    'Saturday'  => 'Sabtu',
];


/* =========================================================
   BULAN INDONESIA
   ========================================================= */

$bulanIndo = [
    'January'   => 'Januari',
    'February'  => 'Februari',
    'March'     => 'Maret',
    'April'     => 'April',
    'May'       => 'Mei',
    'June'      => 'Juni',
    'July'      => 'Juli',
    'August'    => 'Agustus',
    'September' => 'September',
    'October'   => 'Oktober',
    'November'  => 'November',
    'December'  => 'Desember',
];


/* =========================================================
   FORMAT TANGGAL
   ========================================================= */

$tanggal = strtotime($model->tanggal);

$namaHari = $hariIndo[date('l', $tanggal)];
$tanggalAngka = date('d', $tanggal);
$namaBulan = $bulanIndo[date('F', $tanggal)];
$tahun = date('Y', $tanggal);

$tanggalFormatted = $namaHari . ', ' . $tanggalAngka . ' ' . $namaBulan . ' ' . $tahun;


/* =========================================================
   FORMAT WAKTU
   ========================================================= */

$waktuMulai = date('H:i', strtotime($model->waktu_mulai));
$waktuSelesai = date('H:i', strtotime($model->waktu_selesai));

?>

<style>

/* Gaya dokumen SENGAJA dibatasi ke .dokumen-page.
   Sebelumnya aturan ini menargetkan `body`, sehingga font Times New Roman
   ikut menimpa navbar dan sidebar di layout admin. */
.dokumen-page,
.dokumen-page * {
    box-sizing: border-box;
}

.dokumen-page {
    color: #000;
    font-family: "Times New Roman", Times, serif;
    font-size: 16px;
}

.dokumen-page .no-print {
    width: 800px;
    margin: 18px auto 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.no-print a,
.dokumen-page .no-print button {
    font-family: Arial, sans-serif;
    font-size: 13px;
}

.no-print .back-button,
.dokumen-page .no-print .print-button {
    display: inline-flex;
    align-items: center;
    min-height: 34px;
    padding: 0 14px;
    border-radius: 6px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
}

.dokumen-page .aksi-cetak {
    display: inline-flex;
    gap: 10px;
    align-items: center;
}

.dokumen-page .no-print .back-button {
    border: 1px solid #d1d5db;
    background: #ffffff;
    color: #111827;
}

.dokumen-page .no-print .back-button:hover {
    border-color: #247b59;
    color: #247b59;
}

.dokumen-page .no-print .print-button {
    border: 1px solid #1769e0;
    background: #1769e0;
    color: #ffffff;
}

.dokumen-page .dokumen-wrapper {
    width: 800px;
    min-height: 0;
    margin: 0 auto;
    padding: 38px 55px 35px 70px;
    background: #fff;
    border-radius: 4px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
    line-height: 1.5;
}

.dokumen-page .kop-surat {
    position: relative;
    min-height: 95px;
    padding: 0 35px 12px 95px;
    margin-bottom: 20px;
    text-align: center;
    border-bottom: 2px solid #000;
}

.dokumen-page .kop-logo {
    position: absolute;
    left: 0;
    top: -3px;
    width: 78px;
    height: 78px;
    object-fit: contain;
}

.dokumen-page .kop-surat h1 {
    margin: 0;
    padding: 0;
    font-size: 23px;
    font-weight: bold;
    line-height: 1.2;
}

.dokumen-page .kop-alamat {
    margin: 4px 0 2px;
    font-size: 12px;
    line-height: 1.3;
}

.dokumen-page .kop-kontak {
    margin: 2px 0;
    font-size: 11px;
    line-height: 1.3;
}

.dokumen-page .judul-dokumen {
    margin: 18px 0 22px;
    text-align: center;
    font-size: 19px;
    font-weight: bold;
    line-height: 1.35;
    text-decoration: underline;
}

.dokumen-page .detail-container {
    margin-bottom: 18px;
}

.dokumen-page .detail-row {
    display: flex;
    align-items: flex-start;
    margin-bottom: 8px;
    font-size: 16px;
    line-height: 1.45;
}

.dokumen-page .detail-label {
    width: 135px;
    min-width: 135px;
    font-weight: bold;
    color: #000;
}

.dokumen-page .detail-value {
    flex: 1;
    padding-left: 2px;
}

.dokumen-page .section-title {
    margin: 14px 0 6px;
    font-size: 17px;
    font-weight: bold;
    line-height: 1.4;
}

.dokumen-page .isi-dokumen {
    margin: 0;
    font-size: 16px;
    line-height: 1.55;
    text-align: justify;
}

.dokumen-page .pesan-qr {
    margin: 15px 0 10px;
    font-size: 16px;
    line-height: 1.45;
}

.dokumen-page .qr-wrapper {
    display: flex;
    justify-content: center;
    margin-top: 8px;
}

.dokumen-page .qr-box {
    width: 140px;
    padding: 6px;
    border: 1px dashed #999;
    background: #fff;
    text-align: center;
}

.dokumen-page .qr-box img {
    display: block;
    width: 115px !important;
    height: 115px !important;
    margin: 0 auto;
    object-fit: contain;
}

.dokumen-page .qr-title {
    margin: 5px 0 1px;
    font-size: 9px !important;
    font-weight: bold;
    line-height: 1.2;
}

.dokumen-page .qr-id {
    margin: 0;
    font-size: 8px !important;
    color: #666;
    line-height: 1.2;
    word-break: break-word;
}

.dokumen-page .footer-dokumen {
    margin: 24px 0 0;
    text-align: right;
    font-size: 9px !important;
    color: #888;
    line-height: 1.2;
}

@media print {

    @page {
        size: A4;
        margin: 0;
    }

    html,
    body {
        margin: 0 !important;
        padding: 0 !important;
        background: #fff !important;
    }

    .dokumen-page .no-print {
        display: none !important;
    }

    .dokumen-page .dokumen-wrapper {
        width: 100% !important;
        min-height: auto !important;
        margin: 0 !important;
        padding: 3cm 3cm 3cm 4cm !important;
        background: #fff !important;
        border-radius: 0 !important;
        box-shadow: none !important;
    }

    .dokumen-page .kop-surat {
        min-height: 88px;
        padding-left: 90px;
        padding-right: 35px;
        padding-bottom: 10px;
        margin-bottom: 17px;
    }

    .dokumen-page .kop-logo {
        left: 0;
        top: -2px;
        width: 75px;
        height: 75px;
    }

    .dokumen-page .kop-surat h1 {
        font-size: 21px;
    }

    .dokumen-page .kop-alamat {
        font-size: 10px;
    }

    .dokumen-page .kop-kontak {
        font-size: 9px;
    }

    .dokumen-page .judul-dokumen {
        margin: 15px 0 20px;
        font-size: 17px;
    }

    .dokumen-page .detail-container {
        margin-bottom: 16px;
    }

    .dokumen-page .detail-row {
        margin-bottom: 6px;
        font-size: 14px;
        line-height: 1.4;
    }

    .dokumen-page .detail-label {
        width: 125px;
        min-width: 125px;
    }

    .dokumen-page .section-title {
        margin-top: 12px;
        margin-bottom: 5px;
        font-size: 15px;
    }

    .dokumen-page .isi-dokumen {
        font-size: 14px;
        line-height: 1.5;
    }

    .dokumen-page .pesan-qr {
        margin-top: 12px;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .dokumen-page .qr-wrapper {
        margin-top: 6px;
    }

    .dokumen-page .qr-box {
        width: 130px;
        padding: 5px;
    }

    .dokumen-page .qr-box img {
        width: 110px !important;
        height: 110px !important;
    }

    .dokumen-page .qr-title {
        margin-top: 4px;
        font-size: 8px !important;
    }

    .dokumen-page .qr-id {
        font-size: 7px !important;
    }

    .dokumen-page .footer-dokumen {
        margin-top: 20px;
        font-size: 8px !important;
    }

}

</style>


<div class="dokumen-page">

<?php if (!$forPdf): ?>
<div class="no-print">
    <?= Html::a('&larr; Kembali ke Detail Agenda', ['/agenda/view', 'id' => $model->agenda_id], ['class' => 'back-button']) ?>
    <span class="aksi-cetak">
        <?php // Unduh PDF dirender di server, jadi dijamin bebas header/footer browser. ?>

        <button type="button" onclick="window.print()" class="print-button">Cetak</button>
    </span>
</div>
<?php endif; ?>


<div class="dokumen-wrapper">

    <div class="kop-surat">
        <img src="/images/logo-unand.png" alt="Logo Universitas Andalas" class="kop-logo">
        <h1>UNIVERSITAS ANDALAS</h1>
        <div class="kop-alamat">Jl. Universitas Andalas, Limau Manis, Padang 25163</div>
        <div class="kop-kontak">Telp: (0751) 71181 | Email: info@unand.ac.id</div>
    </div>

    <h2 class="judul-dokumen">
        <?= Html::encode(strtoupper($model->pembahasan)) ?>
    </h2>

    <div class="detail-container">

        <div class="detail-row">
            <div class="detail-label">Hari, Tanggal</div>
            <div class="detail-value">: <?= Html::encode($tanggalFormatted) ?></div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Waktu</div>
            <div class="detail-value">
                : <?= Html::encode($waktuMulai) ?> - <?= Html::encode($waktuSelesai) ?> WIB
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Tempat</div>
            <div class="detail-value">
                : <?= Html::encode($model->lokasi->lokasi ?? '-') ?>
            </div>
        </div>

    </div>

    <div class="section-title">Topik Bahasan:</div>

    <div class="isi-dokumen">
        <?= nl2br(Html::encode($model->deskripsi)) ?>
    </div>

    <div class="pesan-qr">
        Untuk presensi kehadiran, jangan lupa scan kode QR di bawah ini yaa!
    </div>

    <div class="qr-wrapper">
        <div class="qr-box">
            <?php if ($qrImageUrl !== null): ?>
                <img src="<?= Html::encode($qrImageUrl) ?>" alt="QR Code Presensi">
                <p class="qr-title">SCAN UNTUK PRESENSI</p>
                <p class="qr-id">ID Rapat: <?= Html::encode($qrValue) ?></p>
            <?php else: ?>
                <p class="qr-title">QR PRESENSI BELUM TERSEDIA</p>
                <p class="qr-id">Buat ulang QR dari halaman detail agenda.</p>
            <?php endif; ?>
        </div>
    </div>

    <p class="footer-dokumen">
        Dicetak pada: <?= date('d M Y H:i') ?> | Sistem Agenda Universitas Andalas
    </p>

</div>

</div>
