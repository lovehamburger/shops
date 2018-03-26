$(function(){
	$('input[name=brand_logo]').wrap('<form id="brand_logo" target="up" action="Admin-Brand-upload" method="post" enctype="multipart/form-data"></form>');
	  $('input[name=brand_logo]').change(function(event) {
	  $('#brand_logo').submit();
	  });
	$('.brand').click(function(event) {
		var imgurl =$('#imgurl').val();
		var brand_name =$('#brand_name').val();
		var brand_url =$('#brand_url').val();
		var brandId = $('#brandId').val();
		if(brand_name == ''){
			layer.msg('品牌名称不能为空',{icon:7});
			return ;
		}
		var data = {};
		data.imgUrl = imgurl
		data.brandName = brand_name
		data.brandUrl = brand_url

		var url = 'add';
		if(brandId){
			var url = 'edit';
			data.brandId = brandId;
		}
		$.ajax({
			url: 'Admin-Brand-'+url,
			type: 'post',
			dataType: 'json',
			data: data,
			success:function(data) {
				if(data.err_code == 0){    
					layer.msg(data.err_msg, {icon: 6});
					setTimeout(
						'window.location.href = "Admin-Brand-index"',1000)
			   }else{
				   layer.msg(data.err_msg, {icon: 7});

			   }
			}
		})
	});
})