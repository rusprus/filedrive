<?php
namespace app\modules\notepad\controllers;
use yii\helpers\ArrayHelper;

use Yii;

use yii\web\Controller;
use app\modules\notepad\models\Note;
use yii\web\Response;
use yii\web\Request;


class NotepadController extends Controller
{

    // Отменяем проверку _csrf. Потому что не знаю как его провалидировать.

    public function beforeAction($action) 
        { 
            $this->enableCsrfValidation = false; 
            return parent::beforeAction($action); 
        }
    /**
     * Вывод главной страницы модуля
     *
     * @return 
     */
    public function actionIndex()
    {

        return $this->render( 'index' );
    }

    /**
     * Обработка запроса на получение всех заметок пользователя
     *
     * @return 
     */
    public function actionGetNotes()
    {


        $notes = Note::getAllNotes();

        return json_encode( $notes ) ;

    }

    /**
     * actionUpdateNote()
     * 
     * Получает JSON. Обновляет запись по ID
     *
     * @return 
     */
    public function actionUpdateNote()
    {

        $newNote = Yii::$app->request->getRawBody();
        $newNote = json_decode( $newNote );

        $oldNote = Note::getNoteById( $newNote->id );

        $oldNote->text = $newNote->text;
        $oldNote->level = $newNote->level;
        $oldNote->top = $newNote->top;
        $oldNote->left = $newNote->left;

        $oldNote->save();

        return true;
    }
    
     /**
     * actionDeleteNote()
     * 
     * Получает JSON. Удаляет запись из БД по ID
     *
     * @return 
     */
    public function actionDeleteNote()
    {

        $newNote = Yii::$app->request->getRawBody();
        $newNote = json_decode( $newNote );

        $oldNote = Note::getNoteById( $newNote->id );

        $oldNote->delete();

        return true;
    }


      /**
     * actionInsertNote()
     * 
     * Добавляем новую заметку в БД по нажатию на кнопку 
     * и возвращаем добавленный обьект в форме json
     *
     * @return 
     */
    public function actionInsertNote()
    {
        // $newNote->id = Yii::$app->user->id;
        $newNote = Yii::$app->request->getRawBody();
        $newNote = json_decode( $newNote );

        $newNoteToDb = new Note();
        $newNoteToDb->user_id = 1;
        $newNoteToDb->text = $newNote->text;
        $newNoteToDb->level = $newNote->level;
        $newNoteToDb->top = $newNote->top;
        $newNoteToDb->left = $newNote->left; 

        $newNoteToDb->save();

        return json_encode(  ArrayHelper::toArray($newNoteToDb ) );
    }

 
}