<?php

namespace frontend\models;

use yii\db\ActiveRecord;

class AnketaRecord extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%anketa}}';
    }
}