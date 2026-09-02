<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var string $content */

$this->beginPage();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body style="background: #f4f4f4; margin: 0; padding: 30px 0;">
<?php $this->beginBody() ?>

<?= $content ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>