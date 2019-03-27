<?php
use yii\web\View;
/* @var $this yii\web\View */

$this->title = 'La-Litty';
?>
<div class="site-index">
    <div class="container">
        <div class="panel-group">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a id="filter" data-toggle="collapse" href="#collapse1">Фильтры показать</a>
                    </h4>
                </div>
                <div id="collapse1" class="panel-collapse collapse">
                    <div class="panel-body"><?= $this->render('filter');?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <?=$this->render('atable');?>
    </div>

</div>
<?
$script = <<< JS
$(document).ready(function(){
    $('a#filter').click(function(){
        var att=$(this).attr('aria-expanded');
        if (att=='true')
        {
            $(this).text("Фильтры показать");
        }else{
            $(this).text("Фильтр скрыть");

        }
    });

});
JS;
$this->registerJs($script, View::POS_END);
?>