<?php
namespace app\modules\notepad\models;

use Yii;
use yii\web\Response;
use yii\db\ActiveRecord;
use yii\behaviors\AttributeTypecastBehavior;

class Note extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    
    /**
     * Таблица класса
     * 
     * @return string название таблицы, сопоставленной с этим ActiveRecord-классом.
     */
    public static function tableName()
    {
        return '{{notes}}';
    }


    /**
     * Правила проверки атрибутов модели
     * 
     * @return 
     */
    public function rules()
    {
        return [
            
            [['text', 'level', 'top', 'left', 'user_id'], 'required'],

        ];
    }

    /**
     * Получить все заметки  виде массива
     *
     * @return
     */
    public function getAllNotes(){

        $userId = Yii::$app->user->identity->id;

        $notes = Note::find()->where(['user_id' => $userId])->asArray()->all();   

        // Кастыль для конвертации формата string в int. 
        // Т.к. Yii читает данные с БД int unsigne как string
        for( $i = 0 ; $i < count($notes); $i++ ){

                $notes[$i]['id'] =  (int)$notes[$i]['id'];
                $notes[$i]['level'] = (int)$notes[$i]['level'];
                $notes[$i]['top'] = (int) $notes[$i]['top'];
                $notes[$i]['left'] = (int) $notes[$i]['left']; 
                $notes[$i]['user_id'] = (int) $notes[$i]['user_id']; 
        }

        return $notes;
    }

    /**
     * Получить заметку по id
     *
     * @return
     */
    public function getNoteById( $id ){

        $id = (int)$id;
        $userId = Yii::$app->user->identity->id;

        $note = Note::find()->where(['id' => $id, 'user_id' => $userId])->one();   
        
        return $note;
    }

      /**
     * Обновить заметку
     *
     * @return
     */
    public function updateNote( $newNote ){

        $oldNote = Note::getNoteById( $newNote->id );

        $oldNote->text = $newNote->text;
        $oldNote->level = $newNote->level;
        $oldNote->top = $newNote->top;
        $oldNote->left = $newNote->left;

        if ($oldNote->validate()) {

             $oldNote->save();

            return true;
        } else {

            $errors = $oldNote->errors;
            return false;
        }
    }


    /**
     * Вставить заметку
     *
     * @return
     */
    public function insertNote( $newNote ){

        $newNoteToDb = new Note();
        $newNoteToDb->user_id = Yii::$app->user->identity->id;;
        $newNoteToDb->text = $newNote->text;
        $newNoteToDb->level = $newNote->level;
        $newNoteToDb->top = $newNote->top;
        $newNoteToDb->left = $newNote->left; 

        if ($newNoteToDb->validate()) {

            $newNoteToDb->save();

            return $newNoteToDb;

        } else {

            $errors = $newNoteToDb->errors;
            return false;
        }
    }

    /**
     * Вставить заметку
     *
     * @return
     */
    public function deleteNote( $id ){

        $id = (int)$id;

        $note = Note::getNoteById( $id );

        $note->delete();

        return true;
    }

}