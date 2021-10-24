
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

    async getNotesFromDb(){
        // Делаем запрос в БД на выдачу существующих заметок

        // var token = $('meta[name="csrf-token"]').attr("content");
        // let formData = new FormData();
        // formData.append('val', '1'); 
        // formData.append('_csrf', 'token'); 
        // console.log( formData );
        // console.log( url );

        let url = document.location.origin + '/notepad/notepad/get-notes';
        
        let response = await fetch(url, {
            method: 'POST',
            type: 'JSON',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' 
            },

          })

        if (response.ok) { 
            
            this.dbNotes =  await response.json();

            for( let i=0 ; i< this.dbNotes.length ; i++  ){

                let note = new Note( this.dbNotes[i].id, this.dbNotes[i].text, this.dbNotes[i].level, this.dbNotes[i].top, this.dbNotes[i].left)
            
                let addNote = this.addNote.bind(this);

                addNote( note ); 
            }

          } else {
            alert("Ошибка HTTP: " + response.status);
          }

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
        // this.coord = getCoords( this.field );
        this.registerEvents();

    }

    registerEvents(){

        this.field.addEventListener('mousedown', myEventReg);

        function myEventReg(event){
 
            // Готовим к перемещению:
            event.target.style.zIndex = 1000;

            // Перемещаем по экрану
            document.addEventListener('mousemove', onMouseMove);

            // Фиксируем заметку, удаляем более ненужные обработчики событий
            event.target.addEventListener('mouseup', onMouseUp);

            // Фиксируем заметку  двойным нажатием , удаляем более ненужные обработчики событий
            event.target.addEventListener('dblclick', onDblclick);

            // Отменяем собственое действие перетаскивания браузера
            event.target.addEventListener('dragstart', function() { return false });


            function moveAt(pageX, pageY) {

                event.target.style.left = pageX - event.target.offsetWidth / 2 + 'px';
                event.target.style.top = pageY - event.target.offsetHeight / 2 + 'px';
            }
        
            function onMouseMove(event) {

                moveAt(event.pageX, event.pageY);
            }

            function onMouseUp(event) {

                document.removeEventListener('mousemove', onMouseMove);
                event.target.onmouseup = null;
            }
        
            function onDblclick(event) {

                document.removeEventListener('mousemove', onMouseMove);
                event.target.ondblclick = null;
            }

        }
    }

    updateText( newText = '' ){

        this.text = newText;
    }

    setCoord( x, y ){

    }
}


// Создаем доску и добавляем к элементу с id = 'scene'

desk = new Desk();
desk.bindToElem('scene');

desk.getNotesFromDb();












// Общии функции
// получаем координаты элемента в контексте документа
  function  getCoords(elem) {
    let box = elem.getBoundingClientRect();

    return {
    top: box.top + window.pageYOffset,
    left: box.left + window.pageXOffset
    }
}
  