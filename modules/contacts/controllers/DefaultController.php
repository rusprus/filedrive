<?php
namespace app\modules\contacts\controllers;

use Yii;
use yii\web\Controller;
use app\modules\contacts\models\PhoneBook;
use app\modules\contacts\models\UploadForm;
use yii\web\UploadedFile;
use yii\data\Pagination;


class DefaultController extends Controller
{
    /**
     * Вывод главнуой страницы модуля
     *
     * @return 
     */
    public function actionIndex()
    {
        $user_id = Yii::$app->user->identity->id;

        $model = new UploadForm();

        $contacts = PhoneBook::find()->where(['user_id' => $user_id])->limit(10)->all();

        $max = PhoneBook::find()->where(['user_id' => $user_id])->count();
        $max = ceil($max / 10);

        return $this->render( 'index', ['contacts' => $contacts, 'model' => $model, 'max' => $max ] );
    }

    /**
     * Действие обновления поля контакта
     *
     * @return 
     */
    public function actionUpdate()
    {
        $user_id = Yii::$app->user->identity->id;

       $id = Yii::$app->request->post('id') ? Yii::$app->request->post('id') : null;
       $field = Yii::$app->request->post('field') ? Yii::$app->request->post('field') : null;
       $value = Yii::$app->request->post('value') ? Yii::$app->request->post('value') : null;

        if( $id && $field && $value ){

            $contact = PhoneBook::find()->where(['user_id' => $user_id, 'id' => $id])->one();
            $contact->$field = $value;
            if( $contact->save() ){

                return true;
            }
            return false;
        }
    }

    
   /**
    * Действие загрузки файла .vcf
    */
    public function actionUpload()
    {

        $model = new UploadForm();

        if (Yii::$app->request->isPost) {

            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');

            if ($model->upload()) {

                return $this->redirect( 'index' );
            }
        }

        return $this->redirect( 'index' );
    }


    /**
    * Действие возврата дополнительного контена при прокруке страницы вниз
    */
    public function actionGetContacts()
    {

        $user_id = Yii::$app->user->identity->id;

        $page = (int)Yii::$app->request->get('page');
        $query = PhoneBook::find()->where(['user_id' => $user_id]);
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(),
                                'pageSize' => 10,
                                'page' => $page ]);

        $contacts = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

            $result = '';

            ob_start();

            foreach ($contacts as $item) {
                ?>
                <tr class='table-data' data-page=<?= $pages->page ?>>  
                        <td class='id'  onclick="alert('!')"><?php echo $item->id; ?></td>    
                        <td class='first_name'><?php echo $item->first_name; ?></td>    
                        <td class='last_name'><?php echo $item->last_name; ?></td>
                        <td class='add_names'><?php echo $item->add_names; ?></td>
                        <td class='tel'><?php echo $item->tel; ?></td>
                    </tr>

                <?php
            }

            $result = ob_get_contents();
            ob_end_clean();

            return $result;
    }


    


}