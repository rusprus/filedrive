<?php
namespace app\modules\notepad\controllers;

use Yii;

use yii\web\Controller;
// use app\modules\contacts\models\PhoneBook;


class NotepadController extends Controller
{
    /**
     * Вывод главнуой страницы модуля
     *
     * @return 
     */
    public function actionIndex()
    {


        return $this->render( 'index' );
    }

 
}