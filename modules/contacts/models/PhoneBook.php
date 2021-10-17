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

    // public function getContacts(){

    //         $queryActive = $contact->find()->where([':first_name' => $first_name,
    //                                                 ':last_name' => $last_name,
    //                                                 ':add_names' => $add_names,
    //                                                 ':tel' => $tel]);
    //         $command = Yii::$app->db->createCommand('SELECT * FROM post WHERE id=:id')
    //           ->bindParam(':id', $id);

    //         $id = 1;
    //         $post1 = $command->queryOne();

    //         $id = 2;
    //         $post2 = $command->queryOne();

    // }
//     /**
//      *  Связь с таблицой пользователей
//      *
//      * @return
//      */
//     public function getUser()
//     {
//         return $this->hasOne(User::class, ['id' => 'user_id'])->inverseOf('file');
//     }

//     /**
//      *  Удаление  файла/папки из базы и ФС по id
//      *
//      * @return
//      */
//     public function deleteFileById( $curId ){

//         $userId = Yii::$app->user->identity->id;
//         $curFile = File::find()
//                         ->where(['id' => $curId, 'user_id' => $userId ])
//                         ->one();

//         if( $curFile->type == 'dir'){
//             $curFullPath = $curFile->path . '/' . $curFile->name;
//             $childFiles = File::find()
//                                 ->where(['like', 'path', $curFullPath]) 
//                                 ->andWhere([ 'user_id' => $userId ])
//                                 ->all();

//             foreach( $childFiles as $item ){
//                 $item->delete();
//             }
//             $curFile->deleteDir(__DIR__ . '/../uploads/' . $userId . $curFullPath);
//         }
//         if( $curFile->type == 'file' ){
//             $curFullPath = $curFile->path . '/' . $curFile->name;
//             unlink(__DIR__ . '/../uploads/' . $userId . $curFullPath);
//         } 
//         $curFile->delete();
//         return true;
//     }

//     /**
//      *  Функция рекурсивного удаления  папки  из ФС
//       *
//      * @return
//      */
//     public static function deleteDir( $dirPath ) {

//         if (! is_dir($dirPath)) {
//             throw new InvalidArgumentException("$dirPath must be a directory");
//         }
//         if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
//             $dirPath .= '/';
//         }
//         $files = glob($dirPath . '*', GLOB_MARK);
//         foreach ($files as $file) {
//             if (is_dir($file)) {
//                 self::deleteDir($file);
//             } else {
//                 unlink($file);
//             }
//         }
//         rmdir($dirPath);
//     }

//     /**
//      *  Добавление папки в родительскую директорию
//      *
//      * @return
//      */
//     public function addDirByParentId(  $idParent, $name  ){

//         $userId = Yii::$app->user->identity->id;
//         $name = htmlspecialchars($name);

//         $parent = File::find()->where(['id'=>$idParent, 'user_id' => $userId ])->one();

//         $newDir = new File();
//         $newDir->name = $name;
//         $newDir->path = $parent->path . '/' . $parent->name ;
//         $newDir->type = 'dir';
//         $newDir->size = 1000;
//         $newDir->parent = $idParent;
//         $newDir->user_id = $userId;
//         $newDir->save();

//         mkdir( '../uploads/' . $newDir->user_id . $newDir->path . '/'. $newDir->name);

//         return true;
//     }

//     /**
//      *  Скачивание файла по id
//      *
//      * @return
//      */
//     public function downloadFileById(  $id  ){

//         $userId = Yii::$app->user->identity->id;

//         $file = File::find()->where(['id' => $id, 'user_id' => $userId ])->one();
//         $path = $userId . $file->path . '/' .  $file->name;
//         if( file_exists(  Yii::getAlias('@app').'/uploads/'.  $path ) ){

//             $path = Yii::getAlias('@app').'/uploads/'. $path ;
//             Yii::$app->response->format = Response::FORMAT_RAW;
     
//             return  \Yii::$app->response->sendFile($path) ;
//         }
//         return 'File is upsent';
//     }

//   /**
//      *  Функция переименования   файла/папки по id
//      *
//      * @return
//      */
//     public function renameFileById( $curId, $newname){

//         $newname = htmlspecialchars($newname);
//         $userId = Yii::$app->user->identity->id;
//         $curFile = File::find()->where(['id' => $curId, 'user_id' => $userId ])->one();


//         rename(Yii::getAlias('@app').'/uploads/'. $curFile->user_id . $curFile->path . '/' . $curFile->name,  Yii::getAlias('@app').'/uploads/' . $curFile->user_id  . $curFile->path . '/' . $newname);

//         $oldPath = $curFile->path . '/' . $curFile->name;
//         $curFile->name = $newname;
//         $newPath = $curFile->path . '/' . $curFile->name;
//         $files = new File();
//         $result = $files->find()
//                         ->from('files')
//                         ->where(['like', 'path', $oldPath]) 
//                         ->andWhere([ 'user_id' => $userId ])
//                         ->all();
//         foreach(  $result as $item ){
//             $item->path =  preg_replace( "#".$oldPath."#", $newPath, $item->path);
//             $item->save();
//         }
//         $curFile->save();
//   }

//     /**
//      * Получить файл по Id
//      *
//      * @return
//      */
//     public function getFileById( $curId ){

//         $userId = Yii::$app->user->identity->id;
//         $curFile = File::find()->where(['id' => $curId , 'user_id' => $userId ])->one();

//         return $curFile;
//     }

//     /**
//      * Получить содержимое по id папки
//      *
//      * @return
//      */
//     public function getFilesByParentId( $parentId ){

//         $userId = Yii::$app->user->identity->id;
//         $files = File::find()->where(['parent' => $parentId, 'user_id' => $userId])->All();

//         return $files;
//     }
//     /**
//      * Получить коневую папку пользователя
//      *
//      * @return
//      */
//     public function getUserRoot( $curId ){

//         $userId = Yii::$app->user->identity->id;
//         $curFile = File::find()->where(['path' => '' , 'user_id' => $userId ])->one();

//         return $curFile;
        
//     }
}