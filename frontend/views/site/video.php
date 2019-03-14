<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use limion\jqueryfileupload\JQueryFileUpload;

/* @var $this yii\web\View */
/* @var $model \frontend\models\AnketaForm */
$this->title = 'La Letty';
?>
    <h3>Анкета для работы в La Letty studio</h3>
    <p>сбор анкетных данных потенциальных сотрудников La Letty studio</p>
<?
$form=ActiveForm::begin(['id'=>'anketa_form','options' => ['enctype' => 'multipart/form-data']]);
//$model=new frontend\models\AnketaForm();
echo $form->field($model,'fio');
echo $form->field($model,'videoFile')->fileInput();
?>
    <div class="form-group">
        <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-envelope"></span> Отправить видео</button>
    </div>
<?
ActiveForm::end();

?>