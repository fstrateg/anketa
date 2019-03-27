<?php
namespace backend\models;

use Yii;
use yii\base\Model;
use frontend\models\AnketaRecord;

class NotesModel extends Model
{
    public $status;
    public $notes;
    public $id;

    public function rules()
    {
        return [
            [['status', 'notes'], 'string'],
            [['id'],'integer']
        ];
    }

    public function attributeLabels()
    {
        return
            [
                'status'=>'Статус',
                'notes'=>'Примечание HR',
            ];
    }

    public function initVl($id)
    {
        $rec=AnketaRecord::find()->where(["id"=>$id])->one();
        $this->status=$rec->status;
        $this->notes = $rec->note;
        $this->id = $id;

    }

    public function getStatusList()
    {
        return [
            "Да"=>"Да",
            "Нет"=>"Нет",
            "Резерв"=>"Резерв",
            "Уже работает"=>"Уже работает",
        ];
    }

    public function save($val)
    {
        $rec=AnketaRecord::find()->where(["id"=>$val['id']])->one();
        if (!$rec) return;
        $rec->status=$val['status'];
        $rec->note=$val['notes'];
        $rec->save();
    }

}