<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\File;
use app\models\User;


use app\models\UploadForm;
use yii\web\UploadedFile;
use yii\web\UrlManager;

class FilemanController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'fileman'],
                'rules' => [
                    [
                        'actions' => ['logout', 'fileman'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Рендер файлового менеджера.
     *
     * @return string
     */
    public function actionIndex()
    {

        return $this->render('index');
    }

    /**
     * Вывод корны файлового менеджера
     *
     * @return string
     */
    public function actionFileman()
    {
        $uploadForm = new UploadForm();
        $newDir = new File();   

         // Ищем в базе папку или файл
        if( !$curId = Yii::$app->request->get('id') ){

            $curFile = File::getUserRoot( $curId );

        }else{

            $curFile = File::getFileById( $curId );

        } 
       
        $files = File::getFilesByParentid( $curFile->id );

        if( $curFile->type == 'dir'){
            $this->setBreadcrumbs( $curFile );  
        }

        return $this->render('index', ['files'=> $files, 
                                        'uploadForm'=>$uploadForm, 
                                        'newDir'=>$newDir, 
                                        'curFile'=>$curFile,
                                        ]
                            );
    }

    /**
     *  Получения списка файлов при переходе в папку
     *
     * @return
     */
    function actionGetListFiles(){

        $id = Yii::$app->request->post('id') ? (int)Yii::$app->request->post('id') : null;

        $file = File::getFileById( $id );
        $files = File::getFilesByParentId( $id );

        $this->setBreadcrumbs( $file );

        $body = $this->renderPartial('files-block.php', ['files'=>$files], true);
        $breadcrumbs = Yii::$app->session->get('breadcrumbs');
        $arr = [ $breadcrumbs,  $body];
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $arr;
    }

    /**
     *  Скачивание файла
     *
     * @return
     */
    function actionDownloadFile()
    {   
        $id = Yii::$app->request->get('id') ? (int)Yii::$app->request->get('id') : null;

        return $id ? File::downloadFileById( $id ) : false;
    }

   /**
     *  Асинхронная обработка переименования файла/папки
     *
     * @return
     */
    public function actionRename(){

        $newname = Yii::$app->request->get('newname') ? Yii::$app->request->get('newname') : null;
        $curId = Yii::$app->request->get('id') ? (int)Yii::$app->request->get('id') : null;

        $newname && $curId ? File::renameFileById( $curId, $newname) : false;
    }

    /**
     *  Асинхронное удаление файла/папки
     *
     * @return
     */
    public function actionDel(){

        $curId = Yii::$app->request->post('id') ? (int)Yii::$app->request->post('id') : null;
      
        return $curId ? File::deleteFileById( $curId ) :  false;
    }

    /**
     * Асинхронное добавление папки
     *
     * @return
     */
    public function actionAddDir()
    {
        $name = Yii::$app->request->post('filename');
        $idParent = (int)Yii::$app->request->post('id');

        return $name && $idParent ?  File::addDirByParentId( $idParent, $name ) :  false;
    }

    /**
     * Загрузка файла
     * 
     */
    public function actionUploadFile(){
        
        $uploadForm = new UploadForm();

        if (Yii::$app->request->isPost) { 
            $userId = Yii::$app->user->identity->id;
            

            $uploadForm->imageFile = UploadedFile::getInstance($uploadForm, 'imageFile');
            $uploadForm->idParent = (int)Yii::$app->request->post('UploadForm')['idParent'];

            $parenDir = File::find()->where(['id' => $uploadForm->idParent, 'user_id' => $userId ])->one();

            $uploadForm->nameParent = $parenDir->name;
            $uploadForm->pathParent = $parenDir->path;
            
            if ($uploadForm->upload()) {
                $newFile = new File();
                $newFile->name = $uploadForm->imageFile->baseName . '.' . $uploadForm->imageFile->extension;
                $newFile->path =   $parenDir->path . '/' . $parenDir->name ;
                $newFile->type = 'file';
                $newFile->size = 1000;
                $newFile->parent = $parenDir->id;
                $newFile->user_id = $userId;
                $newFile->save();
                return $this->redirect(Yii::$app->request->referrer);
            }
        }
    }

   /**
     *  Функция установки хлебных крошек
     *
     * @return
     */
    public function setBreadcrumbs( $curFile ){

        $session = Yii::$app->session;
        // $session->set('breadcrumbs', []);
        $breadcrumbs = $session->get('breadcrumbs');
        $breadcrumbs = $breadcrumbs ? $breadcrumbs : [];
        // Убираем лишние ссылки при возврате на папку выше
        foreach($breadcrumbs as $key => $breadcrumb){
            if ($breadcrumb['id'] == $curFile->id){
                $breadcrumbs =  array_slice($breadcrumbs,0, $key);
                break;
            }
        }
        // Добавляем папку в которую вошли
        $breadcrumbs[] = ['id' => $curFile->id,'data-id' => $curFile->id, 'label' => $curFile->name, 'url' => ['/fileman']];
        $this->view->params['breadcrumbs'] = $breadcrumbs;
        $session->set('breadcrumbs', $breadcrumbs);
    }
    
}