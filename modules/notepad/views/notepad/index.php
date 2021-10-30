<?php 
    
use app\modules\notepad\assets\NotepadAsset;


NotepadAsset::register($this);  // $this - представляет собой объект представления

?>

<div class="d-flex justify-content-center"><h1>NotePadd(Заметки)</h1></div>
<div class="d-flex justify-content-center"><button id='createNote'>Создать заметку</button></div>

<div id="scene"><div>

<!-- <script>console.log('dddd')</script> -->