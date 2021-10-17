<?php

/* @var $this yii\web\View */

use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;
use yii\helpers\Url;

use app\assets\FilemanAsset;

FilemanAsset::register($this);  // $this - представляет собой объект представления

$this->title = 'My Yii Application';
?>

<div class="site-index _file-area ">
<?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            // 'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            'homeLink' => false,
        ]) ?>
        

<div class="row _midle h-100 ">
            <div class="col-4 col-md-2 _nav border border-primary" style="">

               <?php  include "nav-block.php"; ?>

            </div>    

            <div class="col-8 col-md-10 _content border border-primary">

                  <?php  include "files-block.php"; ?>

            </div>    
          
  
</div>
          
