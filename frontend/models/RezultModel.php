<?php
namespace frontend\models;

use yii\base\Model;
use yii;
use frontend\models\AnketaRecord;

class RezultModel extends Model
{
    var $fio;
    var $dat;
    var $id;
    var $respondes;
    var $list;
    var $self;
    var $video;

    public function initRezult($id)
    {
        $this->id=$id;
        $rw=AnketaRecord::find()->where(['id'=>$id])->one();
        $this->fio=$rw->fio;
        $this->dat=$rw->dat;
        $this->video=$rw->video;
        $this->respondes=yii::$app->db->createCommand("Select a.*,b.val res,ifnull(c.self,0) self
from quest a
	inner join anketa_det b on (a.id=b.question)
	left join `list` c on a.`list`=c.`list` and c.self=1
        where b.anketaid=:id order by a.tab")->bindParam("id",$id)->queryAll();
        $this->getList();
        foreach($this->respondes as $k=>$v)
        {
            if (empty($v["res"]))
            {
                $v["res"]='-';
                $this->respondes[$k]=$v;
            }
            if ($v["type"]=="list"){
                if ($v["self"])
                {
                    $ss=AnketadetRecord::find()->where(["anketaid"=>$id,"question"=>$v['id'].'00'])->one();
                    if ($ss) $v["ress"]=$ss->val;
                }
                $v["res"]=$this->list[$v["res"]];
                $this->respondes[$k]=$v;

            }
        }
    }

    private function getList()
    {
        $this->list=[];
        $this->self=[];
        $rws=ListRecord::find()->all();
        foreach($rws as $item) {
            $this->list[$item->id] = $item->val;
            if ($item->self) $this->self[$item->id]=1;
        }
    }



}