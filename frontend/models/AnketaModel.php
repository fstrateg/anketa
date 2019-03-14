<?php
namespace frontend\models;

use yii\base\DynamicModel;
use yii\widgets\ActiveForm;
use Yii;


class AnketaModel extends \stdClass
{
    private $questions=null;
    private $addSelf=false;
    public $form=null;


    public function getQuestions()
    {
        if (!$this->questions) {
            $q = QuestRecord::find()->orderBy("tab")->all();
            $this->questions=[];
            foreach($q as $item) {
                $this->questions['field' . $item->id] = $item;
            }
        }
        return $this->questions;
    }

    public function createFormModel()
    {

        $qs=$this->getQuestions();
        $fields=[];
        $ob=[];
        $df=[];
        $dt=[];
        foreach($qs as $k=>$v)
        {
            $fields[]=$k;
            if ($v->ob)
                $ob[]=$k;
            else
                $df[]=$k;
            if ($v->type=='date')
                $dt[]=$k;
            if ($v->type=='list')
            {
                $this->addSelf=false;
                $this->createList($v->list);
                if ($this->addSelf)
                {
                    $fields[]=$k.'00';
                    $df[]=$k.'00';
                }
            }
        }
        $form=new DynamicModel($fields);

        //$form->setAttributes($fields);
        $form->addRule($ob,'required',['message'=>'Пожалуйста ответьте на этот вопрос'])
            ->addRule($dt,'date',['format' => 'php:d.m.Y'])
            ->addRule($df,'string');
        if (Yii::$app->request->isPost) {
            $form->load(Yii::$app->request->post());
        }
        $this->form=$form;
        return $form;
    }

    public function save($anketaid)
    {
        AnketadetRecord::deleteAll(['anketaid'=>$anketaid]);
        //$form=new DynamicModel();
        $form=$this->form;
        foreach($form->attributes as $key=>$vl)
        {
            $rw=new AnketadetRecord();
            $rw->anketaid=$anketaid;
            $key2 = preg_replace("/[^0-9]/", '', $key);
            $rw->setAttribute("question",$key2);
            $rw->setAttribute("val",$vl);
            $rw->save();
        }
    }

    /**
     * @param $form ActiveForm
     */
    public function printFields($form)
    {
        $model=$this->createFormModel();
        foreach($this->questions as $key=>$value)
        {
            $this->addSelf=false;
            $field=$form->field($model,$key);
            switch($value->type)
            {
                case 'area':
                    $field=$form->field($model,$key)->textarea();
                    break;
                case 'date':
                    $form->field($model, $key)->widget(\yii\jui\DatePicker::class, [
                        'language' => 'ru',
                        'dateFormat' => 'dd.MM.yyyy',
                    ]);
                    break;
                case 'list':
                    $field->radioList($this->createList($value->list),['separator'=>'<br/>']);
                    break;
            }
            echo $field->label($value->val);
            if ($this->addSelf)
            {
                echo $form->field($model,$key.'00')->textarea()->label(false);
            }
        }
    }

    private function createList($listid)
    {
        $rez=[];
        $items=ListRecord::find()->where(['list'=>$listid])->orderBy('tab')->all();
        foreach ($items as $item) {
            $rez[$item->id]=$item->val;
            if ($item->self) $this->addSelf=true;
        }
        return $rez;
    }
}