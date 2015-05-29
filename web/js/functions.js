$('#website').keyup(function(e) {
  if (e.keyCode == 13) { sendWebsite();return false; }   // esc
});
function sendWebsite(){
	$('#loading').css('display','block');
    $('.alert-danger').css('display','none');
    $.ajax({
        method: "POST",
        dataType: "json",
        data: {
            website: $('#website').val(),
        },
        url: "/api/website/",
        success: function(data) {
        	$('#loading').css('display','none');
            $('#website_info').html('ID '+data.id+'. TOKEN' + data.token + '');
        },
        statusCode: {
            404: function() {
                $('#loading').css('display','none');
                $('#website_error').css('display','block');
                
            },
            500: function() {
                $('#loading').css('display','none');
                $('#website_error').css('display','block');
                
            }
          }
      });
    
}