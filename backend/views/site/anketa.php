<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model \frontend\models\RezultModel */

$base=str_replace('/admin','',Url::base(true));
?>
    <h3>Анкета <?=$model->fio?></h3>
    <p>Дата заполнения <?=$model->dat?><br />
        Ссылка на видео <a href="<?= $base; ?>/uploads/<?=$model->video?>"><?=$model->video?></a>
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
<div class="row">
        <div class="col-lg-12">
            <?php $form = ActiveForm::begin(['id' => 'snote-form']); ?>
            <?= $form->field($notes, 'status')->dropDownList($notes->getStatusList(),['prompt' => 'Выберите статус...']) ?>
            <?= $form->field($notes, 'notes')->textarea(); ?>
            <?= $form->field($notes, 'id')->hiddenInput()->label(false); ?>
            <? ActiveForm::end(); ?>
</div>
</div>
