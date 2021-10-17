<?php
/**
 * 
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * 
 *
 */
class FilemanAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        // 'css/site.css',
    ];
    public $js = [
        // 'js/main.js',
        'js/fileman.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap4\BootstrapAsset',
    ];
}
