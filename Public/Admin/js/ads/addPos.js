$(function(){
	$('.adsPos').click(function(event) {
		var pname =$('#pname').val();
		var width =$('#width').val();
		var height =$('#height').val();
		var posId = $('#adsPosId').val();
		if(pname == ''){
			layer.msg('广告位名称不能为空',{icon:7});
			return ;
		}
		if(width == ''){
			layer.msg('文章宽度不能为空',{icon:7});
			return ;
		}
		if(height == ''){
			layer.msg('文章高度不能为空',{icon:7});
			return ;
		}
		var data = {};
		data.pname = pname
		data.width = width
		data.height = height

		var url = 'setPos';
		if(posId){
			var url = 'editPos';
			data.posId = posId;
		}
		$.ajax({
			url: 'Admin-ads-'+url,
			type: 'post',
			dataType: 'json',
			data: data,
			success:function(data) {
				if(data.err_code == 0){    
					layer.msg(data.err_msg, {icon: 6});
					setTimeout(
						'window.location.href = "Admin-ads-index"',1000)
			   }else{
				   layer.msg(data.err_msg, {icon: 7});

			   }
			}
		})
	});
})