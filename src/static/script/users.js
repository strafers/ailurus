;
(function($){
    $(document).ready(function(){
	    $('#create_user').on('click', function(event){
		    event.preventDefault();
			var param = {
				'nickname': $("#username").val(),
		        'email': $("#email").val(),
		        'password': $("#password").val(),
		        'role':$('#role') .val()
			}
			$.ajax({
			    type: "POST",
				url: "/?c=admin&a=save_user",
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
		$('.modify-user').on('click', function(event){
			event.preventDefault();
			var user_id=$(this).data("id");
			var param = {"user_id": user_id};
			$.ajax({
			    type: "GET",
				url: "/?c=admin&a=edit_user",
                data: param,

				success: function(msg){
			        if(msg.code == 0){
					    $("#username-modal").val(msg.data.nickname);
					    $("#email-modal").val(msg.data.email);
					    $("#password-modal").val();
					    $("#role-modal").val(msg.data.role);
					    $('#userid').val(user_id);
					}else{
					    alert(msg.message);
					}
					
				}
			});
			$('#user-info').modal('show');
		});
		$('#user-save').on('click',function(event){
			event.preventDefault();
			var param = {
				'nickname': $("#username-modal").val(),
		        'email': $("#email-modal").val(),
		        'password': $("#password-modal").val(),
		        'role':$('#role-modal option:selected') .val(),
		        'user_id':$('#userid').val()
			}

            $.ajax({
			    type: "POST",
				url: "/?c=admin&a=update_user",
                data: param,

				success: function(msg){
			        if(msg.code == 0){
					    window.location.href=msg.data.redirect_url;
					}else{
					    alert(msg.message);
					}
					
				}
			});
		});

		$('#user-close').on('click',function(event){
			event.preventDefault();
			$('#user-info').modal('hide');
		});


		$('.delete-user').on('click',function(event){
			event.preventDefault();
			var user_id=$(this).data("id");
			$('#userid1').val(user_id);
			$('#de-user').modal();

		});

		$('#on-de').on('click',function(event){
			event.preventDefault();
			var param = {
		        'user_id':$('#userid1').val()
			}
			$.ajax({
			    type: "POST",
				url: "/?c=admin&a=delete_user",
                data: param,
				success: function(msg){
			        if(msg.code == 0){
					    window.location.href=msg.data.redirect_url;
					}else{
					    alert(msg.message);
					}
					
				}
			});

		});
		$('#off-de').on('click',function(event){
			event.preventDefault();
			$('#de-user').modal('hide');
			

		});
	});
})(jQuery);
