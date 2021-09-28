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

class SiteController extends Controller
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
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {

        return $this->render('about');
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionFileman()
    {
        $session = Yii::$app->session;

        $model = new UploadForm();
        $newDir = new File();   

        // Загрузка файла
        if (Yii::$app->request->isPost) { 
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            $model->parent = Yii::$app->request->post('UploadForm')['parent'];
            
            if ($model->upload()) {
                $newFile = new File();
                $newFile->name = $model->imageFile->baseName . '.' . $model->imageFile->extension;
                $newFile->path = '..uploads/' . $newFile->name;
                $newFile->type = 'file';
                $newFile->size = 1000;
                $newFile->parent = $model->parent;
                $newFile->save();
                return $this->redirect(Yii::$app->request->referrer);
            }
        }
        // *******************************************************************************
        // Обработка Get-запросов
        if( Yii::$app->request->isGet ) {
            $parent = Yii::$app->request->get('id') ? Yii::$app->request->get('id') : 1;
            $type = Yii::$app->request->get('type') ? Yii::$app->request->get('type'): null;

            // Клик по папке - переход
            if( $parent && ($type == 'dir')){

                $breadcrumbs = $session->get('breadcrumbs');
                // $breadcrumbs[] = ['label' => 'Хранилище', 'url' => ['/fileman']];
                $breadcrumbs[] = ['label' => $parent, 'url' => ['/fileman?id='. $parent]];
                $this->view->params['breadcrumbs'] = $breadcrumbs;
                $session->set('breadcrumbs', $breadcrumbs);


                $files = File::find()->where(['parent'=>$parent])->All();
                return $this->render('index', ['files'=> $files, 
                                                'model'=>$model, 
                                                'newDir'=>$newDir,  
                                                'parent'=> $parent,
                                                // 'breadcrumbs'=>$breadcrumbs,
                                             ]);
            }
            // Клик по файлу - скачиваем 
            if( $type == 'file' ){

                $breadcrumbs[] = ['label' => 'Хранилище', 'url' => ['/fileman']];
                $breadcrumbs[] = ['label' => $parent, 'url' => ['/fileman']];

                $filename = \Yii::$app->request->get('filename');
                $this->downloadFile( $filename );
            //    return "Work";
                return $this->render('index', ['files'=> $files, 'model'=>$model, 'newDir'=>$newDir,  'parent'=> $parent, 'breadcrumbs'=>$breadcrumbs ]);
            }
        }
        
        $breadcrumbs[] = ['label' => 'Хранилище', 'url' => ['/fileman']];
        // $breadcrumbs[] = ['label' => $parent, 'url' => ['/fileman']];
        // Если без запросов
        $files = File::find()->where(['parent'=>1])->All();
        return $this->render('index', ['files'=> $files, 'model'=>$model, 'newDir'=>$newDir, 'parent'=> 1, 'breadcrumbs'=>$breadcrumbs]);
    }


   


     /**
     * downloadFile function. Скачиване файла
     *
     * @return
     */
    public function downloadFile($filename)
    {
        
        if( file_exists(  Yii::getAlias('@app').'../uploads/'. $filename ) ){
            $path = Yii::getAlias('@app').'../uploads/'. $filename ;
            return \Yii::$app->response->sendFile($path);
        }
        return $this->goBack();
    }

    /**
     * AddDir action. Добавление папки
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
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
