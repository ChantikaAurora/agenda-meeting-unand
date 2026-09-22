<?php

/** @var yii\web\View $this */
/** @var app\models\Agenda $model */
/** @var app\models\Member[] $availableMembers */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Tambah Peserta Undangan';
?>
<div class="breadcrumb">
    <a href="<?= Yii::$app->urlManager->createUrl(['/dashboard/index']) ?>">Dashboard</a> &nbsp;›&nbsp;
    <?= Html::a('Kelola Agenda', ['/agenda/index']) ?> &nbsp;›&nbsp;
    <?= Html::a(Html::encode($model->pembahasan), ['/agenda/view', 'id' => $model->agenda_id]) ?> &nbsp;›&nbsp;
    <span class="current">Tambah Peserta</span>
</div>

<div class="dash-banner">
    <div>
        <h1><?= Html::encode($this->title) ?></h1>
        <p>Pilih member yang akan menerima undangan untuk agenda ini.</p>
    </div>
</div>

<div class="card agenda-member-form-card">
    <?php if (empty($availableMembers)): ?>
        <p class="agenda-member-empty">Semua member aktif sudah terdaftar sebagai peserta agenda ini.</p>
        <?= Html::a('Kembali ke Kelola Agenda', ['/agenda/index'], ['class' => 'btn-secondary-sm']) ?>
    <?php else: ?>
        <?php $form = ActiveForm::begin(); ?>
        <label class="form-label" for="member_id">Pilih Peserta</label>
        <?= Html::dropDownList('member_id', null, ArrayHelper::map($availableMembers, 'member_id', static function ($member) {
            return $member->nama . ' — ' . ($member->email ?: 'email belum diisi');
        }), [
            'id' => 'member_id',
            'class' => 'form-control',
            'prompt' => '-- Pilih member --',
            'required' => true,
        ]) ?>
        <div class="form-actions">
            <?= Html::submitButton('Tambah Peserta', ['class' => 'btn-primary-sm']) ?>
            <?= Html::a('Batal', ['/agenda/index'], ['class' => 'btn-secondary-sm']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    <?php endif; ?>
</div>

<?php $this->registerCss(<<<CSS
.agenda-member-form-card {
    max-width: 680px;
}

.agenda-member-empty {
    margin: 0 0 16px;
    color: #6b7280;
}
CSS
); ?>