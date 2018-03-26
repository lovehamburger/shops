$(function(){
	$('.typeAdd').click(function(event) {
		var type_name = $('input[name=type_name]').val();

		var typeId = $('.typeId').val();
		if(type_name == ''){
			layer.msg('类型名称不能为空',{icon:7});
			return;
		}
		var url = 'ajaxAddType';
		var data = {};
		if(typeId){
			url = 'ajaxEditType';
			data.typeId = typeId;
		}
		data.typeName = type_name;
		$.ajax({
			url: 'Admin-Goods-'+url,
			type: 'post',
			dataType: 'json',
			data: data,
			success:function(data) {
				if(data.err_code == 0){    
					layer.msg(data.err_msg, {icon: 6});
					setTimeout(
						'window.location.href = "Admin-Goods-getType"',1000)
			   }else{
				   layer.msg(data.err_msg, {icon: 7});

			   }
			}
		})
	});
})