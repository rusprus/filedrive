import {Note} from './note.component.js'
/**
 *  Обьект доски для вывода заметок. Является агрегатором для обьектов заметок
 * 
*/
export class Desk 
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
        this.coord = this.getCoords( this.field );
        this.dbNotes = [];

        
    }

    bindToElem( elem ){
        
        scene = document.getElementById( elem );
        scene.append( this.field );
        this.coord = this.getCoords( this.field );
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


    // Общии функции
// получаем координаты элемента в контексте документа
  getCoords(elem) {
    let box = elem.getBoundingClientRect();

    return {
    top: box.top + window.pageYOffset,
    left: box.left + window.pageXOffset
    }
}


}