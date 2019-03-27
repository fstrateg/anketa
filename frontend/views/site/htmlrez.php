<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model \frontend\models\RezultModel */
?>
    <html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: small; }
    </style>
</head>
<body>
    <h3>Анкета <?=$model->fio?></h3>
    <p>Дата заполнения <?=$model->dat?><br />
        Ссылка на видео <a href="<?= Url::base(true); ?>/uploads/<?=$model->video?>"><?=$model->video?></a>
    </p>
<?
foreach($model->respondes as $item)
{
    ?>
    <p><b><?=$item['val']."\n"?></b><br /></p><p>
    <?
        echo $item['res'];
        if (isset($item['ress'])) echo '<br />'.$item['ress'];
    ?>
    </p>
    <?
}
?>
</body>
</html>
