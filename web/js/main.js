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


// Добавление папки

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
            // console.log(data);
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
     removeDropdownMenu()
    if (event.target.dataset.contextmenuFolder != undefined) {
        
        contextMenuFolder(event)
    }   
    if (event.target.dataset.contextmenuFile != undefined) {
        contextMenuFile(event)
    } 
    
  });

  document.addEventListener('click', function(event) {
    removeDropdownMenu()
    if (event.target.dataset.click != undefined) {
        clickOnHref(event)
    }   

    if (event.target.dataset.addFolder != undefined) {
        addFolder(event)
    } 
    if (event.target.dataset.uploadFile != undefined) {
        elem = document.getElementById('uploadform-idparent')
        id = document.querySelector('.breadcrumb').lastElementChild.children[0].dataset.id;
        elem.value = id
        // alert(elem.value)
    } 

    // if (event.target.dataset.delFile != undefined) {
    //     addFolder(event)
    // } 
  });

/**
  *  Запрос на добавление папки
  *
  */
 function addFolder(event){

    filename = document.getElementById('newFolder').value
    id = document.querySelector('.breadcrumb').lastElementChild.children[0].dataset.id;

    $.ajax({
        type: 'POST',
        url: 'fileman/add-dir',
        data:{ 'id': id,
                'filename': filename
        },
        success: function(res){
            getListFiles(id)
        },
        error: function(){
            alert('Bad');
        }
    })
    $('#adddirModal').modal('toggle');
 }



function clickOnHref(event){
    event.preventDefault();

    id = event.target.dataset.id;
    type = event.target.dataset.type;

    if( type == 'dir' ) getListFiles(id);
    if( type == 'file' ) downloadFile(event.target);

    // console.log(event.target.dataset.id);
    // console.log(event.target.dataset.type);
}

/**
  *  Запрос на скачивание файла
  *
  */
function  downloadFile(label){
            
            attrLabel = label.attributes;
            a =  document.createElement('a');
            console.log('fileman/download-file&id='+label.getAttribute('data-id'))
            a.setAttribute('href', 'fileman/download-file?id='+label.getAttribute('data-id'))
            a.click();



    // $.ajax({
    //     type: 'POST',
    //     url: 'fileman/download-file',
    //     data:{ 'id': id,
    //     },
    //     // dataType: 'binary',  
    //     success: function(res){
    //         alert('ok');

    //         console.log(res);
    //         // let reader = new FileReader(); // без аргументов
    //         let blob = new Blob([res]);
    //         // let blob = new Blob([res]);

    //         // console.log( blob );
    //         // console.log(reader.readAsArrayBuffer(blob));


    //         var link = document.createElement("a");
    //         link.setAttribute("href", URL.createObjectURL(blob));
    //         link.setAttribute("download", "1-1.jpg");
    //         link.click();
    //     },
    //     error: function(){
    //         alert('Bad');
    //     }
    // })
}

/**
  *  Запрос на получение списка  файлов в папке
  *
  */

function getListFiles(id){

    $.ajax({
        type: 'POST',
        url: 'fileman/get-list-files',
        data:{ 'id': id,
        },
        success: function(res){
            changeBreadcrumb(res[0]);
            $('._content').html(res[1]) ;
        },
        error: function(){
            alert('Bad');
        }
    })

}


// Изменение хлебных крошек

function changeBreadcrumb(breadcrumbParam){

    breadcrumb = $('.breadcrumb').html('');
    breadcrumbItem = $("<li class='breadcrumb-item'><a href='#'></a></li>");
    breadcrumbParam.forEach(element => {

        breadcrumbItem.children('a').eq(0).attr('href', element.url);
        breadcrumbItem.children('a').eq(0).attr('data-id', element.id); 
        breadcrumbItem.children('a').eq(0).attr('data-type', "dir"); 
        breadcrumbItem.children('a').eq(0).attr('data-click', ""); 
        breadcrumbItem.children('a').eq(0).text(element.label);
        breadcrumbItem.clone().appendTo(breadcrumb);

    });
}


// Обработка контекстного меню для папок

function contextMenuFolder(event){
    event.preventDefault();
    event.stopPropagation();
    folder = event.target;
    id = folder.dataset.id


        contextMenuHtml = `<div class="dropdown-menu show" >
                                <a class="dropdown-item" data-id="`+id+`" onclick="delDir(event)" href="#">Удалить</a>
                                <a class="dropdown-item" data-id="`+id+`" onclick="renameDir(event)" href="#">Переименовать</a>
                                <a class="dropdown-item" data-id="`+id+`" href="#">Перейти</a>
                            </div>`;
    folder.insertAdjacentHTML('beforeend', contextMenuHtml);
       

}

// Обработка контекстного меню для файлов

function contextMenuFile(event){
    event.preventDefault();
    event.stopPropagation();
    folder = event.target;
    id = folder.dataset.id

        contextMenuHtml = `<div class="dropdown-menu show" >
                                <a class="dropdown-item" data-id="`+id+`" onclick="delDir(event)" href="#">Удалить</a>
                                <a class="dropdown-item" data-id="`+id+`" onclick="renameDir(event)" href="#">Переименовать</a>
                                <a class="dropdown-item" data-id="`+id+`" href="#">Перейти</a>
                            </div>`;
        folder.insertAdjacentHTML('beforeend', contextMenuHtml);
       
}


// Переименование файла или папки

function renameDir(event){
    event.preventDefault()

    id = event.target.dataset.id;
    dir = $(document.querySelector('div [data-id="'+id+'"]'));
    // labell = document.querySelector('div .label [data-id="84"]'); 

    label = $(dir.children()[1]);

    removeDropdownMenu();
    inputRename = $('<input type = "text">').attr('value', label.text());

    inputRename.on('keydown', function(e) {
        if (e.keyCode === 13) {

            label.text( this.value );
            this.remove();
            dir.append(label);
            label.on('contextmenu', contextMenuFolder);
            sendRename(label);
        }
        
    });
    label.remove();
    dir.append(inputRename);
    inputRename.focus();
    removeDropdownMenu();
    
}

// Запрос на изменеие файла

function sendRename(label){

    newName = label.text();
    id = label.attr('data-id');

    $.ajax({
        type: 'GET',
        url: 'fileman/rename',
        data:{ 'newname': newName,
                'id': id,
        },
        success: function(){
            // alert('ok');
        },
        error: function(){
            alert('Bad');
        }
    })

}

// Запрос на удаление файла или папки

function delDir(event){
    event.preventDefault()

    id = event.target.dataset.id;
    $.ajax({
        type: 'GET',
        url: 'fileman/del',
        data:{ 
            'id': id,
        },
        success: function(res){
            dir = $(document.querySelector('div [data-id="'+id+'"]'));
            dir.remove();
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

