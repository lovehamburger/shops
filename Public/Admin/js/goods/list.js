$(function(){
var _this=this;
var nums = 5;
var Posts=$('.goods-list');
this.dt={};
this.each_data=function(){
	  _this.dt = {
		'pageCount':nums,
	  }
	};
  this.init=function(url){
	_this.each_data();
	 $.ajax({
	  type: "post",
	  url:url, 
	  data:_this.dt,
	  success: function(data) {
		var count=data.count;
		var pagesnum = Math.ceil(count/nums);
		var tc = data.data;
		// var thstr;
		if(data.err_code==0) {
			if (tc.length>0) {
				Posts.html(init_html(tc));
			}
		}
		var laypageindex = laypage({
		  cont: 'biuuu_list', 
		  skin: '#fb771f',
		  pages: pagesnum, 
		  curr: 1, 
		  prev: '上一页', 
		  next: '下一页', 
		  jump: function(obj, first) {
			if(!first) {
			  _this.dt['currPage'] = obj.curr;
			  GetList("Admin-Goods-ajaxGetGoodList")
			}
		  }
		})
	  }
	});
}
init_html=function (data){
	var _html='';
	$.each(data,function(index, object) {
		if(object.sm_thumb.length > 0){
			var imgUrl ="<img src='"+object.sm_thumb+"'>";
		}else{
			var imgUrl = "暂无商品图片";
		}
		if(object.onsale == 1){
			var onsale = '上架';
		}else{
			var onsale = '下架';
		}
		if(object.brand_name == null){
			var brand_name = '暂无品牌';
		}else{
			var brand_name = object.brand_name;
		}
		_html += "<tr class='poid'  poid='"+object.id+"'><td align='center'>"+object.id+"</td>"+
					"<td align='center'>"+object.goods_name+"</td>"+
					"<td align='center'>"+imgUrl+"</td>"+
					"<td align='center'>"+object.market_price+"</td>"+
					"<td align='center'>"+object.shop_price+"</td>"+
					"<td align='center'>"+onsale+"</td>"+
					"<td align='center'>"+object.catename+"</td>"+
					"<td align='center'>"+brand_name+"</td>"+
					"<td align='center'>"+
					'<a class="btn btn-primary btn-sm shiny" href=Admin-Goods-product-goodsId-'+object.id+'> <i class="fa fa-edit"></i>货品列表</a>'+
					'<a class="btn btn-primary btn-sm shiny" href=Admin-Goods-publish-goodsId-'+object.id+'> <i class="fa fa-edit"></i>编辑</a>'+
					"<a class='btn btn-danger btn-sm shiny delete' href='#' ><i class='fa fa-trash-o'></i>删除</a>"+
					"</td></tr>";
	
	});	
	return _html;
}
  _this.init("Admin-Goods-ajaxGetGoodList");

  Posts.on('click','.delete',function(event) {
  	//var adminId = .push();
  	goodsId = $(this).parent().parent('.poid').attr('poid');
	layer.confirm('真的要删除吗？', {
	  btn: ['确定', '在想想'] //可以无限个按钮
	}, function(index, layero){
	 $.ajax({
	 	url: 'Admin-Goods-del',
	 	type: 'post',
	 	dataType: 'json',
	 	data: {goodsId: goodsId},
	 	success:function(data){
	 		if(data.err_code == 0){
	 			layer.msg(data.err_msg, {icon: 6});
	 			setTimeout('window.location.href="Admin-Goods"',1000);
	 		}else{
	 			layer.msg(data.err_msg, {icon: 7});
	 		}
	 	}
	 })
	});
  });
  // 调用分页
  function GetList(url) {
	   $.ajax({
			type: "post",
			url: url,
			data:_this.dt,
			success: function (data) {
			var tc = data.data;
			if(data.err_code==0) {
				if (tc.length > 0) {
					  Posts.html(init_html(tc));
				}else{
					  Posts.html(data.err_msg);
				}   
			} 
			else {
				Posts.html('');
				layer.msg(data.err_msg,{icon:2});
			 }
			}
		});
	  }
})