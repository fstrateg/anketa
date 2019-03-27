<?php
use \backend\models\FilterModel;
use yii\helpers\Html;
use yii\web\View;
?>
<div id="filterpanel">
<?php
$filter=new FilterModel();
$q=$filter->getQuestions();
foreach ($q as $item) {
    $ss=$filter->getOption($item->list);
?>
<div class="form-group">
    <label class="control-label"><?=$item->val;?></label>
    <?
    echo Html::beginTag("select",['class'=>'form-control','name'=>'q'.$item->id]);
    echo Html::renderSelectOptions(null,$ss);
    echo Html::endTag("select");
    ?>

</div>
<?php
}?>
    <div class="form-group">
        <label class="control-label">Подходит нам?</label>
    <?php
    echo Html::beginTag("select",['class'=>'form-control','name'=>'suit']);
    echo Html::renderSelectOptions(null,$filter->getStatusOption());
    echo Html::endTag("select");
    ?>
    </div>
<button class="btn btn-primary" onclick="filter()"><span class="glyphicon glyphicon-refresh"></span> Обновить</button>
</div>
<?
$script = <<< JS
    function filter()
    {
        var vl={};
        var cnt=$('#filterpanel select').each(function(index){
            vl[this.name]=this.value;
         });

        //$.pjax.reload({container: '#pjax-datatable', timeout: false});
        $.pjax({
        type       : 'POST',
        url        : '/admin/index',
        container  : '#pjax-datatable',
        data       : vl,
        push       : true,
        replace    : false,
        timeout    : 10000,
        "scrollTo" : false
    })
    }
JS;

$this->registerJs($script, View::POS_END);
?>