;
(function($){
	$(document).ready(function(){
		$.fn.extend({
			uploader : function(options){
				var option = options || {};
				this.each(function(){
					var options = option;
					var self = $(this);
					var tag = self.data('tag') || 'invoice';
					//上传按钮工具函数
					var btnTools = {
						upload: function(input){
							var uploadButtonText = input.nextAll('div.app-upload').find('.qq-upload-button span.upload-btn-text');
							!uploadButtonText.data('text') && uploadButtonText.data('text', uploadButtonText.text()).text('正在上传...');
						},
					    reset: function(input){
						    var uploadButtonText = input.nextAll('div.app-upload').find('.qq-upload-button span.upload-btn-text');
						    uploadButtonText.data('text') && uploadButtonText.text(uploadButtonText.data('text')) && uploadButtonText.data('text', null);
					    }
					}
					var qqOptions = {
						request:{endpoint:'/?c=admin&a=save_image', inputName:'attachement', uuidName:'id', params:{uploader:'qq'}},
					    deleteFile:{enabled:true, endpoint:'/?c=admin&a=delete_image', method:'POST', params:{}},
					    debug:true,
					    text: {
						    uploadButton:'<span class="upload-btn-text">上传附件</span>',
						    dragZoneText:'拖动到此处以上传文件'
					    },
						validation: {
							itemLimit: 1,
							allowedExtensions: ['jpeg', 'jpg', 'gif', 'png','bmp']
						},
					    callbacks:{
						    onSubmit:function(){
							    btnTools.upload(self);
						    },
						    onError:function(e, id, filename, msg, request)
						    {
							    var errorMsg = (request.status != 200) ? request.statusText : msg;
							    //alert(errorMsg, {closeToParent:true, target:self});
						    },
						    onComplete:function(e, id, filename, data){
							    btnTools.reset(self);
							    if(data && data.success && data.newUuid)
							    {
								    var list = !self.val() ? [] : self.val().split(',');
								    list.push(data.newUuid);
								    var targetInput = self.data('input');
								    $('input[name='+targetInput+']').val(list.join(','));
								    if(options.preview){//为了加一个链接预览
									    $('input[name='+targetInput+']').parentsUntil("div.control-group").find('.qq-upload-success:last-child').append('<a style="color:#fff;font-size:12px;" target="_blank" href="'+options.preview_baseurl+data.newUuid+'">预览</a>');;
								    }
								    self.val(list.join(','));
									self.attr('data-id', data.url);
							    }
						    },
						    onDeleteComplete:function(e, id, request){
							    var data = $.parseJSON(request.response);
							    if(data && data.success)
							    {
								    var list = !self.val() ? [] : self.val().split(',');
								    delete list[list.indexOf(data.id)];
								    var targetInput = self.data('input');
								    $('input[name='+targetInput+']').val(list.join(','));
								    self.val(list.filter(function(elem){return typeof elem !== 'undefined';}).join(','));
							    }
						    }
					    }
					}; 
					//上传文件模板
					var templates = arguments.callee.templates = arguments.callee.templates || {
						'default' : '<div class="qq-uploader span12">' +
							'<pre class="qq-upload-drop-area"><span>{dragZoneText}</span></pre>' +
							'<div class="qq-upload-button btn">{uploadButtonText}</div>' +
							'<span class="qq-drop-processing"><span>{dropProcessingText}</span><span class="qq-drop-processing-spinner"></span></span>' +
							'<ul class="qq-upload-list" style="margin-top: 10px; text-align: center;"></ul>' +
							'</div>',
						'mobile': '<div class="qq-uploader">' +
							'<pre class="qq-upload-drop-area"><span>{dragZoneText}</span></pre>' +
							'<div class="qq-upload-button upload-button background-button">{uploadButtonText}</div>' +
							'<span class="qq-drop-processing"><span>{dropProcessingText}</span><span class="qq-drop-processing-spinner"></span></span>' +
							'<ul class="qq-upload-list" style="margin-top: 10px; text-align: center;"></ul>' +
							'</div>'
					}; 
					//选择模板
					options.template = options.template || 'default';
					if(options.template)
					{
						if(templates.hasOwnProperty(options.template))
						{
							qqOptions.template = templates[options.template];
						}
						else
						{
							qqOptions.template = templates['default'];
						}
					}
					var container = $('<div class="app-upload"></div>').insertAfter(self);
					container.fineUploader(qqOptions);
					for(var name in qqOptions.callbacks)
					{
						container.on(name.substring(2, 3).toLowerCase() + name.substring(3), qqOptions.callbacks[name]);
					}
					container.data('input', self);
					//隐藏进度列表
					if(options.hideList) container.find('ul.qq-upload-list').hide();
					if(options.initCallback) options.initCallback(); 
				});
			}
		});
		 $('#certificate_pic').uploader();

         $('#add-ad').on('click', function(event){
		     event.preventDefault();
			 var param = {
				 'weixin_app_id': $("#app-id").val(),
			     'image_url': $("#certificate_pic").attr('data-id')
			}
			$.ajax({
				type: "POST",
				url: "/?c=admin&a=save_advertisement",
				data: param,
				success: function(msg){
					if(msg.code == 0){
						window.location.href = msg.data.redirect_url;
					}else{
						alert(msg.message);
					}
				}
			});

		 })


		 $('.delete-ad').on('click',function(event){
			event.preventDefault();
			var adver_id=$(this).data("id");
			$('#ad-id').val(adver_id);
			$('#de-ad').modal();

		});

		$('#on-ad').on('click',function(event){
			event.preventDefault();
			var param = {
		        'id':$('#ad-id').val()
			}
			$.ajax({
			    type: "POST",
				url: "/?c=admin&a=remove_advertisement",
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
		$('#off-ad').on('click',function(event){
			event.preventDefault();
			$('#de-ad').modal('hide');
			
		});


	});
})(jQuery);
