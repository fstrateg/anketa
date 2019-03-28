<?php
namespace backend\models;

use frontend\models\AnketadetRecord;
use frontend\models\AnketaRecord;
use frontend\models\RezultModel;
use yii\data\ArrayDataProvider;
use yii;

class ModelAnketa
{
    public function getData()
    {

        //$ank=AnketaRecord::find()->all();
        $ank=$this->getFilterData();
        $rez=[];
        foreach($ank as $item)
        {
            if (is_object($item)) {
                $row = ["id" => $item->id,
                    'fio' => $item->fio,
                    'dat' => $item->dat,
                    'video' => $item->video,
                    'status' => $item->status,
                    'note' => $item->note

                ];
            }
            else
            {
                $row = $item;
            }
            $id=$row['id'];
            $details=RezultModel::InitInstance($id);
            foreach($details->respondes as $q)
            {
                if (!in_array($q['id'],[4,5,6,7])) continue;
                $row['q'.$q['id']]=$q['res'];
            }
            $rez[$id]=$row;
        }
        return new ArrayDataProvider(['allModels'=>$rez,'pagination'=>[
            'pageSize' => 25,
        ],]);
    }

    public function getFilterData()
    {
        $usl=$this->getFilterFromPost();
        $usl2=$this->getFilterFromPostAdd();
        if (empty($usl))
        {
            if (empty($usl2)) return AnketaRecord::find()->all();
            return AnketaRecord::find()->where($usl2)->all();
        }
        $where=implode(' or ',$usl);
        $usl2=empty($usl2)?'':" and $usl2";
        $cnt=count($usl);
        $rez=yii::$app->db->createCommand(
            "
Select id,fio,dat,video,status,note from anketa a where id in (
        Select anketaid from (
        Select anketaid, count(anketaid) cnt
        from anketa_det a where $where
        group by anketaid) b
        where cnt=$cnt) $usl2")->queryAll();


        return $rez;
    }

    private function getFilterFromPost()
    {
        if (count($_POST)<=1) return '';
        $rzd='';
        $usl=[];
        foreach($_POST as $k=>$v)
        {
            if(!preg_match('/q[0-9]/',$k)) continue;
            if(empty($v)) continue;
            $usl[]=$rzd.str_replace('q','(a.question=',$k)." and a.val=$v)";
        }
        return $usl;
    }

    private function getFilterFromPostAdd()
    {
        if (!isset($_POST['suit'])) return '';
        if (empty($_POST['suit'])) return '';
        return "status='".$_POST['suit']."'";
    }

    public function delete($id)
    {
        $rec=AnketaRecord::findOne($id);
        if (!empty($rec->video)) {

            $root = str_replace('backend','frontend',\Yii::getAlias('@webroot'));
            $ds = DIRECTORY_SEPARATOR;
            $file=$root . $ds . 'uploads' .$ds. $rec->video;
            if (file_exists($file))
                unlink($file);
        }
        AnketadetRecord::deleteAll(['anketaid'=>$id]);
        AnketaRecord::deleteAll(['id'=>$id]);
    }
}