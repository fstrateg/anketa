<?php

namespace frontend\models;

use yii\db\ActiveRecord;

class ListRecord extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%list}}';
    }
}