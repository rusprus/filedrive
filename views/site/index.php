<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Filedrive';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about text-center" >
    <h1><?= Html::encode($this->title) ?></h1>
    <p>Альтернатива сервисам гугл</p>

<div class="row">
    <div class="col">
        <img src="<?= Url::to("@web/img/contacts.png")?>" class="rounded mx-auto d-block" alt="..." style="width:400px; heigth: 300px;margin:50px;">
    </div>
    <div class="col " >
        <img src="<?= Url::to("@web/img/files.png")?>" class="rounded mx-auto d-block" alt="..." style="width:400px; heigth: 300px;margin:50px;">
    </div>
    <div class="col">
        <img src="<?= Url::to("@web/img/notes.png")?>" class="rounded mx-auto d-block" alt="..." style="width:400px; heigth: 300px;margin:50px;">
    </div>
  
</div>
   
</div>
