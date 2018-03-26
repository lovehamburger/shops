/*
 * 微博配图上传JS插件
 * */
$(function () {
	var lee_pic = {
			uploadTotal : 0,
			uploadLimit : 8,
			uploadify : function () {
				//文件上传测试
				$('#file').uploadify({
					swf : ThinkPHP['UPLOADIFY'] + '/uploadify.swf',
					uploader : 'Admin-Goods-uploadPic',
					width : 120,
					height : 35,
					fileTypeDesc : '图片类型',
					buttonCursor : 'pointer',
					buttonText : '上传图片',
					fileTypeExts : '*.jpeg; *.jpg; *.png; *.gif',
					fileSizeLimit : '1MB',
					overrideEvents : ['onSelectError','onSelect','onDialogClose'],
					onSelectError : function (file, errorCode, errorMsg) {
						switch (errorCode) {
							case -110 : 
								$('#error').dialog('open').html('超过1024KB...');
								setTimeout(function () {
									$('#error').dialog('close').html('...');
								}, 1000);
								break;
						}
					},
					onUploadStart : function () {
						if (lee_pic.uploadTotal == 8) {
							$('#file').uploadify('stop');
							$('#file').uploadify('cancel');
							$('#error').dialog('open').html('限制为8张...');
							setTimeout(function () {
								$('#error').dialog('close').html('...');
							}, 1000);
						} else {
							$('.goods_pic_list').append('<div class="pic_content" style="display:inline;"><span class="remove"></span><span class="text">删除</span><img src="' + ThinkPHP['IMG'] + '/loading_100.png" class="pic_list"></div>');
						}
					},
					onUploadSuccess : function (file, data, response) {
						$('.goods_pic_list').append('<input type="hidden" clsss="img_val" name="images" value=' + data + '>')
						var imageUrl = $.parseJSON(data);
						lee_pic.thumb(imageUrl['sm_thumb']);
						lee_pic.hover();
						lee_pic.remove();
						lee_pic.uploadTotal++;
						lee_pic.uploadLimit--;
						$('.weibo_pic_total').text(lee_pic.uploadTotal);
						$('.weibo_pic_limit').text(lee_pic.uploadLimit);
					}
				});
			},
			hover : function () {
				var content = $('.weibo_pic_content');
				var len = content.length;
				$(content[len - 1]).hover(function () {
					$(this).find('.remove').show();
					$(this).find('.text').show();
				}, function () {
					$(this).find('.remove').hide();
					$(this).find('.text').hide();
				});
			},
			remove : function () {
				var remove = $('.pic_content .text');
				var len = remove.length;
				$(remove[len - 1]).on('click', function () {
					$(this).parent().next('input[name="images"]').remove();
					$(this).parent().remove();
					lee_pic.uploadTotal--;
					lee_pic.uploadLimit++;
					$('.weibo_pic_total').text(lee_pic.uploadTotal);
					$('.weibo_pic_limit').text(lee_pic.uploadLimit);
				});
			},
			thumb : function (src) {
				var img = $('.pic_list');
				var len = img.length;
				$(img[len - 1]).attr('src', src).hide();
				setTimeout(function () {
					if ($(img[len - 1]).width() > 100) {
						$(img[len - 1]).css('left', -($(img[len - 1]).width() - 100) / 2);
					}
					if ($(img[len - 1]).height() > 100) {
						$(img[len - 1]).css('top', -($(img[len - 1]).height() - 100) / 2);
					}
					$(img[len - 1]).attr('src', src).fadeIn();
				}, 50);
			},
			init : function () { 
				$('.profile14').click(function(event) {
					lee_pic.uploadify();
				});
				// $('#pic_box a.close').on('click',function(){
				// 	$('#pic_box').hide();
				// 	$('.pic_arrow_top').hide();
				// });
			},
	};
	lee_pic.init();
	window.uploadCount = {
		clear : function () {
			lee_pic.uploadTotal = 0;
			lee_pic.uploadLimit = 8;
		}
	};

		delPic = function(picId){
		layer.confirm('确定删除吗？同时会删除货品中的设置哦', {
	  			btn: ['确定', '在想想'] //可以无限个按钮
			},function(){
				
				$.ajax({
					url: 'Admin-Goods-delGoodsPic',
					type: 'post',
					dataType: 'json',
					data: {picId: picId},
					success:function(data){
				 		if(data.err_code == 0){
	 						layer.msg(data.err_msg, {icon: 6});
	 						//
				 		}else{
				 			layer.msg(data.err_msg, {icon: 7});
				 		}
				 	}
				})
			})
	}
});
