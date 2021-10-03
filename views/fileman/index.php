<?php

/* @var $this yii\web\View */

use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;
use yii\helpers\Url;

$this->title = 'My Yii Application';
?>

<div class="site-index _file-area ">


<div class="row _midle h-100 ">
            <div class="col-4 col-md-2 _nav border border-primary" style="">
                <div class="left-nav" role="navigation">
                    <ul class="nav flex-column">

                        <li class="nav-item">
                          <!-- Кнопка-триггер модального окна -->
                          <a type="" href="#" class="nav-link" data-toggle="modal" data-target="#uploadModal">
                            Загрузить файл</a>
                        </li>
                        <li class="nav-item">
                          <!-- Кнопка-триггер модального окна -->
                          <a type="" href="#" class="nav-link" data-toggle="modal" data-target="#adddirModal">
                            Создать папку</a>
                        </li>

                          <li class="nav-item">
                            <a class="nav-link" href="#">Корзина</a>
                          </li>

                      </ul>
                </div>
            </div>    
            <div class="col-8 col-md-10 _content border border-primary">




                <div class=" row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4">

<?php foreach($files as $file):  ?>
                    <div class="dir  col border border-primary px-4 py-4"  style="width:250px; height: 100px;">
                      <?php if($file->type == 'dir'):?>
                            <img src="<?php echo Url::to('@web/img/folder.png'); ?>" alt="folder" class="h-100 w-25">
                            <a href="<?php echo Url::to(['fileman/fileman', 'id' => $file->id, 'type'=>$file->type]);?>" data-contextmenu class="d-inline-block "><?php echo $file->name ?></a>
                      <?php endif; ?>
                      <?php if($file->type == 'file'):?>
                            <img src="<?php echo Url::to('@web/img/document.png'); ?>" alt="folder" class="h-100 w-25">
                            <a href="<?php echo Url::to(['fileman/fileman', 'id' => $file->id]);?>" data-contextmenu class="d-inline-block " ><?php echo $file->name ?></a>
                      <?php endif; ?>
                    </div>
<?php endforeach; ?>
                   
                </div>
            </div>    

<!-- Модальное окно добавления файла-->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Загрузка файла</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <?php $form = ActiveForm::begin(['id' => 'upload-form',
        'action'=> 'upload-file',
        'options' => ['enctype' => 'multipart/form-data']]) ?>

        <?php echo $form->field($uploadForm, 'imageFile')->fileInput() ?>
        <?php echo $form->field($uploadForm, 'parent')->hiddenInput(['value' => $curFile->id])->label('') ;?>


        <!-- <button>Submit</button> -->
        <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
        <button type="submit" class="btn btn-primary">Сохранить изменения</button>
      </div>

        <?php ActiveForm::end() ?>
      </div>

    </div>
  </div>
</div>


<!-- Модальное окно добавления папки-->
<div class="modal fade" id="adddirModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Создание папки</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <?php $form = ActiveForm::begin([
          'id'=>'dirform',
        'action'=> 'add-dir',
        'options' => []]) ?>

        <?php echo $form->field($newDir, 'name')->textInput(['placeholder' => "Название папки"])->label('') ;?>
       
        <?php echo $form->field($newDir, 'parent')->hiddenInput(['value' => $curFile->id])->label('') ;?>

        <!-- <button>Submit</button> -->
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
          <button type="submit" class="btn btn-primary">Сохранить изменения</button>
       </div>

        <?php ActiveForm::end() ?>
      </div>
     
    </div>
  </div>
</div>
</div>
