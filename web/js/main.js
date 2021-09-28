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
            // Implement successful
            console.log(data);
            document.location.reload();
        },
        error: function(jqXHR, errMsg) {
            alert(errMsg);
        }
     });
     return false; // prevent default submit
});