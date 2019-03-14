<?php

namespace frontend\models;

use yii\db\ActiveRecord;

class QuestRecord extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%quest}}';
    }
}