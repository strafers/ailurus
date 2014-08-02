;
(function($){
    $(document).ready(function(){
		$("#appname").on('blur', function(){
		    var app_name = $('#appname').val();
			var re = new RegExp("^[a-zA-Z]+$");
			if (!re.test(app_name)){
				if($("#appname").nextAll().length <1)
				    $("#appname").after("<span class='text-error'>应用名称必须为字母</span>");
			}
		})
		$("#token").on('blur', function(){
		    var token = $("#token").val();
			var token_re = new RegExp("^[a-zA-Z0-9]+$");
			if(!token_re.test(token)){ 
				if($("#token").nextAll().length <=2)
			    {  
				    if($("#token").nextAll().length == 2)
		                $("#token").next().remove();	
				    $("#token").after("<span class='text-error'>token为字母或数字,长度至少为7位</span>");
				    return;
				}
			}
			if(token.length <=6){
				if($("#token").nextAll().length <=2)
			    {  
				    if($("#token").nextAll().length == 2)
		                $("#token").next().remove();	
                    $("#token").after("<span class='text-error'>token长度不符合要求</span>");
					
				}
			}

		})
	    $('#create-app').on('click', function(event){
		    event.preventDefault();
			var param = {
				'app_name': $("#appname").val(),
		        'app_desc': $("#appdetail").val(),
		        'token': $("#token").val(),
		        'action_class':$('#action-class') .val()
			}
			console.log(JSON.stringify(param));
			$.ajax({
			    type: "POST",
				url: "/?c=admin&a=save_app",
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


	    $('.detail-token').mouseover(function(){
	    	$('a.anchor-token').show();

	    }).mouseout(function(){
	    	$('a.anchor-token').hide();
	    });

	    $('.detail-action').mouseover(function(){
	    	$('a.anchor-action').show();
	    }).mouseout(function(){
	    	$('a.anchor-action').hide();
	    });

	    $('.detail-api').mouseover(function(){
	    	var target=$(this);
	    	target.find('a.anchor-api').show();
	    }).mouseout(function(){
	    	var target=$(this);
	    	target.find('a.anchor-api').hide();
	    });




		$('.modify-app').on('click', function(event){
			event.preventDefault();
			var app_id=$(this).data("id");
			var param = {"app_id": app_id};
			$.ajax({
			    type: "GET",
				url: "/?c=admin&a=edit_app",
                data: param,

				success: function(msg){
					console.log(msg);
			        if(msg.code == 0){
					    $("#appname-modal").val(msg.data.app_name);
					    $("#desc-modal").val(msg.data.app_desc);
					    $("#token-modal").val(msg.data.token);
					    $("#class-modal").val(msg.data.action_class);
					    $('#appid').val(app_id);
					}else{
					    alert(msg.message);
					}
					
				}
			});
			$('#app-info').modal('show');
		});
		$('#app-save').on('click',function(event){
			event.preventDefault();
			var param = {
				'app_name': $("#appname-modal").val(),
		        'app_desc': $("#desc-modal").val(),
		        'token': $("#token-modal").val(),
		        'action_class':$('#class-modal') .val(),
		        'app_id':$('#appid').val()
			}
			console.log(JSON.stringify(param));
            $.ajax({
			    type: "POST",
				url: "/?c=admin&a=update_app",
                data: param,

				success: function(msg){
					console.log(msg);
			        if(msg.code == 0){
					    window.location.href=msg.data.redirect_url;
					}else{
					    alert(msg.message);
					}
					
				}
			});
		});

		$('#app-close').on('click',function(event){
			event.preventDefault();
			$('#app-info').modal('hide');
		});


		$('.delete-app').on('click',function(event){
			event.preventDefault();
			var app_id=$(this).data("id");
			$('#appid1').val(app_id);
			$('#de-app').modal();

		});

		$('#on-dele').on('click',function(event){
			event.preventDefault();
			var param = {
		        'app_id':$('#appid1').val()
			}
			$.ajax({
			    type: "POST",
				url: "/?c=admin&a=delete_app",
                data: param,
				success: function(msg){
					console.log(msg);
			        if(msg.code == 0){
					    window.location.href=msg.data.redirect_url;
					}else{
					    alert(msg.message);
					}
					
				}
			});

		});
		
		$('#off-dele').on('click',function(event){
			event.preventDefault();
			$('#de-app').modal('hide');			

		});





	});
	
})(jQuery);
