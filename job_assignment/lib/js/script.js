$(document).ready(function() {
    $('#show_check_post').select2();
    $(document).on('change','#show_check_post',function(){
    	var post_type = [];
    	$(this).each(function(){
    		post_type.push($(this).val());
    	});
    	var data = {'action':'post_type_post','post_type':post_type};
    	$.post(ajaxurl, data, function(response, status) {
    		if(response.data.data != ''){
    			$('#post-custom').html(response.data.data);
    		}else{
    			$('#post-custom').html('');
    		}
		  	
		});
    });
});