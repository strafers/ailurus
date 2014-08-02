;
(function($){
    $(document).ready(function(){
	    $('#edit-config').on('click', function(event){
		    event.preventDefault();
			var param = {
				'devteam': $("#devteam").val(),
		        'keywords': $("#keywords").val(),
		        'site_name': $("#sitename").val(),
		        'use_ssl':$('#use-ssl input:radio:checked').val(),
		        'site_open':$('#site-open input:radio:checked').val()
			}
			console.log(JSON.stringify(param));
			$.ajax({
			    type: "POST",
				url: "/?c=admin&a=save_config",
                data: param,
				success: function(msg){
			        if(msg.code == 0){
					    window.location.href = msg.data.redirect_url;
					}else{
					    alert(msg.message);
					}
				}
			});
		});
	});
})(jQuery);