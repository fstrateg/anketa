<?php

namespace frontend\models;

use yii\db\ActiveRecord;

class AnketadetRecord extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%anketa_det}}';
    }
}