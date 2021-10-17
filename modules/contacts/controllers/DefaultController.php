<?php
namespace app\modules\contacts\controllers;

use Yii;

use yii\web\Controller;
use app\modules\contacts\models\PhoneBook;
use app\modules\contacts\models\UploadForm;
use yii\web\UploadedFile;


class DefaultController extends Controller
{
    /**
     * 
     *
     * @return 
     */
    public function actionIndex()
    {
        $model = new UploadForm();

        $contacts = PhoneBook::find()->limit(50)->all();

        return $this->render( 'index', ['contacts' => $contacts, 'model' => $model ] );
    }

    /**
     * 
     *
     * @return 
     */
    public function actionUpdate()
    {

       $id = Yii::$app->request->post('id') ? Yii::$app->request->post('id') : null;
       $field = Yii::$app->request->post('field') ? Yii::$app->request->post('field') : null;
       $value = Yii::$app->request->post('value') ? Yii::$app->request->post('value') : null;

        if( $id && $field && $value ){

            $contact = PhoneBook::findOne( $id );
            $contact->$field = $value;
            if( $contact->save() ){

                return true;
            }
            return false;
        }
    }

    
   /**
    * 
    */
    public function actionUpload()
    {

        $model = new UploadForm();

        if (Yii::$app->request->isPost) {

            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');

            if ($model->upload()) {
                // file is uploaded successfully

                return $this->redirect( 'index' );
            }
        }


        return $this->redirect( 'index' );

    }

   


}