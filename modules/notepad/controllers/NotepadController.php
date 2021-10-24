<?php
namespace app\modules\notepad\controllers;

use Yii;

use yii\web\Controller;
use app\modules\notepad\models\Note;


class NotepadController extends Controller
{

    public function beforeAction($action) 
{ 
    $this->enableCsrfValidation = false; 
    return parent::beforeAction($action); 
}
    /**
     * Вывод главнуой страницы модуля
     *
     * @return 
     */
    public function actionIndex()
    {

        return $this->render( 'index' );
    }

    /**
     * Вывод главнуой страницы модуля
     *
     * @return 
     */
    public function actionGetNotes()
    {
        // return var_dump('dddddd');
        $notes = Note::getAllNotes();

        return json_encode( $notes ) ;

    }

 
}