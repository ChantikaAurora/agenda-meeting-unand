<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Agenda $model */

$this->title = 'Cetak Dokumen Agenda';


/* =========================================================
   QR CODE
   ========================================================= */

$qrValue = !empty($model->qr_code_value)
    ? $model->qr_code_value
    : $model->agenda_id;

$qrImageUrl =
    'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data='
    . urlencode($qrValue);


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

* {
    box-sizing: border-box;
}

html,
body {
    margin: 0;
    padding: 0;
}

body {
    background: #f3f4f6;
    color: #000;
    font-family: "Times New Roman", Times, serif;
    font-size: 16px;
}

.no-print {
    width: 800px;
    margin: 18px auto 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.no-print a,
.no-print button {
    font-family: Arial, sans-serif;
    font-size: 13px;
}

.dokumen-wrapper {
    width: 800px;
    min-height: 1120px;
    margin: 0 auto;
    padding: 45px 55px 45px 70px;
    background: #fff;
    border-radius: 4px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
    line-height: 1.5;
}

.kop-surat {
    position: relative;
    min-height: 95px;
    padding: 0 35px 12px 95px;
    margin-bottom: 20px;
    text-align: center;
    border-bottom: 2px solid #000;
}

.kop-logo {
    position: absolute;
    left: 0;
    top: -3px;
    width: 78px;
    height: 78px;
    object-fit: contain;
}

.kop-surat h1 {
    margin: 0;
    padding: 0;
    font-size: 23px;
    font-weight: bold;
    line-height: 1.2;
}

.kop-alamat {
    margin: 4px 0 2px;
    font-size: 12px;
    line-height: 1.3;
}

.kop-kontak {
    margin: 2px 0;
    font-size: 11px;
    line-height: 1.3;
}

.judul-dokumen {
    margin: 18px 0 22px;
    text-align: center;
    font-size: 19px;
    font-weight: bold;
    line-height: 1.35;
    text-decoration: underline;
}

.detail-container {
    margin-bottom: 18px;
}

.detail-row {
    display: flex;
    align-items: flex-start;
    margin-bottom: 8px;
    font-size: 16px;
    line-height: 1.45;
}

.detail-label {
    width: 135px;
    min-width: 135px;
    font-weight: bold;
}

.detail-value {
    flex: 1;
    padding-left: 2px;
}

.section-title {
    margin: 14px 0 6px;
    font-size: 17px;
    font-weight: bold;
    line-height: 1.4;
}

.isi-dokumen {
    margin: 0;
    font-size: 16px;
    line-height: 1.55;
    text-align: justify;
}

.pesan-qr {
    margin: 15px 0 10px;
    font-size: 16px;
    line-height: 1.45;
}

.qr-wrapper {
    display: flex;
    justify-content: center;
    margin-top: 8px;
}

.qr-box {
    width: 140px;
    padding: 6px;
    border: 1px dashed #999;
    background: #fff;
    text-align: center;
}

.qr-box img {
    display: block;
    width: 115px !important;
    height: 115px !important;
    margin: 0 auto;
    object-fit: contain;
}

.qr-title {
    margin: 5px 0 1px;
    font-size: 9px !important;
    font-weight: bold;
    line-height: 1.2;
}

.qr-id {
    margin: 0;
    font-size: 8px !important;
    color: #666;
    line-height: 1.2;
    word-break: break-word;
}

.footer-dokumen {
    margin: 24px 0 0;
    text-align: right;
    font-size: 9px !important;
    color: #888;
    line-height: 1.2;
}

@media print {

    @page {
        size: A4;
        margin: 3cm 3cm 3cm 4cm;
    }

    html,
    body {
        margin: 0 !important;
        padding: 0 !important;
        background: #fff !important;
    }

    .no-print {
        display: none !important;
    }

    .dokumen-wrapper {
        width: 100% !important;
        min-height: auto !important;
        margin: 0 !important;
        padding: 0 !important;
        background: #fff !important;
        border-radius: 0 !important;
        box-shadow: none !important;
    }

    .kop-surat {
        min-height: 88px;
        padding-left: 90px;
        padding-right: 35px;
        padding-bottom: 10px;
        margin-bottom: 17px;
    }

    .kop-logo {
        left: 0;
        top: -2px;
        width: 75px;
        height: 75px;
    }

    .kop-surat h1 {
        font-size: 21px;
    }

    .kop-alamat {
        font-size: 10px;
    }

    .kop-kontak {
        font-size: 9px;
    }

    .judul-dokumen {
        margin: 15px 0 20px;
        font-size: 17px;
    }

    .detail-container {
        margin-bottom: 16px;
    }

    .detail-row {
        margin-bottom: 6px;
        font-size: 14px;
        line-height: 1.4;
    }

    .detail-label {
        width: 125px;
        min-width: 125px;
    }

    .section-title {
        margin-top: 12px;
        margin-bottom: 5px;
        font-size: 15px;
    }

    .isi-dokumen {
        font-size: 14px;
        line-height: 1.5;
    }

    .pesan-qr {
        margin-top: 12px;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .qr-wrapper {
        margin-top: 6px;
    }

    .qr-box {
        width: 130px;
        padding: 5px;
    }

    .qr-box img {
        width: 110px !important;
        height: 110px !important;
    }

    .qr-title {
        margin-top: 4px;
        font-size: 8px !important;
    }

    .qr-id {
        font-size: 7px !important;
    }

    .footer-dokumen {
        margin-top: 20px;
        font-size: 8px !important;
    }

}

</style>


<div class="no-print">
    <?= Html::a('&larr; Kembali', ['/unit/index'], ['class' => 'btn btn-default']) ?>
    <button type="button" onclick="window.print()" class="btn btn-primary">Cetak</button>
</div>


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

        <div class="detail-row">
            <div class="detail-label">Nomor Surat</div>
            <div class="detail-value">
                : <?= Html::encode($model->nomor_surat ?? '-') ?>
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
            <img src="<?= Html::encode($qrImageUrl) ?>" alt="QR Code Presensi">
            <p class="qr-title">SCAN UNTUK PRESENSI</p>
            <p class="qr-id">ID Rapat: <?= Html::encode($qrValue) ?></p>
        </div>
    </div>

    <p class="footer-dokumen">
        Dicetak pada: <?= date('d M Y H:i') ?> | Sistem Agenda Universitas Andalas
    </p>

</div>