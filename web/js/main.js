// $('#contact-form').on('beforeSubmit', function (e) {
// 	if (!confirm("Everything is correct. Submit?")) {
// 		return false;
// 	}
// 	return true;
// });
// alert('sdsdsdsd');


// $('#upload-form').on('afterValidate', function (e) {
//     var $form = $('#upload-form');
//     $form.on('beforeSubmit', function() {
//         var data = $form.serialize();
//         $.ajax({
//             url: $form.attr('action'),
//             type: 'POST',
//             data: data,
//             success: function (data) {
//                 // Implement successful
//             },
//             error: function(jqXHR, errMsg) {
//                 alert(errMsg);
//             }
//          });
//          return false; // prevent default submit
//     });
// });



var $form = $('#dirform');
$form.on('beforeSubmit', function() {
    // var formData = new FormData($('#upload-form'));
    var data = $form.serialize();
    // console.log(formData);
    $.ajax({
        url: $form.attr('action'),
        type: 'POST',
        data: data,
        success: function (data) {
            // Implement successful
            console.log(data);
            document.location.reload();
        },
        error: function(jqXHR, errMsg) {
            alert(errMsg);
        }
     });
     return false; // prevent default submit
});

// Контекстное меню

$('.dir a').on('contextmenu', contextMenu);

//Обработка контекстного меню
function contextMenu(event){
    event.preventDefault();
    event.stopPropagation();
    if(!$(event.target).find('.dropdown-menu').length){
        
        removeDropdownMenu();

        hrefToId = $(event.target).attr('href') ;
        contextMenuHtml = `<div class="dropdown-menu show">
                                <a class="dropdown-item" onclick="delDir(event)" href="`+hrefToId+`&delete=true">Удалить</a>
                                <a class="dropdown-item" onclick="renameDir(event)" href="`+hrefToId+`&rename=">Переименовать</a>
                                <a class="dropdown-item" href="`+hrefToId+`">Перейти</a>
                            </div>`;
        $(event.target).append(contextMenuHtml);
       
    }else{
        removeDropdownMenu();
    }
}


// Переименование файла или папки
function renameDir(event){
    event.preventDefault()

    dir = $($(event.target).parents('.dir'))
    hrefFile = $($(event.target).parents('.dir a'))
    removeDropdownMenu();

    console.log( hrefFile.text());
    inputRename = $('<input type = "text">').attr('value', hrefFile.text());
    inputRename.on('keydown', function(e) {
        if (e.keyCode === 13) {
            this.remove();
            dir.append(hrefFile);
            hrefFile.on('contextmenu', contextMenu);
        }
    });
    hrefFile.remove();
    dir.append(inputRename);
    inputRename.focus();
    removeDropdownMenu();
    
}
// Удаление файла или папки
function delDir(event){
    event.preventDefault()
    alert('delete');
}

// Удаляем контекстное меню при клике вне папки
$('._file-area').bind( "contextmenu", function(e) {
    e.preventDefault();
    removeDropdownMenu();
});

$('._file-area').bind( "click dblclick", function(e) {
    removeDropdownMenu();
});

function removeDropdownMenu(){
  $('.dir').find('.dropdown-menu').remove('.dropdown-menu');
}
