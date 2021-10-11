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
     * Вывод корны файлового менеджера
     *
     * @return string
     */
    public function actionFileman()
    {
        $uploadForm = new UploadForm();
        $newDir = new File();   

         // Ищем в базе папку или файл
        $curId = Yii::$app->request->get('id') ? (int)Yii::$app->request->get('id') : 1;
        $curFile = File::find()->where(['id' => $curId ])->one();
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
     *  Получения списка файлов при переходе в папку
     *
     * @return
     */
    function actionGetListFiles(){
        $id = Yii::$app->request->post('id') ? (int)Yii::$app->request->post('id') : null;
        $file = File::find()->where(['id' => $id ])->one();
        $files = File::find()->where(['parent' => $id])->All();
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
        $id = Yii::$app->request->get('id') ? (int)Yii::$app->request->get('id') : 1;
        $file = File::find()->where(['id' => $id ])->one();
        $path =  $file->path . '/' .  $file->name;
        if( file_exists(  Yii::getAlias('@app').'/uploads/'. $path ) ){

            $path = Yii::getAlias('@app').'/uploads/'. $path ;
            Yii::$app->response->format = Response::FORMAT_RAW;
     
            return  \Yii::$app->response->sendFile($path) ;
        }
        return 'File is upsent';
    }

   /**
     *  Асинхронная обработка переименования файла/папки
     *
     * @return
     */
    public function actionRename(){

        $newname = Yii::$app->request->get('newname') ? Yii::$app->request->get('newname') : null;
        $newname = htmlspecialchars($newname);
        $curId = Yii::$app->request->get('id') ? (int)Yii::$app->request->get('id') : null;

        if( $newname &&  $curId  ){
            $this->renameFile( $curId, $newname);
            return true;
        } 
        return false;
    }

    /**
     *  Удаление файла/папки
     *
     * @return
     */
    public function actionDel(){

        $curId = Yii::$app->request->get('id') ? (int)Yii::$app->request->get('id') : null;

        if( $curId  ){
            $curFile = File::find()
                            ->where(['id' => $curId])
                            ->one();
            if( $curFile->type == 'dir'){
                $curFullPath = $curFile->path . '/' . $curFile->name;
                $childFiles = File::find()
                                    ->where(['like', 'path', $curFullPath]) 
                                    ->all();
                foreach( $childFiles as $item ){
                    $item->delete();
                }
                $this->deleteDir(__DIR__ . '/../uploads'. $curFullPath);
            }
            if( $curFile->type == 'file' ){
                $curFullPath = $curFile->path . '/' . $curFile->name;
                unlink(__DIR__ . '/../uploads'. $curFullPath);
            } 
            $curFile->delete();
            return true;
        } 
        return false;
    }

    /**
     * Добавление папки
     *
     * @return
     */
    public function actionAddDir()
    {

        $name = Yii::$app->request->post('filename');
        $name = htmlspecialchars($name);
        $idParent = (int)Yii::$app->request->post('id');
        
        $dir = File::find()->where(['id'=>$idParent])->one();
        $nameParent = $dir->name ;
        $pathParent = $dir->path ;

        $newDir = new File();
        $newDir->name = $name;
        $newDir->path = $pathParent . '/' . $nameParent ;
        $newDir->type = 'dir';
        $newDir->size = 1000;
        $newDir->parent = $idParent;
  
        $newDir->save();
        mkdir( '../uploads' . $newDir->path . '/'. $newDir->name);

    }

    /**
     * Загрузка файла
     * 
     */
    public function actionUploadFile(){
        
        $uploadForm = new UploadForm();

        if (Yii::$app->request->isPost) { 

            $uploadForm->imageFile = UploadedFile::getInstance($uploadForm, 'imageFile');
            $uploadForm->idParent = (int)Yii::$app->request->post('UploadForm')['idParent'];

            $parenDir = File::find()->where(['id' => $uploadForm->idParent ])->one();

            $uploadForm->nameParent = $parenDir->name;
            $uploadForm->pathParent = $parenDir->path;
            
            if ($uploadForm->upload()) {
                $newFile = new File();
                $newFile->name = $uploadForm->imageFile->baseName . '.' . $uploadForm->imageFile->extension;
                $newFile->path =   $parenDir->path . '/' . $parenDir->name ;
                $newFile->type = 'file';
                $newFile->size = 1000;
                $newFile->parent = $parenDir->id;
                $newFile->save();
                return $this->redirect(Yii::$app->request->referrer);
            }
        }
    }

    /**
     *  Функция удаления  файла/папки и всего содержимого
     *
     * @return
     */
    public static function deleteDir($dirPath) {
        if (! is_dir($dirPath)) {
            throw new InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }

      /**
     *  Функция переименования   файла/папки
     *
     * @return
     */
    public function renameFile( $curId, $newname){
        
        $curFile = File::find()->where(['id' => $curId ])->one();

        rename(Yii::getAlias('@app').'/uploads'. $curFile->path . '/' . $curFile->name,  Yii::getAlias('@app').'/uploads' . $curFile->path . '/' . $newname);

        $oldPath = $curFile->path . '/' . $curFile->name;
        $curFile->name = $newname;
        $newPath = $curFile->path . '/' . $curFile->name;
        $files = new File();
        $result = $files->find()
                       ->from('files')
                        ->where(['like', 'path', $oldPath]) 
                        ->all();
        foreach(  $result as $item ){
            $item->path =  preg_replace( "#".$oldPath."#", $newPath, $item->path);
            $item->save();
        }
        $curFile->save();
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