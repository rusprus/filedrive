/**
 *  Обьект заметки.
 * 
*/
export class Note 
{

    constructor( id = null, text = 'Заметка', level = 1, top = 200, left = 700 ){

        this.id = id;
        this.text = text;
        this.level = level;
        this.top = top;
        this.left = left;
        this.field = document.createElement('div');
        this.field.innerText = this.text;
        this.field.classList.add('note');
        this.registerEvents();
        this.inputArea;
        this.noteButton;

    }

    // Регистрация событий заметки
    registerEvents(){

        // Регистрация нажатия
        this.field.addEventListener('mousedown', this.pushMouse.bind(this));

        // Регистрация освобождения
        // this.field.addEventListener('mouseup', this.upMouse.bind(this));
        
        document.addEventListener('mouseup', this.upMouse.bind(this));

        // Регистрация события dblclick ( редактирования заявки )
        this.field.addEventListener('dblclick', this.editNote.bind(this));
    }

    // Редактируем заметку
    editNote(event){

        event.stopPropagation();
        event.preventDefault();

        // Создаем поле ввода текста заметки
        this.inputArea = document.createElement('textarea');
        // this.inputArea.setAttribute('wrap', 'hard');
        this.inputArea.innerText = this.text;
        this.inputArea.rows = 10;
        this.field.innerText = '';
        this.field.append(this.inputArea);

         // Добавляем кнопку сохранить 
         this.noteButton = document.createElement('button');
         this.noteButton.innerText = "Сохранить";

        // Добавляем кнопку удалить 
        this.noteButtonDelete = document.createElement('button');
        this.noteButtonDelete.innerText = "Удалить";

         //Вешаем событие на кнопку сохранить
         this.noteButton.addEventListener('click', this.submBtn.bind(this));

        //Вешаем событие на кнопку удалить
        this.noteButtonDelete.addEventListener('click', this.deleteNote.bind(this));
        
        this.field.append(this.noteButton);
        this.field.append(this.noteButtonDelete);
    }

    submBtn(event){

       this.field.innerText  = this.inputArea.value;
       this.text  = this.inputArea.value;

       console.log('submBtn');
       // Обновляем БД
       this.updateNote()
       // Удаляем кнопку и поле редактирования текста
       this.noteButton.remove();
       this.inputArea.remove();
    }

    // Отпускаем заметку
    upMouse(event){
        if(event.target.tagName ==  "BUTTON") return ;
    
        // Снимаем отслеживание курсора
        document.removeEventListener('mousemove', this.onMouseMove);
        document.onmousemove = null;

        console.log( 'upMouse' )


        this.top = parseInt( this.field.style.top, 10 );
        this.left =  parseInt( this.field.style.left, 10 );

        // Обновляем БД
        this.updateNote()
    }
    
    // Нажимаем на заметку
    pushMouse(event){
        if(event.target.tagName ==  "BUTTON") return ;
        // Готовим к перемещению:
        // event.target.style.zIndex = 1000;
        this.field.style.zIndex = 1000

        // При нажатии регистрируем отслеживание курсора
        this.onMouseMove = this.onMouseMove.bind(this);
        document.addEventListener('mousemove', this.onMouseMove);

        // Отменяем собственое действие перетаскивания браузера
        // event.target.addEventListener('dragstart', function() { return false });  
        document.addEventListener('dragstart', function() { return false });  
    }

    // Двигаем мышь
    onMouseMove(event) {
        // Изменяем координаты заметки
        // event.target.style.left = event.pageX - event.target.offsetWidth / 2 + 'px';
        // event.target.style.top = event.pageY - event.target.offsetHeight / 2 + 'px';
        console.log(this.field)
        console.log(event)
        this.field.style.left = event.pageX - this.field.offsetWidth / 2 + 'px';
        this.field.style.top = event.pageY - this.field.offsetHeight / 2 + 'px';
    }


      // Запрос на создание новой заметки.
   creatNote(){

    console.log('creatNote');

                //  Update-запрос заметки
               let data = JSON.stringify( this, ['text', 'level', 'top', 'left'] );
                let url = document.location.origin + '/notepad/notepad/insert-note';
        
                fetch(url, {
                    method: 'POST',
                    type: 'JSON',
                    body: data,
                    headers: {
                      'Content-Type': 'application/json; charset=UTF-8' 
                    },
                  }).then( 
                      (response) => {
                        if (response.ok) { 
                            
                            return response.json()
                        }else{
                            return alert("Ошибка HTTP: " + response.status);
                        }
                    })
                    .then((response)=>{
                        this.id = response.id 


                    })
    }

    // Отсылаем новые данные по текущей заметке. Координаты, текст.
   updateNote(){

                //  Update-запрос заметки
               let data = JSON.stringify( this, ['text', 'id', 'level', 'top', 'left'] );
                let url = document.location.origin + '/notepad/notepad/update-note';
        
                fetch(url, {
                    method: 'POST',
                    type: 'JSON',
                    body: data,
                    headers: {
                      'Content-Type': 'application/json; charset=UTF-8' 
                    },
                  }).then( 
                      (response) => {
                        if (response.ok) { 

                            return response.json()
                        }else{
                            return alert("Ошибка HTTP: " + response.status);
                        }
                    })
    }


    // Удаляем запись по id.
   deleteNote(){


    console.log('DeleteNote');

                //  Update-запрос заметки
               let data = JSON.stringify( this, [ 'id' ] );
                let url = document.location.origin + '/notepad/notepad/delete-note';
        
                fetch(url, {
                    method: 'POST',
                    type: 'JSON',
                    body: data,
                    headers: {
                      'Content-Type': 'application/json; charset=UTF-8' 
                    },
                  }).then( 
                      (response) => {
                        if (response.ok) { 


                            // Удаляем кнопку и поле редактирования текста
                            this.field.remove();
                            this.noteButton.remove();
                            this.noteButtonDelete.remove();
                            this.inputArea.remove();
                            

                            return response.json()
                        }else{
                            return alert("Ошибка HTTP: " + response.status);
                        }
                    })
    }

    
}