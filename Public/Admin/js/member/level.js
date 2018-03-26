$(function(){
var _this=this;
var nums = 5;
var Posts=$('.brand-list');
init_html=function (data){
	var _html='';
	$.each(data,function(index, object) {
		if(object.brand_logo.length > 0){
			var imgUrl ="<img src='"+object.brand_logo+"'>";
		}else{
			var imgUrl = "暂无品牌LOGO";
		}
		_html += "<tr class='poid'  poid='"+object.id+"'><td align='center'>"+object.id+"</td>"+
					"<td align='center'>"+imgUrl+"</td>"+
					"<td align='center'>"+object.brand_name+"</td>"+
					"<td align='center'>"+
					'<a class="btn btn-primary btn-sm shiny" href=Admin-Brand-publish-brandId-'+object.id+'> <i class="fa fa-edit"></i>编辑</a>'+
					"<a class='btn btn-danger btn-sm shiny delete' href='#' ><i class='fa fa-trash-o'></i>删除</a>"+
					"</td></tr>";
	
	});	
	return _html;
}
  _this.init("Admin-Brand-list");

  Posts.on('click','.delete',function(event) {
  	var brandId = [];
  	//var adminId = .push();
  	brandId.push($(this).parent().parent('.poid').attr('poid'));
	layer.confirm('真的要删除吗？', {
	  btn: ['确定', '在想想'] //可以无限个按钮
	}, function(index, layero){
	 $.ajax({
	 	url: 'Admin-Brand-del',
	 	type: 'post',
	 	dataType: 'json',
	 	data: {brandId: JSON.stringify(brandId)},
	 	success:function(data){
	 		if(data.err_code == 0){
	 			layer.msg(data.err_msg, {icon: 6});
	 			setTimeout('window.location.href="Admin-Brand"',1000);
	 		}else{
	 			layer.msg(data.err_msg, {icon: 7});
	 		}
	 	}
	 })
	});
  });
})