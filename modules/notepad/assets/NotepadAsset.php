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
        'js/components/desk.component.js',
        'js/components/note.component.js',

    ];

    public $jsOptions = [
        "type" => "module",
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