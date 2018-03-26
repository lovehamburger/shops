$(function(){
	$('.attrAdd').click(function(event) {
		var attr_name = $('input[name=attr_name]').val();

		var attrId = $('.attrId').val();
		if(attr_name == ''){
			layer.msg('属性名称不能为空',{icon:7});
			return;
		}
		var attr_type = $('input[name=attr_type]:checked').val();
		var attr_values = $('textarea[name=attr_values]').val();
		var type_id = $('option[name=typeId]:selected').val();
		if(attr_type == 1){
			if(attr_values == ''){
				layer.msg('单选类型的属性可选值不能为空',{icon:7});
				return;
			}
		}
		var url = 'ajaxAddAttr';
		var data = {};
		if(attrId){
			url = 'ajaxEditAttr';
			data.attrId = attrId;
		}
		data.attr_name = attr_name;
		data.attr_type = attr_type;
		data.attr_values = attr_values;
		data.type_id = type_id;
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