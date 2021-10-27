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
     * Получить все заметки  виде массива
     *
     * @return
     */
    public function getAllNotes(){

        // $userId = Yii::$app->user->identity->id;
        $userId = 1;
        $notes = Note::find()->where(['user_id' => $userId])->asArray()->all();   
        // $notes = Note::find()->asArray()->all();

        // Кастыль для конвертации формата string в int. Т.к. Yii читает данные с БД int unsigne как string
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
    public function getNoteById( $id){

        // $userId = Yii::$app->user->identity->id;
        $userId = 1;
        $note = Note::find()->where(['id' => $id, 'user_id' => $userId])->one();   
        
        return $note;
    }

    /**
     *  Удаление  файла/папки из базы и ФС по id
     *
     * @return
     */
    public function deleteFileById( $curId ){

        $userId = Yii::$app->user->identity->id;
        $curFile = File::find()
                        ->where(['id' => $curId, 'user_id' => $userId ])
                        ->one();

        if( $curFile->type == 'dir'){
            $curFullPath = $curFile->path . '/' . $curFile->name;
            $childFiles = File::find()
                                ->where(['like', 'path', $curFullPath]) 
                                ->andWhere([ 'user_id' => $userId ])
                                ->all();

            foreach( $childFiles as $item ){
                $item->delete();
            }
            $curFile->deleteDir(__DIR__ . '/../uploads/' . $userId . $curFullPath);
        }
        if( $curFile->type == 'file' ){
            $curFullPath = $curFile->path . '/' . $curFile->name;
            unlink(__DIR__ . '/../uploads/' . $userId . $curFullPath);
        } 
        $curFile->delete();
        return true;
    }

    /**
     *  Функция рекурсивного удаления  папки  из ФС
      *
     * @return
     */
    public static function deleteDir( $dirPath ) {

        if (! is_dir($dirPath)) {
            throw new InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }

    /**
     *  Добавление папки в родительскую директорию
     *
     * @return
     */
    public function addDirByParentId(  $idParent, $name  ){

        $userId = Yii::$app->user->identity->id;
        $name = htmlspecialchars($name);

        $parent = File::find()->where(['id'=>$idParent, 'user_id' => $userId ])->one();

        $newDir = new File();
        $newDir->name = $name;
        $newDir->path = $parent->path . '/' . $parent->name ;
        $newDir->type = 'dir';
        $newDir->size = 1000;
        $newDir->parent = $idParent;
        $newDir->user_id = $userId;
        $newDir->save();

        mkdir( '../uploads/' . $newDir->user_id . $newDir->path . '/'. $newDir->name);

        return true;
    }

    /**
     *  Скачивание файла по id
     *
     * @return
     */
    public function downloadFileById(  $id  ){

        $userId = Yii::$app->user->identity->id;

        $file = File::find()->where(['id' => $id, 'user_id' => $userId ])->one();
        $path = $userId . $file->path . '/' .  $file->name;
        if( file_exists(  Yii::getAlias('@app').'/uploads/'.  $path ) ){

            $path = Yii::getAlias('@app').'/uploads/'. $path ;
            Yii::$app->response->format = Response::FORMAT_RAW;
     
            return  \Yii::$app->response->sendFile($path) ;
        }
        return 'File is upsent';
    }

  /**
     *  Функция переименования   файла/папки по id
     *
     * @return
     */
    public function renameFileById( $curId, $newname){

        $newname = htmlspecialchars($newname);
        $userId = Yii::$app->user->identity->id;
        $curFile = File::find()->where(['id' => $curId, 'user_id' => $userId ])->one();


        rename(Yii::getAlias('@app').'/uploads/'. $curFile->user_id . $curFile->path . '/' . $curFile->name,  Yii::getAlias('@app').'/uploads/' . $curFile->user_id  . $curFile->path . '/' . $newname);

        $oldPath = $curFile->path . '/' . $curFile->name;
        $curFile->name = $newname;
        $newPath = $curFile->path . '/' . $curFile->name;
        $files = new File();
        $result = $files->find()
                        ->from('files')
                        ->where(['like', 'path', $oldPath]) 
                        ->andWhere([ 'user_id' => $userId ])
                        ->all();
        foreach(  $result as $item ){
            $item->path =  preg_replace( "#".$oldPath."#", $newPath, $item->path);
            $item->save();
        }
        $curFile->save();
  }

    /**
     * Получить файл по Id
     *
     * @return
     */
    public function getFileById( $curId ){

        $userId = Yii::$app->user->identity->id;
        $curFile = File::find()->where(['id' => $curId , 'user_id' => $userId ])->one();

        return $curFile;
    }


    /**
     * Получить коневую папку пользователя
     *
     * @return
     */
    public function getUserRoot( $curId ){

        $userId = Yii::$app->user->identity->id;
        $curFile = File::find()->where(['path' => '' , 'user_id' => $userId ])->one();

        return $curFile;
        
    }
}