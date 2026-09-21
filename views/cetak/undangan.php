<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Agenda $model */
/** @var bool $forPdf */

$this->title = 'Undangan Rapat';
$tanggal = Yii::$app->formatter->asDate($model->tanggal, 'php:d F Y');
$qrValue = $model->qr_code_value ?: $model->agenda_id;
$logoPath = Yii::getAlias('@webroot/images/logo-unand.png');
$logoSrc = is_file($logoPath)
    ? 'data:image/png;base64,' . base64_encode((string) file_get_contents($logoPath))
    : Yii::getAlias('@web') . '/images/logo-unand.png';
$qrPath = !empty($model->qr_code_path) ? Yii::getAlias('@webroot/' . $model->qr_code_path) : '';
$qrSrc = is_file($qrPath)
    ? 'data:image/png;base64,' . base64_encode((string) file_get_contents($qrPath))
    : 'https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=' . urlencode($qrValue);
?>

<?php if (!$forPdf): ?>
<div class="invitation-toolbar">
    <?= Html::a('&larr; Kembali ke Detail Agenda', ['/agenda/view', 'id' => $model->agenda_id], ['class' => 'invitation-back']) ?>
    <button type="button" onclick="window.print()" class="invitation-print">Cetak</button>
</div>
<?php endif; ?>

<div class="invitation-paper<?= $forPdf ? ' pdf-paper' : '' ?>">
    <div class="invitation-header">
        <img src="<?= Html::encode($logoSrc) ?>" alt="Logo Universitas Andalas">
        <div>
            <h1>UNIVERSITAS ANDALAS</h1>
            <p>Undangan Rapat</p>
        </div>
    </div>

    <p>Dengan hormat,</p>
    <p>Sehubungan dengan akan dilaksanakannya rapat, kami mengundang Bapak/Ibu untuk hadir pada:</p>

    <table class="invitation-details">
        <tr><th>Agenda</th><td><?= Html::encode($model->pembahasan) ?></td></tr>
        <tr><th>Hari, tanggal</th><td><?= Html::encode($tanggal) ?></td></tr>
        <tr><th>Waktu</th><td><?= Html::encode(substr($model->waktu_mulai, 0, 5) . ' - ' . substr($model->waktu_selesai, 0, 5) . ' WIB') ?></td></tr>
        <tr><th>Tempat</th><td><?= Html::encode($model->lokasi->lokasi ?? '-') ?></td></tr>
    </table>

    <p><?= nl2br(Html::encode($model->deskripsi ?: 'Demikian undangan ini disampaikan. Atas perhatian dan kehadirannya, kami ucapkan terima kasih.')) ?></p>

    <div class="invitation-qr">
        <img src="<?= Html::encode($qrSrc) ?>" alt="QR Code Presensi">
        <strong>SCAN UNTUK PRESENSI</strong>
    </div>

    <p class="invitation-footer">Sistem Agenda Universitas Andalas</p>
</div>

<style>
    * { box-sizing: border-box; }
    @page { margin: 0; }
    body { margin: 0; background: #f3f4f6; color: #111827; font-family: Arial, sans-serif; }
    .invitation-toolbar { width: 800px; margin: 18px auto 14px; display: flex; justify-content: space-between; }
    .invitation-back, .invitation-print { display: inline-flex; align-items: center; min-height: 34px; padding: 0 14px; border-radius: 6px; font-size: 13px; font-weight: 600; text-decoration: none; }
    .invitation-back { border: 1px solid #d1d5db; background: #fff; color: #111827; }
    .invitation-print { border: 1px solid #247b59; background: #247b59; color: #fff; cursor: pointer; }
    .invitation-paper { width: 800px; min-height: 1120px; margin: 0 auto; padding: 60px 70px; background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,.12); font-family: "Times New Roman", serif; font-size: 16px; line-height: 1.6; }
    .invitation-paper.pdf-paper { width: 100%; min-height: 0; padding: 42px 58px; margin: 0; box-shadow: none; }
    .invitation-header { display: flex; align-items: center; gap: 20px; padding-bottom: 18px; border-bottom: 2px solid #111; text-align: center; }
    .invitation-header img { width: 78px; height: 78px; object-fit: contain; }
    .invitation-header div { flex: 1; }
    .invitation-header h1 { margin: 0; font-size: 22px; }
    .invitation-header p { margin: 3px 0 0; font-size: 16px; }
    .invitation-details { width: 100%; margin: 25px 0; border-collapse: collapse; }
    .invitation-details th { width: 150px; text-align: left; }
    .invitation-details th, .invitation-details td { padding: 5px 0; vertical-align: top; }
    .invitation-qr { margin: 26px auto 0; text-align: center; }
    .invitation-qr img { display: block; width: 110px; height: 110px; margin: 0 auto 8px; }
    .invitation-qr strong { font-size: 11px; }
    .invitation-footer { margin-top: 42px; text-align: right; font-size: 12px; }
    @media print { .invitation-toolbar { display: none; } body { background: #fff; } .invitation-paper { width: 100%; min-height: 0; margin: 0; box-shadow: none; } }
</style>