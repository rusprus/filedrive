<?php

namespace app\modules\notepad\assets;

use yii\web\AssetBundle;

/**
 * Class NotepadAsset
 * 
 */
class NotepadAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $depends = [

        'yii\web\YiiAsset',
        'yii\bootstrap4\BootstrapAsset',
    ];

    /**
     * @inheritdoc
     */
    public $js = [
        'js/notepad.js',

    ];

    /**
     * @inheritdoc
     */
    public $css = [
        'css/notepad.css',

    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = __DIR__ ;
        parent::init();
    }
}