<?php
use yii\widgets\Pjax;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;
use backend\models\ModelAnketa;
use yii\web\View;

$model=new ModelAnketa();
$data=$model->getData();
?>
<?php
Pjax::begin(['id'=>'pjax-datatable','clientOptions' => ['method' => 'POST']]);
echo GridView::widget([
    'id'=>'datatable',
    // полученные данные
    'dataProvider' => $data,
    // Отображать 5 страниц
    'pager' => ['maxButtonCount' => 5],
    // колонки с данными
    'columns' => [
        [
            'label' =>"ID", // название столбца
            'attribute' => 'id', // атрибут
            //'value'=>function($data){return $data->id;} // объявлена анонимная функция и получен результат
        ],
        [
            'label' => 'Дата',
            'attribute' => 'dat',
            //'value' => function($data) { return $data->name; },
        ],
        [
            'label' => 'ФИО',
            'attribute' => 'fio',
            //'value' => function($data) { return $data->name; },
        ],
        [
            'label'=>'Селфи-видео',
            'format' => 'raw',
            'value' => function ($model) {
                if (empty($model['video'])) return "-";
                $url=str_replace('/admin','',Url::base(true)).'/uploads/';
                $html=Html::a('Ссылка',$url.$model['video'],['data-pjax' => '0','download'=>'']);
                /*$html.=' '.Html::a('<span class="glyphicon glyphicon-search"></span>', 'javascript:void(0)', [
                        'title' => 'Посмотреть',
                        'class'=>'avideo',
                        'data-pjax' => '0',
                        'data-row'=>$model['id'],
                    ]);*/
                return $html;
            }

        ],
        [
            'label'=>'Дата рожд.',
            'attribute' => 'q4',
        ],
        [
            'label'=>'График работы',
            'attribute' => 'q5',
        ],
        [
            'label'=>'Курение',
            'attribute' => 'q6',
        ],
        [
            'label'=>'Пол клиента',
            'attribute' => 'q7',
        ],
        [
            'label'=>'PDF-анкета',
            'format' => 'raw',
            'value' => function ($model) {
                return Html::a('Ссылка','/site/rezult?id='.$model['id'],['target'=>'_blank','data-pjax'=>"0"]);
            }
        ],
        [
            'label'=>'Подходит?',
            'attribute'=>'status'
        ],
        [
            'label'=>'Примечание HR',
            'format' => 'raw',
            'value' => function ($model) {
                if (empty($model['note'])) return '';
                return Html::img('/images/notes.png',['title'=>$model['note'], 'data-toggle'=>"tooltip"]);
            }
        ],
        ['class' => 'yii\grid\ActionColumn',
            'template' => '{notes}{update}{delete}',
            'buttons' => [
                'notes' => function ($url,$model,$key){
                    return Html::a('<span class="glyphicon glyphicon-comment"></span>', 'javascript:void(0)', [
                        'title' => 'Коментировать',
                        'class'=>'comment',
                        'data-pjax' => '0',
                        'data-row'=>$key,
                    ]).' ';
                },
                'update' => function ($url, $model, $key) {
                    return Html::a('<span class="glyphicon glyphicon-search"></span>', 'javascript:void(0)', [
                        'title' => 'Посмотреть',
                        'class' => 'aview',
                        'data-pjax' => '0',
                        'data-row'=>$key,
                    ]).' ';
                },
                'delete' => function ($url, $model, $key) {
                    $url="javascript: deleteAnketa($key)";
                    $html=Html::a('<span class="glyphicon glyphicon-remove text-danger"></span>', $url, [
                        'title' => 'Удалить',
                        'data-confirm' => 'Вы уверены что хотите удалить?',
                        'data-method' => 'post',
                        'data-pjax' => '0'
                    ]);
                    return $html;
                },
            ],
        ],
    ],
]);
$script = <<< JS
    function init()
    {
        $('#datatable .comment').click(function()
        {
            var id=$(this).attr('data-row');
            var jqxhr = $.get( "/admin/notes", {id: id})
                .done(function(data) {
                    $('#myNotes div.modal-body').html(data);
                })
                  .fail(function() {
                    alert( "error" );
                  });
             //$('#myNotes div.modal-body').html("<p>"+id+"</p>");
            //alert( id );
            $('#myNotes').modal();
        });

        $('#datatable .aview').click(function()
        {
            var id=$(this).attr('data-row');
            var jqxhr = $.get( "/admin/viewanketa", {id: id})
                .done(function(data) {
                    $('#myAnketa div.modal-body').html(data);
                })
                  .fail(function() {
                    alert( "error" );
                  });
             //$('#myNotes div.modal-body').html("<p>"+id+"</p>");
            //alert( id );
            $('#myAnketa').modal();
        });
    }

    $(document).ready(function(){
        $('#datatable [data-toggle="tooltip"]').tooltip();
        init();
    });

    function saveNote()
    {
        var vl={
            status: $('#myNotes #notesmodel-status').val(),
            notes: $('#myNotes #notesmodel-notes').val(),
            id: $('#myNotes #notesmodel-id').val(),
        };
        var jqxhr = $.post( "/admin/savenotes", vl)
            .done(function(data){
                if (data=="OK")
                {
                    $('#myNotes').modal('toggle');
                    //$.pjax.reload({container: '#pjax-datatable', timeout: false});
                    filter();
                    init();
                }
            });
    }

    function saveAnketa()
    {
        var vl={
            status: $('#myAnketa #notesmodel-status').val(),
            notes: $('#myAnketa #notesmodel-notes').val(),
            id: $('#myAnketa #notesmodel-id').val(),
        };
        var jqxhr = $.post( "/admin/saveanketa", vl)
            .done(function(data){
                if (data=="OK")
                {
                    $('#myAnketa').modal('toggle');
                    //$.pjax.reload({container: '#pjax-datatable', timeout: false});
                    filter();
                    init();
                }
            });
    }

    function deleteAnketa(id)
    {
        var jqxhr = $.get( "/admin/delete?id="+id)
            .done(function(data){
                if (data=="OK")
                {
                    filter();
                     init();
                }
            });
    }
JS;
$this->registerJs($script, View::POS_END);
?>
<input type="hidden" value="test">
<?
Pjax::end();

?>
<!-- Modal -->
<div id="myNotes" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">HR примечание</h4>
            </div>
            <div class="modal-body">
                <p>Произошла ошибка.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" onclick="saveNote()">Сохранить</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
            </div>
        </div>

    </div>
</div>
<div id="myAnketa" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Просмотр анкеты</h4>
            </div>
            <div class="modal-body">
                <p>Произошла ошибка.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" onclick="saveAnketa()">Сохранить</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
            </div>
        </div>

    </div>
</div>
<div id="myVideo" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Просмотр Selfy</h4>
            </div>
            <div class="modal-body">
                <p>Произошла ошибка.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
            </div>
        </div>

    </div>
</div>
<?

?>

