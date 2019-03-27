<?php
use yii\widgets\ActiveForm;
/* @var $model \backend\models\NotesModel */
?>
    <div class="row">
        <div class="col-lg-12">
           <?php $form = ActiveForm::begin(['id' => 'note-form']); ?>
            <?= $form->field($model, 'status')->dropDownList($model->getStatusList(),['prompt' => 'Выберите статус...']) ?>
            <?= $form->field($model, 'notes')->textarea(); ?>
            <?= $form->field($model, 'id')->hiddenInput()->label(false); ?>
            <? ActiveForm::end(); ?>
        </div>
    </div>
