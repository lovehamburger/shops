$(function(){
	var cate = $('.cate');
	cate.click(function(event) {
		var catename = $('#catename').val();
		var id = $('input[name=cateId]').val();
		if(catename == ''){
			layer.msg('分类名称不能为空',{icon:7});
			return;
		}
		var pid = $('select[name=pid]').val();
		if(pid == ''){
			layer.msg('上级分类不能为空',{icon:7});
			return;
		}
		var data = {};
		data.cateName = catename;
		data.cateId = pid;
		if(id){
			url = 'edit';
			data.id = id;
		}else{
			url = 'add';
		}
		$.ajax({
			url: 'Admin-Cate-'+url,
			type: 'post',
			dataType: 'json',
			data: data,
			success: function (data) {
			   if(data.err_code == 0){     
					layer.msg(data.err_msg, {icon: 6});
					setTimeout(
						'window.location.href = "Admin-Cate"',1000)
			   }else{
				   layer.msg(data.err_msg, {icon: 7});

			   }
			},
		})
	});
})