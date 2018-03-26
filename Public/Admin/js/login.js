$(function(){
	var login = $('.login');
	var Validator = true;
	
	login.click(function(event) {
		var username = $("input[name=username]").val();
		var password = $("input[name=password]").val();
		var verify = $("input[name=verify]").val();
		if(username == ''){
			layer.msg('用户名不能为空',{icon:7});
			Validator = false;
			return;
		}
		if(password == ''){
			layer.msg('密码不能为空',{icon:7});
			Validator = false;
			return;
		}
		if(verify == ''){
			layer.msg('验证码不能为空',{icon:7});
			Validator = false;
			return;
		}
		var data = {};
		data.verify = verify;
		data.userName = username;
		data.passWord = password;
		$.ajax({
			url: 'Admin-Login-login',
			type: 'post',
			dataType: 'json',
			data: data,
			success : function(data) {
				if(data.err_code == 0){
					window.location.href = "Admin-index";
				}else{
					layer.msg(data.err_msg,{icon:7});					
				}
			}
		})
	});
})