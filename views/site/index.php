<?php

/* @var $this yii\web\View */

use yii\widgets\ActiveForm;

$this->title = 'My Yii Application';
?>
<div class="site-index">

<div class="row _midle">
            <div class="col-4 col-md-2 _nav border border-primary" style="">
                <div class="left-nav" role="navigation">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                          <a class="nav-link active" aria-current="page" href="#">Мой диск</a>

  <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

  <?php echo $form->field($model, 'imageFile')->fileInput() ?>

  <button>Submit</button>

  <?php ActiveForm::end() ?>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" href="#">Компьютер</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" href="#">Доступные мне</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Недавнии</a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" href="#">Помеченые</a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" href="#">Корзина</a>
                          </li>
                        <li class="nav-item">
                          <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
                        </li>
                      </ul>
                </div>
            </div>    
            <div class="col-8 col-md-10 _content border border-primary">
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4">

<?php foreach($files as $file):  ?>
                    <div class="col border border-primary px-4 py-4" style="width:250px; height: 100px;">
                      <img src=" <?php 
                        if($file->type == 'dir'){
                           echo "./img/folder.png";
                         }else{
                           echo "./img/document.png";
                         }  
                        ?>" alt="folder" class="h-100 w-25">
                      <p class="d-inline-block "><?php echo $file->name ?></p>
                    </div>
<?php endforeach; ?>
                   
                </div>
            </div>    

</div>
