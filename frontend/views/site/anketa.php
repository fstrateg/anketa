<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \frontend\models\AnketaModel */
$this->title = 'La Letty';
?>
<h3>Здравствуйте <?=$fio?>!</h3>
<!--h3>Анкета для работы в La Letty studio</h3-->
<p>сбор анкетных данных потенциальных сотрудников La Letty studio</p>
<?
$form=ActiveForm::begin(['id'=>'anketa_form']);

$model->printFields($form);
?>
<div class="form-group">
            <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-envelope"></span> Отправить анкету</button>
</div>
<?
ActiveForm::end()
?>