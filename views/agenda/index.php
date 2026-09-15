<?php

/** @var yii\web\View $this */
/** @var app\models\AgendaSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use app\models\Agenda;

$this->title = 'Kelola Agenda Rapat';

/** @var app\models\User $identity */
$identity = Yii::$app->user->identity;
$canManage = $identity->can('manageAgenda');

$statusMap = [
    'terjadwal' => ['class' => 'badge-terjadwal', 'label' => 'Terjadwal'],
    'berlangsung' => ['class' => 'badge-berlangsung', 'label' => 'Berlangsung'],
    'selesai' => ['class' => 'badge-selesai', 'label' => 'Selesai'],
    'dibatalkan' => ['class' => 'badge-dibatalkan', 'label' => 'Dibatalkan'],
];

$iconCalendar = '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M7 2v2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-2V2h-2v2H9V2H7zM5 9h14v11H5V9z"/></svg>';
$iconPin = '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a7 7 0 0 0-7 7c0 5.25 7 13 7 13s7-7.75 7-13a7 7 0 0 0-7-7zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5z"/></svg>';
$iconEye = '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10zm0-8a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/></svg>';
$iconEdit = '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>';
$iconDelete = '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M6 7h12l-1 14H7L6 7zm3-4h6l1 2H8l1-2z"/></svg>';
?>
<div class="breadcrumb">
    <a href="<?= Yii::$app->homeUrl ?>">Dashboard</a> &nbsp;›&nbsp; <span class="current">Kelola Agenda</span>
</div>

<div class="dash-banner">
    <div>
        <h1>Kelola Agenda Rapat</h1>
        <p>Manajemen jadwal dan materi rapat universitas.</p>
    </div>
    <?php if ($canManage): ?>
        <?= Html::a('+ Tambah Agenda', ['/agenda/create'], ['class' => 'btn-primary-sm']) ?>
    <?php endif; ?>
</div>

<div class="card filter-card">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'action' => ['/agenda/index'],
        'options' => ['class' => 'filter-bar'],
    ]); ?>

    <div class="filter-search">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
        <?= Html::activeTextInput($searchModel, 'pembahasan', [
            'placeholder' => 'Cari judul agenda atau unit...',
            'class' => 'filter-search-input',
        ]) ?>
    </div>

    <?= Html::activeDropDownList($searchModel, 'waktuFilter', [
        '' => 'Semua Waktu',
        'akan_datang' => 'Akan Datang',
        'selesai' => 'Sudah Lewat',
    ], ['class' => 'filter-select']) ?>

    <?= Html::activeDropDownList($searchModel, 'status', ['' => 'Semua Status'] + Agenda::statusList(), [
        'class' => 'filter-select',
    ]) ?>

    <?= Html::submitButton('Cari', ['class' => 'btn-primary-sm']) ?>

    <?php ActiveForm::end(); ?>
</div>

<div class="card">
    <table class="table-clean">
        <thead>
            <tr>
                <th>Judul/Pembahasan</th>
                <th>Waktu &amp; Tempat</th>
                <th>Unit</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php $models = $dataProvider->getModels(); ?>
        <?php if (empty($models)): ?>
            <tr><td colspan="5" class="table-empty">Tidak ada agenda yang cocok dengan pencarian.</td></tr>
        <?php endif; ?>
        <?php foreach ($models as $a):
            $status = $statusMap[$a->status] ?? ['class' => '', 'label' => Html::encode($a->status)];
            $unitName = $a->lokasi->unit->nama_unit ?? '-';
        ?>
            <tr>
                <td>
                    <?= Html::a(Html::encode($a->pembahasan), ['/agenda/view', 'id' => $a->agenda_id], ['class' => 'row-link']) ?>
                </td>
                <td>
                    <div class="wt-line"><?= $iconCalendar ?> <?= Yii::$app->formatter->asDate($a->tanggal, 'php:d M Y') ?>, <?= substr($a->waktu_mulai, 0, 5) ?>-<?= substr($a->waktu_selesai, 0, 5) ?></div>
                    <div class="wt-line muted"><?= $iconPin ?> <?= Html::encode($a->lokasi->lokasi ?? '-') ?></div>
                </td>
                <td><?= Html::encode($unitName) ?></td>
                <td><span class="badge-status <?= $status['class'] ?>"><?= $status['label'] ?></span></td>
                <td>
                    <div class="action-icons">
                        <?= Html::a($iconEye, ['/agenda/view', 'id' => $a->agenda_id], ['title' => 'Lihat']) ?>
                        <?php if ($canManage): ?>
                            <?= Html::a($iconEdit, ['/agenda/update', 'id' => $a->agenda_id], ['title' => 'Ubah']) ?>
                            <?= Html::a($iconDelete, ['/agenda/delete', 'id' => $a->agenda_id], [
                                'title' => 'Hapus',
                                'class' => 'danger',
                                'data' => ['confirm' => 'Yakin ingin menghapus agenda ini?', 'method' => 'post'],
                            ]) ?>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <?php if ($dataProvider->pagination->pageCount > 1): ?>
        <div class="pagination-wrap">
            <?= LinkPager::widget([
                'pagination' => $dataProvider->pagination,
                'options' => ['class' => 'pager'],
                'linkOptions' => ['class' => 'pager-link'],
                'activePageCssClass' => 'pager-active',
                'prevPageLabel' => '‹',
                'nextPageLabel' => '›',
            ]) ?>
        </div>
    <?php endif; ?>
</div>
