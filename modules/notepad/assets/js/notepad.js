import {Desk} from './components/desk.component.js'
import { Note } from './components/note.component.js';
// import {Note} from './components/note.component.js'


// Создаем доску и
let desk = new Desk();

// Добавляем к элементу с id = 'scene'
desk.bindToElem('scene');

// Берем с ДБ заметки и выводим на доску 
desk.getNotesFromDb();



//Создаем кнопку добавить и вешаем событие колика 
let createButton = document.getElementById('createNote');

createButton.onclick = function(){

    // Создаем локально
    let newNote = new Note();

    //Создаем в БД
    newNote.creatNote();

    // Добавляем на доску локально
    desk.addNote( newNote );
}
