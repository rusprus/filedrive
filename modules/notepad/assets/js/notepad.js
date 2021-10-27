
/**
 *  Обьект доски для вывода заметок. Является агрегатором для обьектов заметок
 * 
*/
class Desk 
{

    // Конструктор создает div в качестве области вывода заметок и
    // массив для зарегистрированных заметок
    // this.field - верстка доски 
    // Массив levels определяет z-index для каждой заметки. Изменяется при изменении фокуса

    constructor(){

        this.field = document.createElement('div');
        this.field.classList.add('field');
        this.notes = [];
        this.levelsZ = [];
        this.coord = getCoords( this.field );
        this.dbNotes = [];

        
    }

    bindToElem( elem ){
        
        scene = document.getElementById( elem );
        scene.append( this.field );
        this.coord = getCoords( desk.field );
    }

    

    removeNote( note ){

        let index = this.notes.findIndex(function(item) {

            if(item.id == note.id) return true;
          });

        this.notes.splice( index, 1 );
        note = null;
    }

    getNotesFromDb(){
        // Делаем запрос в БД на выдачу существующих заметок

        let url = document.location.origin + '/notepad/notepad/get-notes';
        
        fetch(url, {
            method: 'POST',
            type: 'JSON',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' 
            },
          }).then( 
              (response) => {
                if (response.ok) { 
                    return response.json() 
                }else{
                    return alert("Ошибка HTTP: " + response.status);
                }
            }).then(
                (response) => {

                this.dbNotes =  response;
                
                for( let i=0 ; i < this.dbNotes.length ; i++  ){

                    // console.log(this.dbNotes[i] );

    
                    let note = new Note( this.dbNotes[i].id, this.dbNotes[i].text, this.dbNotes[i].level, this.dbNotes[i].top, this.dbNotes[i].left)

                    this.addNote( note ); 
                }
            })
    }

    addNote( note ){
        
        note.field.style.left = note.left + 'px';
        note.field.style.top = note.top + 'px';

        this.notes.push( note );
        this.field.append( note.field );
    }


}

/**
 *  Обьект заметки.
 * 
*/
class Note 
{

    constructor( id, text, level, top, left ){

        this.id = id;
        this.text = text;
        this.level = level;
        this.top = top;
        this.left = left;
        this.field = document.createElement('div');
        this.field.text = 'Пустая заметка';
        this.field.classList.add('note');
        this.registerEvents();

    }

    // Регистрация событий заметки
    registerEvents(){

        // Регистрация нажатия
        this.field.addEventListener('mousedown', this.pushMouse.bind(this));

        // Регистрация освобождения
        this.field.addEventListener('mouseup', this.upMouse.bind(this));
    }

    // Отпускаем заметку
    upMouse(event){

        // Снимаем отслеживание курсора
        document.removeEventListener('mousemove', this.onMouseMove);
        document.onmousemove = null;

        // При отпускании обновляем БД
        this.updateNote(event)
    }
    
    // Нажимаем на заметку
    pushMouse(event){

        // Готовим к перемещению:
        event.target.style.zIndex = 1000;

        // При нажатии регистрируем отслеживание курсора
        document.addEventListener('mousemove', this.onMouseMove);

        // Отменяем собственое действие перетаскивания браузера
        event.target.addEventListener('dragstart', function() { return false });  
    }

    // Двигаем мышь
    onMouseMove(event) {
        // Изменяем координаты заметки
        event.target.style.left = event.pageX - event.target.offsetWidth / 2 + 'px';
        event.target.style.top = event.pageY - event.target.offsetHeight / 2 + 'px';
    }

    // Отсылаем новые данные по текущей заметке. Координаты, текст.
   updateNote( event ){

        this.text = 'newText';
        this.top = parseInt( event.target.style.top, 10 );
        this.left =  parseInt( event.target.style.left, 10 );

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
}



// Создаем доску и
desk = new Desk();

// Добавляем к элементу с id = 'scene'
desk.bindToElem('scene');

// Берем с ДБ заметки и выводим на доску 
desk.getNotesFromDb();

// console.log(desk.constructor)












// Общии функции
// получаем координаты элемента в контексте документа
  function  getCoords(elem) {
    let box = elem.getBoundingClientRect();

    return {
    top: box.top + window.pageYOffset,
    left: box.left + window.pageXOffset
    }
}
  