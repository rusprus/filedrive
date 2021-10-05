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

//////////////////////////////////////////////////////////////////////////////////////
// Делегирование событий

document.addEventListener('contextmenu', function(event) {
    
    if (event.target.dataset.contextmenu != undefined) {
        contextMenu(event)
    }   
  });


//Обработка контекстного меню для ссылок файлов

function contextMenu(event){
    event.preventDefault();
    event.stopPropagation();
    hrefFile = $(event.target);
    if(!hrefFile.find('.dropdown-menu').length){
        removeDropdownMenu();
        hrefToId = hrefFile.attr('href') ;
        contextMenuHtml = `<div class="dropdown-menu show" >
                                <a class="dropdown-item" onclick="delDir(event)" href="`+hrefToId+`&delete=true">Удалить</a>
                                <a class="dropdown-item" onclick="renameDir(event)" href="`+hrefToId+`">Переименовать</a>
                                <a class="dropdown-item" href="`+hrefToId+`">Перейти</a>
                            </div>`;
        hrefFile.append(contextMenuHtml);
       
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
    inputRename = $('<input type = "text">').attr('value', hrefFile.text());
    inputRename.on('keydown', function(e) {
        if (e.keyCode === 13) {
            hrefFile.text( this.value );
            this.remove();
            dir.append(hrefFile);
            hrefFile.on('contextmenu', contextMenu);
            // console.log(hrefFile.text);
            sendRename(hrefFile);
        }
        
    });
    hrefFile.remove();
    dir.append(inputRename);
    inputRename.focus();
    removeDropdownMenu();
    
}

// Запрос на изменеие файла

function sendRename(hrefFile){
    url = location.origin + hrefFile.attr('href') + '&newname=' + hrefFile.text();
    url = new URL( url );
    qHost = url.origin;
    qPath = url.pathname;
    
    qParamNewname = url.searchParams.get('newname');
    qParamId = url.searchParams.get('id');
  


    $.ajax({
        type: 'GET',
        url: 'fileman/rename',
        data:{ 'newname': qParamNewname,
                'id': qParamId,
        },
        success: function(){
            // alert('ok');
        },
        error: function(){
            alert('Bad');
        }
    })

}

// Удаление файла или папки

function delDir(event){
    event.preventDefault()
    // console.log(event.target);
    url = location.origin + hrefFile.attr('href');
    url = new URL( url );
    qParamId = url.searchParams.get('id');
    // console.log( qParamId );


    $.ajax({
        type: 'GET',
        url: 'fileman/del',
        data:{ 
            'id': qParamId,
        },
        success: function(res){
            // alert('ok');
            window.location.reload(false);
        //     $(event.target).remove();
           console.log(res);
        },
        error: function(){
            alert('Bad');
        }
    })
}

// Функция удаления контекстного меню

function removeDropdownMenu(){
  $('.dir').find('.dropdown-menu').remove('.dropdown-menu');
}

