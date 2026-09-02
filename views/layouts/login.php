<?php

/** @var yii\web\View $this */
/** @var string $content */

use yii\helpers\Html;

$this->beginPage();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <style>
        :root {
            --sirat-green: #0C6B3D;
            --sirat-green-dark: #0C6B3D ;
            --sirat-gold: #c9a227;
        }
        * { box-sizing: border-box; }
        body {
            background: #f4f3ef;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .login-card {
            display: flex;
            width: 100%;
            max-width: 960px;
            min-height: 520px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            overflow: hidden;
            margin: 24px;
        }
        .login-side {
        flex: 0 0 46%;
        background:
        linear-gradient(135deg, rgba(12, 107, 61, 0.3) 0%, rgba(201, 162, 39, 0.2) 100%),
        #F2F4F6;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 40px;
        text-align: center;
        }

        .login-side img.crest {
            width: 100px;
            height: 100px;
            object-fit: contain;
            margin-bottom: 16px;
        }
        .login-side .icon-badge {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: var(--sirat-green);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
        }
        .login-side .icon-badge svg { width: 22px; height: 22px; fill: #fff; }
        .login-side h2 {
            font-size: 1.05rem;
            font-weight: 600;
            color: #2c2c2c;
            margin: 0;
            line-height: 1.4;
        }
        .login-main {
            flex: 1;
            padding: 48px 56px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .login-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 6px;
        }
        .login-brand img { width: 28px; height: 28px; }
        .login-brand span { font-weight: 600; font-size: 0.95rem; color: #333; }
        .login-main h1 {
            font-size: 2.1rem;
            font-weight: 700;
            margin: 4px 0 0px;
            color: #1a1a1a;
        }
        .login-sub {
            color: #7a7a7a;
            font-size: 0.9rem;
            margin-bottom: 14px;
        }
        .login-divider {
            height: 2px;
            width: 100%;
            background: var(--sirat-gold);
            opacity: 0.6;
            margin-bottom: 24px;
        }
        .login-main label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 6px;
            display: block;
        }
        .login-main .form-control {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 10px 14px;
            font-size: 0.9rem;
            width: 100%;
            margin-bottom: 14px;
        }
        .login-main .form-control:focus {
            outline: none;
            border-color: var(--sirat-green);
            box-shadow: 0 0 0 3px rgba(31,77,44,0.1);
        }
        .forgot-link {
            font-size: 0.8rem;
            color: var(--sirat-green);
            text-decoration: none;
            float: right;
            font-weight: 500;
        }
        .btn-login {
            background: var(--sirat-green);
            border: none;
            color: #fff;
            font-weight: 600;
            padding: 12px;
            border-radius: 8px;
            width: 100%;
            margin-top: 10px;
            font-size: 0.95rem;
            cursor: pointer;
        }
        .btn-login:hover { background: var(--sirat-green-dark); color: #fff; }
        .login-footer {
            text-align: center;
            font-size: 0.75rem;
            color: #999;
            margin-top: 20px;
        }
        .password-wrapper { position: relative; }
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 38px;
            cursor: pointer;
            color: #999;
            background: none;
            border: none;
            font-size: 1rem;
        }
        .form-check { display: flex; align-items: center; gap: 8px; margin: 4px 0 8px; }
        .form-check label { margin: 0; font-weight: 400; font-size: 0.85rem; color: #444; }
        .alert-danger {
            background: #fdecea; color: #a12622; border: 1px solid #f5c6c3;
            padding: 10px 14px; border-radius: 8px; font-size: 0.85rem; margin-bottom: 14px;
        }
        .invalid-feedback { color: #c0392b; font-size: 0.78rem; margin-top: -10px; margin-bottom: 10px; }
        @media (max-width: 700px) {
            .login-card { flex-direction: column; }
            .login-side { flex: none; padding: 24px; }
            .login-main { padding: 32px; }
        }
    </style>
</head>
<body>
<?php $this->beginBody() ?>

<?= $content ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
