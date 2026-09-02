<?php

/** @var yii\web\View $this */
/** @var app\models\LoginForm $model */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Login - Sistem Manajemen Agenda Rapat';
?>
<div class="login-card">

    <div class="login-side">
        <?= Html::img('@web/images/logo-unand.png', [
            'class' => 'crest',
            'alt' => 'Universitas Andalas',
            'onerror' => "this.style.display='none'",
        ]) ?>
        <div class="icon-badge">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M7 2v2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-2V2h-2v2H9V2H7zM5 9h14v11H5V9z"/>
            </svg>
        </div>
        <h2>Sistem Informasi<br>Agenda Rapat</h2>
    </div>

    <div class="login-main">
        <div class="login-brand">
            <?= Html::img('@web/images/logo-unand.png', [
                'alt' => 'Universitas Andalas',
                'onerror' => "this.style.display='none'",
            ]) ?>
            <span>Universitas Andalas</span>
        </div>
        <h1>Sistem Manajemen Agenda Rapat</h1>
        <p class="login-sub">Sign in to manage your meetings and schedules.</p>
        <div class="login-divider"></div>

        <?php if (Yii::$app->session->hasFlash('error')): ?>
            <div class="alert-danger"><?= Yii::$app->session->getFlash('error') ?></div>
        <?php endif; ?>

        <?php $form = ActiveForm::begin([
            'id' => 'login-form',
            'options' => ['class' => ''],
            'fieldConfig' => [
                'errorOptions' => ['class' => 'invalid-feedback'],
            ],
        ]); ?>

        <?= $form->field($model, 'username', [
            'options' => ['class' => ''],
            'template' => "{label}\n{input}\n{error}",
        ])->label('Email or Username')->textInput([
            'autofocus' => true,
            'placeholder' => 'Enter your email',
            'class' => 'form-control',
        ]) ?>

        <div class="password-wrapper">
            <label>
                Password
                <a href="#" class="forgot-link">Forgot password?</a>
            </label>
            <?= Html::activePasswordInput($model, 'password', [
                'id' => 'password-input',
                'placeholder' => 'Enter your password',
                'class' => 'form-control',
            ]) ?>
            <button type="button" class="password-toggle" onclick="togglePassword()">👁</button>
            <?= Html::error($model, 'password', ['class' => 'invalid-feedback']) ?>
        </div>

        <div class="form-check">
            <?= Html::activeCheckbox($model, 'rememberMe', [
                'label' => false,
            ]) ?>
            <label for="loginform-rememberme">Remember me</label>
        </div>

        <?= Html::submitButton('Login', ['class' => 'btn-login']) ?>

        <?php ActiveForm::end(); ?>

        <p class="login-footer">© <?= date('Y') ?> Universitas Andalas. All rights reserved.</p>
    </div>
</div>

<script>
function togglePassword() {
    const input = document.getElementById('password-input');
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>
