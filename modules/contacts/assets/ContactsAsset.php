<?php

namespace app\modules\contacts\assets;

use yii\web\AssetBundle;

/**
 * Class ContactsAsset
 * 
 */
class ContactsAsset extends AssetBundle
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
        'js/view-contacts.js',

    ];

    /**
     * @inheritdoc
     */
    public $css = [

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