<?php 
namespace app\models;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class UploadForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $imageFile;
    public $idParent;
    public $nameParent;
    public $pathParent;
    public $userId;

    public function rules()
    {
        return [
            [['imageFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg, txt'],
            [['idParent'], 'integer'],
            [['nameParent'], 'trim'],
            [['pathParent'], 'trim'],
            [['userId'], 'safe'],
            // [['imageFile'], 'skipOnEmpty' => false, 'extensions' => 'png, jpg, txt'],
        ];
    }
    
    public function upload()
    {
        if ($this->validate()) {

            $userId = Yii::$app->user->identity->id;
            $this->imageFile->saveAs('../uploads/'. $userId  . $this->pathParent . '/'. $this->nameParent . '/'. $this->imageFile->baseName . '.' . $this->imageFile->extension);
            return true;
        } else {
            return false;
        }
    }
}