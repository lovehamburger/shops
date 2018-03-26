$(function(){
	$('.member').click(function(event) {
		var level_name =$('input[name=level_name]').val();
		var points_min =$('input[name=points_min]').val();
		var points_max = $('input[name=points_max]').val();
		var rate = $('input[name=rate]').val();
		var levelId = $('#levelId').val();
		if(level_name == ''){
			layer.msg('会员等级名称不能为空',{icon:7});
			return ;
		}
		if(points_min == ''){
			layer.msg('会员等级下限不能为空',{icon:7});
			return ;
		}else if(level_name < 0){
			layer.msg('等级下限不能小于0',{icon:7});
		}
		if(points_max == ''){
			layer.msg('会员等级上限不能为空',{icon:7});
			return ;
		}else if(points_max < 0){
			layer.msg('等级上限不能小于0',{icon:7});
		}
		if(rate == ''){
			layer.msg('会员折扣率不能为空',{icon:7});
			return ;
		}else if(rate < 0){
			layer.msg('会员折扣率必须在0到100之间',{icon:7});
		}
		var data = {};
		data.level_name = level_name
		data.points_min = points_min
		data.points_max = points_max
		data.rate = rate
		var url = 'addLevel';

		if(levelId){
			var url = 'editLevel';
			data.levelId = levelId;
		}
		$.ajax({
			url: 'Admin-Member-'+url,
			type: 'post',
			dataType: 'json',
			data: data,
			success:function(data) {
				if(data.err_code == 0){    
					layer.msg(data.err_msg, {icon: 6});
					setTimeout(
						'window.location.href = "Admin-Member-level"',1000)
			   }else{
				   layer.msg(data.err_msg, {icon: 7});

			   }
			}
		})
	});
})