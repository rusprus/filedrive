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
            url: "contacts/default/update",
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
    $(".table-data td").dblclick(function () {

        let content = $(this).text();
        let fieldId = $(this).attr('class');
        let rowId = $(this).parent().children().first().text();

        cell = new Cell( fieldId, rowId, content );

        $(this).html("<input type='text' value='" + cell.getContent() + "' />");
        $(this).children().focus();
        $(this).children().keypress(function (e) {
            if (e.which == 13) {

                cell.setContent( $(this).val() );
                $(this).parent().text(cell.getContent());

            }
        });
        $(this).children().blur(function () {
            $(this).parent().text(cell.getContent());
        });
    });

//  Код фильтрации столбцов 

    
    function FilterTable(table) {

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

    table1 = new FilterTable($(this).find('table'));

    $(".table-filters input").on("input", function () {

        table1.filter();
        // filterTable( $(this).parents('table') );
    });


});
