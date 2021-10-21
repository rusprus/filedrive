
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
    }

    bindToElem( elem ){
        
        scene = document.getElementById( elem );
        scene.append( this.field );
        this.coord = getCoords( desk.field );
        // console.log( this.coord );
    }

    addNote( note ){

        note.coord.left = this.coord.left + 70 * note.id;
        note.coord.top = this.coord.top + 70 * note.id;

        note.field.style.left = note.coord.left + 'px';
        note.field.style.top = note.coord.top + 'px';

        // console.log( note.field.style.left );
        // console.log( note.coord.left );

        this.notes.push( note );
        this.field.append( note.field );

    }

    removeNote( note ){

        let index = this.notes.findIndex(function(item) {

            if(item.id == note.id) return true;
          });

        this.notes.splice( index, 1 );
        note = null;
    }

    getNotesFromDb(){
        
    }

    changeFocus( note ){

    }
}

/**
 *  Обьект заметки.
 * 
*/
class Note 
{

    constructor( id, text, level ){

        this.id = id;
        this.text = text;
        this.level = level;
        this.field = document.createElement('div');
        this.field.text = 'Пустая заметка';
        this.field.classList.add('note');
        this.coord = getCoords( this.field );
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


//  Создаем заметки
note1 = new Note( 1, 'Note1', 1) ;
note2 = new Note( 2, 'Note2', 1) ;
note3 = new Note( 3, 'Note3', 1) ;

// Добавляем заметки на доску
desk.addNote( note1 );
desk.addNote( note2 );
desk.addNote( note3 );

// desk.removeNote( note1 );




  







// Общии функции
// получаем координаты элемента в контексте документа
function getCoords(elem) {
    let box = elem.getBoundingClientRect();
  
    return {
      top: box.top + window.pageYOffset,
      left: box.left + window.pageXOffset
    };
  }