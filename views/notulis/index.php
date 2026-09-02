<?php

use app\models\Agenda;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var string|null $search */
/** @var string|null $statusFilter */

$this->title = 'Daftar Agenda Rapat';
$this->params['breadcrumbs'][] = 'Notulen';
$this->params['breadcrumbs'][] = $this->title;

function statusNotulenBadge(Agenda $model)
{
    $lampirans = $model->lampirans;

    if (empty($lampirans)) {
        return ['label' => 'Belum Diunggah', 'color' => '#fee2e2', 'text' => '#991b1b'];
    }

    $lampiranTerbaru = end($lampirans);

    if ($lampiranTerbaru->status === 'draft') {
        return ['label' => 'Draft', 'color' => '#f1f5f9', 'text' => '#475569'];
    }

    if (!empty($lampiranTerbaru->email_sent_at)) {
        return ['label' => 'Email Terkirim', 'color' => '#dbeafe', 'text' => '#1e40af'];
    }

    return ['label' => 'Selesai Diunggah', 'color' => '#dcfce7', 'text' => '#166534'];
}
?>
<div class="notulis-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>Kelola status dokumentasi dan notulensi seluruh agenda rapat universitas.</p>

    <?php Pjax::begin(['id' => 'notulis-pjax']); ?>

    <?php $form = \yii\widgets\ActiveForm::begin([
        'method' => 'get',
        'action' => ['notulis/index'],
        'options' => ['style' => 'display:flex; gap:10px; margin-bottom:16px; align-items:center;'],
    ]); ?>

        <?= Html::textInput('search', $search, [
            'class' => 'form-control',
            'placeholder' => 'Cari judul agenda atau penyelenggara...',
            'style' => 'max-width:320px;',
        ]) ?>

        <?= Html::dropDownList('status_notulen', $statusFilter, [
            '' => 'Semua Status',
            'Belum Diunggah' => 'Belum Diunggah',
            'Draft' => 'Draft',
            'Selesai Diunggah' => 'Selesai Diunggah',
            'Email Terkirim' => 'Email Terkirim',
        ], [
            'class' => 'form-control',
            'style' => 'max-width:200px;',
            'onchange' => 'this.form.submit()',
        ]) ?>

        <?= Html::submitButton('Cari', ['class' => 'btn btn-primary']) ?>

        <?php if (!empty($search) || !empty($statusFilter)): ?>
            <?= Html::a('Reset', ['notulis/index'], ['class' => 'btn btn-outline-secondary']) ?>
        <?php endif; ?>

    <?php \yii\widgets\ActiveForm::end(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => "{items}\n<div class='mt-3'>{pager}</div>",
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'label' => 'Judul Rapat & Penyelenggara',
                'format' => 'raw',
                'value' => function (Agenda $model) {
                    return '<strong>' . Html::encode($model->pembahasan) . '</strong>';
                },
            ],
            [
                'label' => 'Tanggal & Waktu',
                'format' => 'raw',
                'value' => function (Agenda $model) {
                    $tanggal = date('d M Y', strtotime($model->tanggal));
                    $waktu = date('H:i', strtotime($model->waktu_mulai));
                    return $tanggal . '<br><span style="color:#888;">' . $waktu . ' WIB</span>';
                },
            ],
            [
                'label' => 'Tempat/Lokasi',
                'value' => function (Agenda $model) {
                    return $model->lokasi->lokasi ?? '-';
                },
            ],
            [
                'label' => 'Status Notulen',
                'format' => 'raw',
                'value' => function (Agenda $model) {
                    $badge = statusNotulenBadge($model);
                    return '<span style="background:' . $badge['color'] . '; color:' . $badge['text'] . '; padding: 4px 12px; border-radius: 12px; font-size: 12px;">' . $badge['label'] . '</span>';
                },
            ],
            [
                'label' => 'Aksi',
                'format' => 'raw',
                'value' => function (Agenda $model) {
                    $badge = statusNotulenBadge($model);
                    if ($badge['label'] === 'Belum Diunggah') {
                        return Html::a('Upload Notulen', ['/lampiran/create', 'agenda_id' => $model->agenda_id], ['class' => 'btn btn-primary btn-sm']);
                    } elseif ($badge['label'] === 'Draft') {
                        return Html::a('Edit Notulen', ['/lampiran/update', 'agenda_id' => $model->agenda_id], ['class' => 'btn btn-outline-success btn-sm']);
                    } else {
                        return Html::a('Lihat Berkas', ['/lampiran/index', 'agenda_id' => $model->agenda_id], ['class' => 'btn btn-outline-secondary btn-sm']);
                    }
                },
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>

</div>