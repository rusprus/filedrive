<?php
namespace app\models;

use yii\db\ActiveRecord;

class File extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    
    /**
     * @return string название таблицы, сопоставленной с этим ActiveRecord-классом.
     */
    public static function tableName()
    {
        return '{{files}}';
    }


    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id'])->inverseOf('file');
    }
}