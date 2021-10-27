<?php
namespace app\modules\notepad\controllers;

use Yii;

use yii\web\Controller;
use app\modules\notepad\models\Note;
use yii\web\Response;
use yii\web\Request;


class NotepadController extends Controller
{

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
     * 
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

        // return var_dump( $oldNote );
        return true;


    }

    

 
}