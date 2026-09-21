<?php

use app\models\Agenda;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var string|null $search */
/** @var string|null $statusFilter */

$this->title = 'Daftar Agenda Rapat';
$this->params['breadcrumbs'][] = 'Notulen';
$this->params['breadcrumbs'][] = $this->title;

function latestAvailableLampiran(Agenda $model)
{
    $lampirans = array_values(array_filter($model->lampirans, static function ($lampiran) {
        return $lampiran->deleted_at === null
            && !empty($lampiran->file_path)
            && is_file(Yii::getAlias('@webroot/' . ltrim($lampiran->file_path, '/')));
    }));

    usort($lampirans, static function ($first, $second) {
        return $second->lampiran_id <=> $first->lampiran_id;
    });

    return $lampirans[0] ?? null;
}

function statusNotulenBadge(Agenda $model)
{
    $lampiranTerbaru = latestAvailableLampiran($model);

    if ($lampiranTerbaru === null) {
        return ['label' => 'Belum Diunggah', 'color' => '#fee2e2', 'text' => '#991b1b'];
    }

    if (!empty($lampiranTerbaru->email_sent_at)) {
        return ['label' => 'Email Terkirim', 'color' => '#dbeafe', 'text' => '#1e40af'];
    }

    return ['label' => 'Selesai Diunggah', 'color' => '#dcfce7', 'text' => '#166534'];
}
?>
<div class="nt-breadcrumb">
    <a href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/notulis/dashboard'])) ?>">Dashboard</a>
    &nbsp;›&nbsp; <span>Daftar Agenda</span>
</div>
<h1 class="nt-page-title">Daftar Agenda Rapat</h1>
<p class="nt-page-description">Kelola status dokumentasi dan notulensi seluruh agenda rapat universitas.</p>

    <?php Pjax::begin(['id' => 'notulis-pjax']); ?>

    <?php $form = \yii\widgets\ActiveForm::begin([
        'method' => 'get',
        'action' => ['notulis/index'],
        'options' => ['class' => 'nt-card nt-filter-card nt-filter-form'],
    ]); ?>

        <?= Html::dropDownList('status_notulen', $statusFilter, [
            '' => 'Semua Status',
            'Belum Diunggah' => 'Belum Diunggah',
            'Draft' => 'Draft',
            'Selesai Diunggah' => 'Selesai Diunggah',
        ], [
            'class' => 'nt-filter-select',
            'onchange' => 'this.form.submit()',
        ]) ?>

        <?= Html::submitButton('☷&nbsp; Filter Lain', ['class' => 'nt-filter-button']) ?>

        <?php if (!empty($search) || !empty($statusFilter)): ?>
            <?= Html::a('Reset', ['notulis/index'], ['class' => 'nt-filter-button']) ?>
        <?php endif; ?>

    <?php \yii\widgets\ActiveForm::end(); ?>

    <section class="nt-card nt-table-card">
        <div class="nt-table-wrap">
            <table class="nt-table">
                <thead><tr><th>No</th><th>Judul Rapat &amp; Penyelenggara</th><th>Tanggal &amp; Waktu</th><th>Tempat/Lokasi</th><th>Status Notulen</th><th>Aksi</th></tr></thead>
                <tbody>
                <?php $models = $dataProvider->getModels(); ?>
                <?php if (empty($models)): ?>
                    <tr><td colspan="6" class="nt-empty">Belum ada agenda yang sesuai.</td></tr>
                <?php endif; ?>
                <?php foreach ($models as $index => $model):
                    $badge = statusNotulenBadge($model);
                    $statusClass = $badge['label'] === 'Belum Diunggah' ? 'red' : ($badge['label'] === 'Draft' ? 'gray' : ($badge['label'] === 'Email Terkirim' ? 'blue' : 'green'));
                    $lampiran = latestAvailableLampiran($model);
                    $hasFile = $lampiran !== null;
                    $number = $dataProvider->pagination->offset + $index + 1;
                ?>
                    <tr>
                        <td><?= $number ?></td>
                        <td><strong><?= Html::encode($model->pembahasan) ?></strong><small><?= Html::encode($model->createdBy->nama ?? 'Agenda Universitas') ?></small></td>
                        <td><strong><?= date('d M Y', strtotime($model->tanggal)) ?></strong><small><?= date('H:i', strtotime($model->waktu_mulai)) ?> WIB</small></td>
                        <td><?= Html::encode($model->lokasi->lokasi ?? '-') ?></td>
                        <td><span class="nt-status <?= $statusClass ?>"><?= Html::encode($badge['label']) ?></span></td>
                        <td>
                            <?php if ($badge['label'] === 'Belum Diunggah'): ?>
                                <?= Html::a('<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M11 16h2V8l3 3 1.4-1.4L12 4.2l-5.4 5.4L8 11l3-3v8zM5 20v-2h14v2H5z"/></svg>Upload Notulen', ['/lampiran/create', 'agenda_id' => $model->agenda_id], ['class' => 'nt-action-button primary']) ?>
                            <?php elseif ($badge['label'] === 'Draft'): ?>
                                <?= Html::a('<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>Edit Notulen', ['/lampiran/update', 'agenda_id' => $model->agenda_id], ['class' => 'nt-action-button']) ?>
                                <?php if ($hasFile): ?>
                                    <?= Html::a('Lihat Berkas', ['/lampiran/index', 'agenda_id' => $model->agenda_id], ['class' => 'nt-action-button muted']) ?>
                                <?php endif; ?>
                            <?php else: ?>
                                <?php if ($hasFile): ?>
                                    <?= Html::a('Lihat Berkas', ['/lampiran/index', 'agenda_id' => $model->agenda_id], ['class' => 'nt-action-button muted']) ?>
                                <?php else: ?>
                                    <?= Html::a('Edit Notulen', ['/lampiran/update', 'agenda_id' => $model->agenda_id], ['class' => 'nt-action-button']) ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="nt-pagination">
            <span>Menampilkan <?= count($models) ?> agenda</span>
            <div class="nt-pagination-links">
                <?php if ($dataProvider->pagination->page > 0): ?>
                    <?= Html::a('&lsaquo;', ['notulis/index', 'page' => $dataProvider->pagination->page], ['aria-label' => 'Halaman sebelumnya']) ?>
                <?php else: ?><span class="disabled">&lsaquo;</span><?php endif; ?>
                <?php for ($page = 0; $page < $dataProvider->pagination->pageCount; $page++): ?>
                    <?= Html::a((string) ($page + 1), ['notulis/index', 'page' => $page + 1], ['class' => $page === $dataProvider->pagination->page ? 'active' : '']) ?>
                <?php endfor; ?>
                <?php if ($dataProvider->pagination->page < $dataProvider->pagination->pageCount - 1): ?>
                    <?= Html::a('&rsaquo;', ['notulis/index', 'page' => $dataProvider->pagination->page + 2], ['aria-label' => 'Halaman berikutnya']) ?>
                <?php else: ?><span class="disabled">&rsaquo;</span><?php endif; ?>
            </div>
        </div>
    </section>
    <?php Pjax::end(); ?>
