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

            $newname = Yii::$app->request->get('newname') ? Yii::$app->request->get('newname') : null;
            $curId = Yii::$app->request->get('id') ? Yii::$app->request->get('id') : null;

            if( $newname && $curId ){
                renameFile( $curId, $newname);
           }

            // Ищем в базе папку или файл
            $curId = Yii::$app->request->get('id') ? Yii::$app->request->get('id') : 1;
            $curFile = File::find()->where(['id' => $curId ])->one();
            
            // Если файл, то скачать 
            if( $curFile->type == 'file' ){
                $this->downloadFile( $curFile->path . '/' .  $curFile->name);
            }
        }else{
            // Папка по умолчанию, если файла нет в базе
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
     *  Асинхронная обработка переименования файла/папки
     *
     * @return
     */
    public function actionRename(){

        $newname = Yii::$app->request->get('newname') ? Yii::$app->request->get('newname') : null;
        $curId = Yii::$app->request->get('id') ? Yii::$app->request->get('id') : null;

        if( $newname &&  $curId  ){
            $this->renameFile( $curId, $newname);
            return true;
        } 
        return false;
    }

    // SELECT `id`,`path` FROM `files` WHERE `path` LIKE '/Хранилище/Китай%'
    /**
     *  Удаление файла/папки
     *
     * @return
     */
    public function actionDel(){
        $curId = Yii::$app->request->get('id') ? Yii::$app->request->get('id') : null;

        if( $curId  ){
   
            $curFile = File::find()
                            ->where(['id' => $curId])
                            ->one();

            $curFullPath = $curFile->path . '/' . $curFile->name;

            $childFiles = File::find()
                                ->where(['like', 'path', $curFullPath]) 
                                // ->where(['path'=> '/Хранилище/Китай']) 
                                ->all();
            // var_dump(  $childFiles );die;

            foreach( $childFiles as $item ){
                $item->delete();

            }


            if( $curFile->type == 'dir' ) $this->deleteDir(__DIR__ . '/../uploads/'. $curFullPath);
            if( $curFile->type == 'file' ) unlink(__DIR__ . '/../uploads/'. $curFile->path);

            $curFile->delete();

            return true;

        } 
        return false;
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


        //   var_dump( '$oldPath: ' . $oldPath . '; ' . ' $newPath: ' . $newPath );
        //   die;

        //   $files = (new \yii\db\Query());
          $files = new File();
        //   $result = $files->select('files.id, files.path')
         $result = $files->find()
                         ->from('files')
                          ->where(['like', 'path', $oldPath]) 
                          // ->where(['path'=> '/Хранилище/Китай']) 
                          ->all();
        //   var_dump($result);

          foreach(  $result as $item ){
              $item->path =  preg_replace( "#".$oldPath."#", $newPath, $item->path);
              $item->save();
          }
          // $files = new File();
          // $result = $files->find('id, path')
          //                 // ->from('files')
          //                 ->where(['like', 'path', '/Хранилище/Китай']) 
          //                 ->asArray()
          //                 // ->where(['path'=> '/Хранилище/Китай']) 
          //                 ->all();


        //   $pattern = '#a#';
        //   $replasment = 'b';
        //   $str = 'adddddd';
        //   $str = preg_replace( $pattern, $replasment, $str);
        //   echo  $str;

        // var_dump($result);
        //   die;
          $curFile->save();


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

        $name = Yii::$app->request->post('File')['name'];
        $idParent = (int)Yii::$app->request->post('File')['idParent'];
        $dir = File::find()->where(['id'=>$idParent])->one();
        $nameParent = $dir->name ;
        $pathParent = $dir->path ;
        // $nameParent = Yii::$app->request->post('File')['nameParent'];
        // $pathParent = Yii::$app->request->post('File')['pathParent'];

        $newDir = new File();
        $newDir->name = $name;
        $newDir->path = $pathParent . '/' . $nameParent ;
        $newDir->type = 'dir';
        $newDir->size = 1000;
        $newDir->parent = $idParent;
        // var_dump ( "name: ". $name.";  idParent: ". $idParent."; nameParent: ". $nameParent."; pathParent: ". $pathParent );
        // die;        
        // var_dump (  $newDir->path . '/'. $newDir->name);
        // var_dump ( $newDir );
        // die;
        $newDir->save();
        // var_dump('../uploads' . $newDir->path . '/'. $newDir->name);
        mkdir( '../uploads' . $newDir->path . '/'. $newDir->name);
        // var_dump($newDir->path);die;

        // return true; 
    }

    /**
     * Загрузка файла
     * 
     */
    public function actionUploadFile(){
        
        $uploadForm = new UploadForm();

        if (Yii::$app->request->isPost) { 

            $uploadForm->imageFile = UploadedFile::getInstance($uploadForm, 'imageFile');
            $uploadForm->idParent = Yii::$app->request->post('UploadForm')['idParent'];
            // $uploadForm->nameParent = Yii::$app->request->post('UploadForm')['nameParent'];

            $parenDir = File::find()->where(['id' => $uploadForm->idParent ])->one();

            $uploadForm->nameParent = $parenDir->name;
            $uploadForm->pathParent = $parenDir->path;
            
            if ($uploadForm->upload()) {
                $newFile = new File();
                $newFile->name = $uploadForm->imageFile->baseName . '.' . $uploadForm->imageFile->extension;
                // $newFile->path = '../uploads/' . $newFile->name;
                $newFile->path =   $parenDir->path . '/' . $parenDir->name ;
                // var_dump($newFile->path);die;
                $newFile->type = 'file';
                $newFile->size = 1000;
                $newFile->parent = $parenDir->id;
                // var_dump($newFile);
                $newFile->save();
            // var_dump('ddddddddddddddd');die;

                return $this->redirect(Yii::$app->request->referrer);
            }
        }
    }

    
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
    
}