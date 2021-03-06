<?php
namespace app\modules\contacts\models;

use yii\base\Model;
use yii\web\UploadedFile;
use app\modules\contacts\models\PhoneBook;
use app\modules\contacts\models\VCard;
use Yii;



class UploadForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $imageFile;

    public function rules()
    {
        return [
            // [['imageFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'vcf'],
            [['imageFile'], 'file', 'skipOnEmpty' => false],
            [['imageFile'], 'validateImageFile', 'skipOnEmpty' => false],
        ];
    }

    public function validateImageFile($attribute, $params)
    {
        $name = $this->$attribute->name;
        $path_parts = pathinfo($name);

        if( $path_parts['extension'] !== 'vcf' ){
            
            $this->addError($attribute, "Расширение должно быть vcf");
        }
    }
    
    public function upload()
    {

        if ($this->validate()) {

            $this->imageFile->saveAs(__DIR__ .'/../uploads/' . $this->imageFile->baseName . '.' . $this->imageFile->extension);
            $this->writeToDb();
            unlink(__DIR__ .'/../uploads/' . $this->imageFile->baseName . '.' . $this->imageFile->extension);
            
            return true;
        } else {

            return false;
        }
    }

     /**
    * Скрипт получает файл с контактами .vcf и заносит в БД
    */
    public function writeToDb()
    {
        $user_id = Yii::$app->user->identity->id;

        $name = $this->imageFile->name;
        $path_parts = pathinfo($name);
        $new_vcf =  __DIR__ .'/../uploads/'. $name;

        // Блок проверки файла. Проверка на 1. существование, 2. 
        if( file_exists($new_vcf) && isset($name) && $path_parts['extension'] == 'vcf' ){

                $vCard = new vCard( $new_vcf );

                foreach ($vCard as $vCardPart){

                    $contact = new PhoneBook;
                    $contact->first_name = $vCardPart->n[0]['FirstName'];
                    $contact->last_name = $vCardPart->n[0]['LastName'];
                    $contact->add_names = $vCardPart->n[0]['AdditionalNames'];
                    $contact->tel = isset($vCardPart->tel[0]['Value']) ? $vCardPart->tel[0]['Value'] : null;
                    $contact->user_id = $user_id;
                    
                    $contact->save();

                }
            
        }

    }
}