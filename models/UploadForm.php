<?php 
namespace app\models;

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

    public function rules()
    {
        return [
            [['imageFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg, txt'],
            [['idParent'], 'integer'],
            [['nameParent'], 'trim'],
            [['pathParent'], 'trim'],
            // [['imageFile'], 'skipOnEmpty' => false, 'extensions' => 'png, jpg, txt'],
        ];
    }
    
    public function upload()
    {
        if ($this->validate()) {

            // var_dump($this);
            // var_dump('../uploads' . $this->pathParent . $this->nameParent . '/'. $this->imageFile->baseName . '.' . $this->imageFile->extension);
            // die;
            $this->imageFile->saveAs('../uploads' . $this->pathParent . '/'. $this->nameParent . '/'. $this->imageFile->baseName . '.' . $this->imageFile->extension);
            return true;
        } else {
            return false;
        }
    }
}