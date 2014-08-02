;
(function($){
	$(document).ready(function(){
        
        
		$('#login-btn').on('click',function(e){
			e.preventDefault();
			$('.topbar-dropdown').toggle();
        });

        $('.btnSubmit').on('click',function(event){
            event.preventDefault();
            $.ajax({
                type: "POST",
                url: "/?a=login",
                data: "email="+$('#login_email').val()+"&password="+$('#login_password').val()+"&hash="+$('#hash').val(),

                success: function(msg){
                    
                    console.log(msg);
                    if (msg.code==0) {    
                        window.location.href = "/?c=admin&a=weixin_apps";
                    }
                    else{
                        alert("用户名或密码错误");
                    }               
                }
            });
        });
       
	

	});
})(jQuery);


