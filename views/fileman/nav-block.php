<?php
use yii\widgets\ActiveForm;
?>

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
          'action'=> 'fileman/upload-file',
          'options' => ['enctype' => 'multipart/form-data']]) ?>

          <?php echo $form->field($uploadForm, 'imageFile')->fileInput() ?>
          <?php echo $form->field($uploadForm, 'idParent')->hiddenInput(['value' => $curFile->id])->label('') ;?>


          <!-- <button>Submit</button> -->
          <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
          <button type="submit" class="btn btn-primary" data-upload-file >Сохранить изменения</button>
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

        <input id='newFolder' class="form-control" type="text" placeholder="Новая папка" value=''>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal" >Закрыть</button>
            <button type="submit" class="btn btn-primary" data-add-folder >Сохранить изменения</button>
        </div>

        </div>
      
      </div>
    </div>
  </div>