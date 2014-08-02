;
(function($){
	$(document).ready(function(){
		$('#publish').on('click',function(event){
			event.preventDefault();
			editor.sync();
			var param = {
				'title': $("#article-title").val(),
		        'weixin_app_id': $("#app-id").val(),
		        'content': editor.html()
			}
			console.log(JSON.stringify(param));
			$.ajax({
			    type: "POST",
				url: "/?c=admin&a=save_article",
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



		$('#update').on('click',function(event){
			event.preventDefault();
			var arti_id=$(this).data("id");
			$('#artid').val(arti_id);
			editor.sync();
			var param = {
				'title': $("#edit-title").val(),
		        'weixin_app_id': $("#app-id").val(),
		        'id':$('#artid').val(),
		        'content': editor.html()
			}
			console.log(JSON.stringify(param));
			$.ajax({
			    type: "POST",
				url: "/?c=admin&a=update_article",
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

		$('.delete-article').on('click',function(event){
			event.preventDefault();
			var article_id=$(this).data("id");
			$('#artid1').val(article_id);
			$('#de-article').modal();

		});

		$('#on-delet').on('click',function(event){
			event.preventDefault();
			var param = {
		        'id':$('#artid1').val()
			}
			$.ajax({
			    type: "POST",
				url: "/?c=admin&a=delete_article",
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
		$('#off-delet').on('click',function(event){
			event.preventDefault();
			$('#de-article').modal('hide');
			
		});

	});

	

})(jQuery);
