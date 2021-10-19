// Класс ячейки 
class Cell{

    constructor( fieldId, rowId, content ) {
        this.fieldId = fieldId;
        this.rowId = rowId;
        this.content = content;
      }

    setContent(newContent){

       if( this.content !== newContent && this.checkContent(newContent)){

        this.content = newContent;

        this.updateContent();

       } 
    }

    getContent(){
        return this.content;
    }

    checkContent(newContent){
        switch( this.fieldId ){
            case 'tel':
                return this.checkNumber(newContent) ;
            break;

            case 'first_name' :
                // checkNumber(newContent) 
                return true;
            break;

            case 'last_name' :
                // checkNumber(newContent) 
                return true;
            break;

            case 'add_names' :
                // checkNumber(newContent) 
                return true;
            break;
        }
    }

    updateContent(){

        $.ajax({
            url: "/contacts/default/update",
            type: "POST",
            data: {id : this.rowId, 
                    field : this.fieldId,
                    value: this.content},
            success: function(result){
                alert('Записалось.');
            },
            error: function(e){
                alert('Не работает');
            }
        });
    
    }

    checkNumber(number){

        var template = '(\\+?[0-9]{0,3}){0,1}[- \\\\(]{0,}([9][0-9]{2})[- \\\\)]{0,}(([0-9]{2}[-]{0,}[0-9]{2}[- ]{0,}[0-9]{3})|([0-9]{3}[- ]{0,}[0-9]{2}[- ]{0,}[0-9]{2})|([0-9]{3}[-]{0,}[0-9]{1}[- ]{0,}[0-9]{3})|([0-9]{2}[- ]{0,}[0-9]{3}[- ]{0,}[0-9]{2}))';
        var pattern =  new RegExp("^"+template+"","i");
    
        if (pattern.test(number)) {
            // alert('Данные валидны');
            return true;
        }
        else {
            alert('Смените формат. Например 8-910-236-69-14');
            return false;
    }
    }
}

//  Код изменения ячейки и подачи события "изменения" для записи в БД 

$(function () {

    // Делегируем событие выбора ячейки на таблицу
    $("table").dblclick(function (event) {
        
        if( event.target.tagName == 'TD' ){

            let targetCell = $(event.target);

            // Получаем содержимое ячейки 
            let content = targetCell.text();

            // Получаем название столбца ячейки 
            let fieldId = targetCell.attr('class');

            // Получаем номер сторки ячейки 
            let rowId = targetCell.parent().children().first().text();
    
            // Создаем обьект ячейки для манипуляции содержимом и взаимодействия с БД
            cell = new Cell( fieldId, rowId, content );

            targetCell.html("<input type='text' value='" + cell.getContent() + "' />");
            targetCell.children().focus();
            targetCell.children().keypress(function (e) {
                if (e.which == 13) {

                    cell.setContent( targetCell.children().val() );

                    targetCell.text(cell.getContent());
    
                }else{
          
                }
            });
            targetCell.children().blur(function () {
                if( cell.getContent() ) targetCell.text(cell.getContent());
                
            });

        }
       
    });

//  Код фильтрации столбцов 

    
    function filterTable(table) {

        let all_rows = table.find('tr');
        let data_rows = all_rows.slice(1);
        let filter_row = all_rows.eq(0);
        let filter_sells = filter_row.find('td'); 

        this.filter = function() {
            
            data_rows.each(function (rowIndex, row) {

                // let self = this; //запишем this в любую переменную
                let valid = true;

                filter_sells.each(function (colIndex, filter_cell) {

                    let cur_cell = filter_sells.eq(colIndex).find('input').val();

                    if (cur_cell) {

                        if (data_rows.eq(rowIndex).find('td').eq(colIndex).text().toLowerCase().indexOf(cur_cell.toLowerCase()) == -1) {

                            valid = valid && false;
                        }
                    }
                });
                
                if (valid === true) {
                    $(this).css('display', '');
                } else {
                    $(this).css('display', 'none');
                }
            });
        }
    }



    $(".table-filters input").on("input", function () {

        table1 = new filterTable($(document).find('table'));


        table1.filter();

    });
});



// Функция показа дополнительного контента при прокрутке
function scrollMore(){

    var $target = $('#showmore-triger');

	if (block_show) {

		return false;
	}

	var wt = $(window).scrollTop();
	var wh = $(window).height();
	var et = $target.offset().top;
	var eh = $target.outerHeight();
	var dh = $(document).height();   

	// if (wt + wh >= et || wh + wt == dh || eh + et < wh){
        if ( wh + wt == dh || eh + et < wh){

		var page = $target.attr('data-page');	
		page++;
		block_show = true;

		$.ajax({ 
			// url: '/contacts/default/get-contacts?page=' + page,  
			url: '/contacts/default/get-contacts?page=' + page,  
			dataType: 'html',
			success: function(data){
				$('#filter-table').append(data);
				block_show = false;
			}
		});

		$target.attr('data-page', page);
		if (page ==  $target.attr('data-max')) {

			$target.remove();
		}

	}

}

// Событие вызываемое при прокрутке
var block_show = false;

$(window).scroll(function(){

	scrollMore();
});

$(document).ready(function(){ 

	scrollMore();
});
