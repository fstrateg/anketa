<?php
namespace frontend\models;

use yii\base\Model;
use Yii;


class AnketaForm extends Model
{
    public $fio;
    public $videoFile;
    public $video;

    public function rules()
    {
        return [
            [['fio'], 'required', 'message'=>'Представьтесь, пожалуйста!'],
            [['videoFile'], 'required', 'message'=>'Нам нужно больше информации о Вас!'],
            [['videoFile'], 'file',
                //'extensions' => 'mp4, avi',
                'mimeTypes' => ['video/mp4','video/x-msvideo','video/3gpp'],
                'wrongMimeType'=>'Разрешена загрузка только файлов формата mp4, avi, 3gp',
                'maxFiles' => 1],
        ];
    }

    public function attributeLabels()
    {
        return
            [
                'fio'=>'Как Вас зовут (ФИО)?',
                'videoFile'=>'Запишите селфи видое: Почему Вы хотите работать с нами?'
            ]
        ;
    }

    public function saveToDb()
    {
        $session=Yii::$app->session;
        $id=$session->getId();
        $rec=AnketaRecord::find()->where(['session'=>$id])->one();
        if (empty($rec))
        {
            $rec=new AnketaRecord();
            $rec->dat=date('y.m.d H:i');
        }
        $rec->session=$id;
        $rec->fio=$this->fio;
        $rec->video = $this->video;
        $rec->save();
    }

    public function uploadfile()
    {
        $session=Yii::$app->session;
        $randomString = $session->getId();
        $fn= preg_replace("/[ |\.]/", '_', $this->translate($this->fio));
        $fn= preg_replace("/\W/","",$fn);
        $this->video=$randomString .'_' . $fn . '.' . $this->videoFile->extension;
        $this->videoFile->saveAs('uploads/' . $this->video);
    }

    public function translate($text)
    {
        $rus = array('А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
        $lat = array('A', 'B', 'V', 'G', 'D', 'E', 'E', 'J', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'Ch', 'Sh', 'Sch', 'Y', 'Y', "'", 'E', 'Yu', 'Ya', 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'j', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', 'y', 'y', "'", 'e', 'yu', 'ya');
        return str_replace($rus, $lat, $text);
    }
}