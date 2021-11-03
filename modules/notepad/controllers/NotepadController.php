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
     * Получает JSON. Обновляет запись
     *
     * @return 
     */
    public function actionUpdateNote()
    {

        $newNote = Yii::$app->request->getRawBody();
        $newNote = json_decode( $newNote );

        Note::updateNote( $newNote );

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

        $note = Yii::$app->request->getRawBody();
        $note = json_decode( $note );

        Note::deleteNote( $note->id );

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
        
        $newNote = Yii::$app->request->getRawBody();
        $newNote = json_decode( $newNote );

        $newNoteToDb = Note::insertNote( $newNote );

        return json_encode(  ArrayHelper::toArray($newNoteToDb ) );
    }

 
}