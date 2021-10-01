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
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
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
     * Действие  файлового менеджера
     *
     * @return string
     */
    public function actionFileman()
    {
        $uploadForm = new UploadForm();
        $newDir = new File();   

        // Обработка Get-запроса
        if( Yii::$app->request->isGet ) {
            // Ищем в базе папку или файл
            $curId = Yii::$app->request->get('id') ? Yii::$app->request->get('id') : 1;
            $curFile = File::find()->where(['id' => $curId ])->one();
            
            // Если файл, то скачать 
            if( $curFile->type == 'file' ){
                $this->downloadFile( $curFile->name );
            }
        }else{
            // Папка по умолчанию, если в базе нет
            $curFile = File::find()->where(['id' => 1])->one();
            }
        // Список содержимого папки
        $files = File::find()->where(['parent' => $curFile->id])->All();

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
     *  Установка хлебных крошек
     *
     * @return
     */
    public function setBreadcrumbs( $curFile ){
 
        $session = Yii::$app->session;
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
        $breadcrumbs[] = ['id' => $curFile->id, 'label' => $curFile->name, 'url' => ['/fileman?id='. $curFile->id . '&type='.$curFile->type]];

        $this->view->params['breadcrumbs'] = $breadcrumbs;
        $session->set('breadcrumbs', $breadcrumbs);

    }

     /**
     *  Скачивание файла
     *
     * @return
     */
    public function downloadFile($filename)
    {
        if( file_exists(  Yii::getAlias('@app').'/uploads/'. $filename ) ){
            $path = Yii::getAlias('@app').'/uploads/'. $filename ;

            return \Yii::$app->response->sendFile($path);
        }
        return $this->goBack();
    }

    /**
     * Добавление папки
     *
     * @return
     */
    public function actionAddDir()
    {

        $input_name = Yii::$app->request->post('File')['name'];
        $input_name = strip_tags($input_name);
        $input_name = htmlspecialchars($input_name);
        // $input_name = mysql_escape_string($input_name);

        $input_parent = (int)Yii::$app->request->post('File')['parent'];

        $newFile = new File();
        $newFile->name = $input_name;
        $newFile->path = '../uploads/' . $input_name;
        $newFile->type = 'dir';
        $newFile->size = 1000;
        $newFile->parent = $input_parent;
        $newFile->save();
        mkdir($newFile->path);
        return true; 
    }

    /**
     * Загрузка файла
     * 
     */
    public function actionUploadFile(){
        
        $uploadForm = new UploadForm();

        if (Yii::$app->request->isPost) { 

            $uploadForm->imageFile = UploadedFile::getInstance($uploadForm, 'imageFile');
            $uploadForm->parent = Yii::$app->request->post('UploadForm')['parent'];
            
            if ($uploadForm->upload()) {
                $newFile = new File();
                $newFile->name = $uploadForm->imageFile->baseName . '.' . $uploadForm->imageFile->extension;
                $newFile->path = '../uploads/' . $newFile->name;
                $newFile->type = 'file';
                $newFile->size = 1000;
                $newFile->parent = $uploadForm->parent;
                $newFile->save();
                return $this->redirect(Yii::$app->request->referrer);
            }
        }
    }

    
}