$(function(){
	$('.sub_btn').click(function(event) {
		var Validator = true;
		var username = $('#username').val();
		var password = $('#password').val();
		var adminId = $('#adminId').val();
		if(username == ''){
			layer.msg('用户名不能为空', {icon: 7});
			Validator = false;
			return;
		}
		if(!adminId){
			if(password == ''){
				layer.msg('密码不能为空', {icon: 7});
				Validator = false;
				return;
			}
		}
		if(!Validator) return;
		var data = {};
		var url = 'add';
		if(adminId){
			var url = 'edit';
			data.adminId = adminId;
		}
		data.userName = username;
		data.passWord = password;
		$.ajax({
			url: 'Admin-admin-'+url,
			type: 'POST',
			dataType: 'json',
			data: data,
			success: function (data) {
			   if(data.err_code == 0){     
					layer.msg(data.err_msg, {icon: 6});
					setTimeout(
						'window.location.href = "Admin-admin-list"',1000)
			   }else{
				   layer.msg(data.err_msg, {icon: 7});

			   }
			},
		})
	});
})