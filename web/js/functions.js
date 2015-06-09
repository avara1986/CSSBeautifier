$('#website').keyup(function(e) {
  if (e.keyCode == 13) { sendWebsite();return false; }   // esc
});
function sendWebsite(){
    $('.error').css('display','none');
    $('.info').css('display','none');
    $('#loading_website').css('display','block');
    $.ajax({
        method: "POST",
        dataType: "json",
        data: {
            website: $('#website').val(),
        },
        url: "/api/website/",
        success: function(data) {
        	$('.loading').css('display','none');
        	$('#website_id').val(data.id);
        	$('#token').val(data.token);
        	$('#website_info').css('display','block');
        	$('#website_info_msg').addClass('alert');
        	$('#website_info_msg').addClass('alert-success');
            $('#website_info_msg').html('The URL <b>'+$('#website').val()+'</b> was successfully analyzed');
            getWebsiteCSS();
        },
        statusCode: {
            404: function() {
                $('.loading').css('display','none');
                $('#website_error').css('display','block');
                
            },
            500: function() {
                $('.loading').css('display','none');
                $('#website_error').css('display','block');
                
            }
          }
      });
    
}
function getWebsiteCSS(){
    $('.error').css('display','none');
    $('#loading_css').css('display','block');
    $.ajax({
        method: "GET",
        dataType: "json",
        url: "/api/website/"+$('#website_id').val()+"/"+$('#token').val(),
        success: function(data) {
            $('.loading').css('display','none');
            $('#css_info').css('display','block');
            result = "<ul>";
            $.each(data.css, function(i, item) {
                result += ('<li id="css_'+data.css[i].id+'">'+data.css[i].url+'<a href="#" onclick="sendCss(\''+data.css[i].id+'\'); return false;"><i class="glyphicon glyphicon-search"></i></a>');
            });
            result += "<ul>";
            $('#css_info_msg').html(result);
            },
        statusCode: {
            404: function() {
                $('.loading').css('display','none');
                $('#css_error').css('display','block');
                
            },
            405: function() {
                $('.loading').css('display','none');
                $('#css_error').css('display','block');
                
            },
            500: function() {
                $('.loading').css('display','none');
                $('#css_error').css('display','block');
                
            }
          }
      });
    
}
function sendCss(id_css){
    $('.error').css('display','none');
    $('#css_info_'+id_css).remove();
    $('#css_'+id_css).append(genLoading(id_css));
    $('#loading_css_'+id_css).css('display','block');
    $.ajax({
        method: "POST",
        dataType: "html",
        data: {
            id: id_css,
            token: $('#token').val(),
        },
        url: "/api/css/",
        success: function(data) {
            //alert('llega');
            $('#loading_css_'+id_css).remove();
            
            $('.loading').css('display','none');
            $('#css_'+id_css).append('<div id="css_info_'+id_css+'">\
                    <h3>The CSS file was successfully created</h3>\
                    </div>');
            getCss(id_css);
        },
        statusCode: {
            404: function() {
                $('.loading').css('display','none');
                $('#css_error').css('display','block');
                
            },
            500: function() {
                $('.loading').css('display','none');
                $('#css_error').css('display','block');
                
            }
          }
      });
    
}
function getCss(id_css){
    $('.error').css('display','none');
    $('.loading').css('display','none');
    $.ajax({
        method: "GET",
        dataType: "json",
        url: "/api/css/"+id_css+"/"+$('#token').val(),
        success: function(data) {
            $('#css_info_'+id_css).html("");
            $('#css_info_'+id_css).append('<h4>Original compressed</h4>\
                    <textarea onclick="this.focus();this.select();return false;">'+data.original_compressed+'</textarea>\
                    <h4>Clean without unsued rules</h4>\
                    <textarea onclick="this.focus();this.select();return false;">'+data.beauty+'</textarea>\
                    <h4>Clean without unsued rules compressed</h4>\
                    <textarea onclick="this.focus();this.select();return false;">'+data.beauty_compressed+'</textarea>\
                    </div>');
        },
        statusCode: {
            404: function() {
                $('.loading').css('display','none');
                $('#css_error').css('display','block');
                
            },
            500: function() {
                $('.loading').css('display','none');
                $('#css_error').css('display','block');
                
            }
          }
      });
    
}
function genLoading(id){
    return '<div class="row loading" id="loading_css_'+id+'">\
        <div  class="col-lg-12 col-md-12 col-sm-12 col-xs-12">\
        <div class="alert alert-info" role="alert">\
            <i class="fa fa-spinner fa-spin"></i> analyzing CSS...\
        </div>\
    </div>\
    </div>';
}