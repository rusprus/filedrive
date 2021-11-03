<?php
namespace app\modules\contacts\models;

use Yii;
use yii\web\Response;
use yii\db\ActiveRecord;

class PhoneBook extends ActiveRecord
{
    
    /**
     * Таблица класса
     * 
     * @return string название таблицы, сопоставленной с этим ActiveRecord-классом.
     */
    public static function tableName()
    {
        return '{{phone_book}}';
    }
}