<?php
namespace app\modules\contacts;

class Module extends \yii\base\Module
{
    public function init()
    {
        parent::init();

        // $this->$layout =
        $this->params['foo'] = 'bar';
        // ... остальной инициализирующий код ...

         // инициализация модуля с помощью конфигурации, загруженной из config.php
    // \Yii::configure($this, require __DIR__ . '/config.php');
    }
}