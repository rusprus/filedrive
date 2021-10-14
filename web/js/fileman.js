/**
  *  Делегирование событий
  *
  */
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

    if (event.target.dataset.click != undefined && event.target.dataset.type == 'file') {
        event.preventDefault();
        downloadFile(event.target.dataset.id);
    }

    if (event.target.dataset.click != undefined && event.target.dataset.type == 'dir') {
        event.preventDefault();
        getListFiles(event.target.dataset.id);
    }   

    if (event.target.dataset.addFolder != undefined) {
        addFolder(event)
    } 
    
    if (event.target.dataset.uploadFile != undefined) {
        elem = document.getElementById('uploadform-idparent')
        id = document.querySelector('.breadcrumb').lastElementChild.children[0].dataset.id;
        elem.value = id
    } 

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

/**
  *  Запрос на скачивание файла
  *
  */
function  downloadFile(id){
            // id = event.target.dataset.id;
        
            a =  document.createElement('a');
            console.log('fileman/download-file&id='+id)
            a.setAttribute('href', 'fileman/download-file?id='+id)
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
//  alert(id);
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

/**
  *  Функция изменения хлебных крошек
  *
  */
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

/**
  *  Обработка контекстного меню для папок
  *
  */
function contextMenuFolder(event){
    event.preventDefault();
    event.stopPropagation();
    folder = event.target;
    id = folder.dataset.id
    contextMenuHtml = `<div class="dropdown-menu show" >
                            <a class="dropdown-item" data-id="`+id+`" onclick="delDir(event)" href="#">Удалить</a>
                            <a class="dropdown-item" data-id="`+id+`" onclick="renameFile(event)" href="#">Переименовать</a>
                            <a class="dropdown-item" data-type="dir"  data-id="`+id+`" data-click href="#">Перейти</a>
                        </div>`;
    folder.insertAdjacentHTML('beforeend', contextMenuHtml);
}


/**
  *  Обработка контекстного меню для файлов
  *
  */
function contextMenuFile(event){
    event.preventDefault();
    event.stopPropagation();

    file = event.target;
    id = file.dataset.id;
    type = file.dataset.type;

    contextMenuHtml = `<div class="dropdown-menu show" >
                            <a class="dropdown-item" data-id="`+id+`" onclick="delDir(event)" href="#">Удалить</a>
                            <a class="dropdown-item" data-id="`+id+`" onclick="renameFile(event)" href="#">Переименовать</a>
                            <a class="dropdown-item" data-type="file" data-id="`+id+`" data-click  href="#">Скачать</a>
                        </div>`;
    file.insertAdjacentHTML('beforeend', contextMenuHtml);
}

/**
  *  Переименование файла или папки
  *
  */
function renameFile(event){
    event.preventDefault()
    id = event.target.dataset.id;
    dir = $(document.querySelector('div [data-id="'+id+'"]'));
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

/**
  *  Запрос на изменеие файла или папки
  *
  */
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

/**
  *  Запрос на удаление файла или папки
  *
  */
function delDir(event){
    event.preventDefault()
    id = event.target.dataset.id;
    $.ajax({
        type: 'POST',
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

/**
  *  Функция удаления контекстного меню
  *
  */
function removeDropdownMenu(){
  $('.dir').find('.dropdown-menu').remove('.dropdown-menu');
}

