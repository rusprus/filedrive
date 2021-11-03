<?php 
    
use app\modules\notepad\assets\NotepadAsset;


NotepadAsset::register($this);  // $this - представляет собой объект представления

?>

<div class="d-flex justify-content-center"><h1>Заметки</h1></div>
<div class="d-flex justify-content-center">
    <button id='createNote' type="button" class="btn btn-primary">Создать заметку</button>
</div>

<div id="scene">
    <!-- <div class="note" id="note">

        <textarea name="" id="" cols="30" rows="5">
        </textarea>

        <button id='saveButton'>Сохранить</button>
        <button id='deleteButton'>Удалить</button>
    </div> -->
<div>

<!-- <script>console.log('dddd')</script> -->