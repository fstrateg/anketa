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

    public static function InitInstance($id, $where = null)
    {
        $rez=new RezultModel();
        $rez->initRezult($id);
        return $rez;
    }

    public static function filter($id, $where)
    {
        $rez=new RezultModel();
        $rez->initRezult($id);
        foreach($where as $key=>$vl)
        {
            if (substr($key,1,1)!='q') continue;
            if (empty($vl)) continue;
            $q=str_replace('q','',$key)+0;
            foreach($rez->respondes as $r)
            {
                if ($r->id==$q)
                {
                    if ($r->val!=$vl) $rez=null;
                }
            }
        }
        return $rez;
    }

    public function initRezult($id, $where = '')
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