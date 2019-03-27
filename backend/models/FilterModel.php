<?php
namespace backend\models;

use \common\models\SettingsRecord;
use frontend\models\ListRecord;
use \frontend\models\QuestRecord;
use backend\models\NotesModel;

class FilterModel
{
    var $option=array();
    var $quest=array();

    function __construct()
    {
        $qq=SettingsRecord::getValuesGroup('filter');
        $qq=$qq['quest'];
        $this->quest=QuestRecord::find()->where("id in ($qq)")->orderBy("tab")->all();

        $vv=ListRecord::find()->all();
        $this->option=[];
        foreach($vv as $item)
        {
            $this->option[$item->list][$item->id]=$item->val;
        }
    }

    public function getQuestions()
    {
        return $this->quest;
    }

    public function getOption($id)
    {
        return $this->getList($this->option[$id]);
    }

    private function getList($arr)
    {
        $rez=[0=>'Не задано!'];
        foreach($arr as $k=>$item)
            $rez[$k]=$item;
        return $rez;
    }

    public function getStatusOption()
    {
        $nn=new NotesModel();
        return $this->getList($nn->getStatusList());
    }


}