$(function(){
	$('input[name=original]').wrap('<form id="original" target="up" action="Admin-Goods-upload" method="post" enctype="multipart/form-data"></form>');
	  $('input[name=original]').change(function(event) {
	  $('#original').submit();
	  });
	ue= UE.getEditor('goods_desc');
	$('.goods').click(function(event) {
		var imgurl =$('#imgurl').val();
		var goods_name =$('input[name=goods_name]').val();
		var goodsId =$('input[name=goodsId]').val();
		var cate_id =$('#cate_id option:selected').val();
		var type_id =$('#type_id option:selected').val();
		var brand_id =$('#brand_id option:selected').val();
		var goods_desc =ue.getContent();
		var market_price = $('input[name=market_price]').val();
		var shop_price = $('input[name=shop_price]').val();
		var goods_weight = $('input[name=goods_weight]').val();
		var mp = $('.mp').val();
		var onsale = $('input[name=onsale]').is(':checked');
		if(goods_name == ''){
			layer.msg('商品名称不能为空',{icon:7});
			return ;
		}
		if(cate_id == ''){
			layer.msg('商品分类不能为空',{icon:7});
			return ;
		}
		if(type_id == ''){
			layer.msg('商品类型不能为空',{icon:7});
			return ;
		}
		if(market_price == ''){
			layer.msg('市场价格不能为空',{icon:7});
			return ;
		}
		if(isNaN(market_price)){
			layer.msg('市场价格必须是数字',{icon:7});
			return ;
		}
		if(shop_price == ''){
			layer.msg('本店价格不能为空',{icon:7});
			return ;
		}
		if(isNaN(shop_price)){
			layer.msg('本店价格必须是数字',{icon:7});
			return ;
		}
		if(goods_desc == ''){
			layer.msg('商品描述不能为空',{icon:7});
			return ;
		}
		if(goods_weight == ''){
			layer.msg('商品重量不能为空',{icon:7});
			return ;
		}
		if(isNaN(goods_weight)){
			layer.msg('商品重量必须是数字',{icon:7});
			return ;
		}
		var mpLevels = [];
		$('.mp').each(function(i, val) {
			var mpLevel = {};
			var level = $('.mp').eq(i).val();
			if(isNaN(level)){
				layer.msg('会员价格必须是数字',{icon:7});
				return ;
			}
			if(level != ''){
				mpLevel['id'] = $('.mp').eq(i).attr('level');
				mpLevel['level'] = level;
				mpLevels.push(mpLevel);
			}
		})
		/**
		 * 获取图片路径
		 * @type {Array}
		 */
		images = [];
		$('input[name=images]').each(function(i, val) {
			var goodsImg = $('input[name=images]').eq(i).val();
			images.push(goodsImg);
		})
		/**
		 * 是否上下架
		 * @param  {[type]} onsale [description]
		 * @return {[type]}        [description]
		 */
		if(onsale == true){
			onSale = 1;
		}else{
			onSale = 0;
		}
		var attr = {};
		$('input[name=attr_value]').each(function(){
			attr[$(this).attr('val')] = $(this).val();
		})
		$('select[name=attr_value]').each(function(){
			attr[$(this).attr('val')] = $(this).val();
		})
		$('.only').parent().parent().each(function(i,o){
			attrs = {};
			arr = [];
			$(this).find('select[name=attr_values]').each(function(index, val) {
				arrObj = {};
				arrObj['attr_values'] = $(this).val();
				arrObj['attr_price'] = $(this).parent().parent().find('input[name=attr_price]').val();
				arr.push(arrObj);
			});console.log(attrs);
			attr[$(this).attr('val')] = arr;
			console.log(attr);
		})
		var data = {};
		data.imgurl = imgurl
		data.goodsName = goods_name
		data.cateId = cate_id
		data.goodsDesc = goods_desc
		data.brandId = brand_id
		data.marketPrice = market_price
		data.shopPrice = shop_price
		data.goodsWeight = goods_weight
		data.onSale = onSale
		data.levelPrice = JSON.stringify(mpLevels)
		data.goodAttr = JSON.stringify(attr)
		data.images = images
		data.typeId = type_id

		var url = 'add';
		if(goodsId){
			var url = 'ajaxEdit';
			data.goodsId = goodsId;
			var oldAttr = {};
			$('input[name=old_attr_value]').each(function(){
				oldAttr[$(this).attr('val')] = $(this).val();
			})
			$('select[name=old_attr_value]').each(function(){
				oldAttr[$(this).attr('val')] = $(this).val();
			})
			$('.only').parent().parent().each(function(i,o){
			oldAttrs = {};
			oldArr = [];
			$(this).find('select[name=old_attr_value]').each(function(index, val) {
				oldArrObj = {};
				oldArrObj['attr_values'] = $(this).val();
				oldArrObj['attr_price'] = $(this).parent().parent().find('input[name=old_attr_price]').val();
				oldArrObj['goods_attr'] = $(this).parent().parent().find('.addAttr').attr('value');
				//oldArrObj['attr_price'] = $(this).parent().parent().find('input[name=old_attr_price]').val();
				oldArr.push(oldArrObj);
			});console.log(oldAttrs);
				oldAttr[$(this).attr('val')] = oldArr;
				console.log(oldAttr);
			})
		data.oldGoodAttr = JSON.stringify(oldAttr)
		}
		$.ajax({
			url: 'Admin-Goods-'+url,
			type: 'post',
			dataType: 'json',
			data:  data,
			success:function(data) {
				if(data.err_code == 0){    
					layer.msg(data.err_msg, {icon: 6});
					setTimeout(
						'window.location.href = "Admin-Goods-index"',1000)
			   }else{
				   layer.msg(data.err_msg, {icon: 7});

			   }
			}
		})
	});



	 //搜索指定的分类
	$("select[name=type_id]").change(function(){
		Posts = $('#attr');
		var type_id = $(this).val();
		if (type_id) {
			$.ajax({
			url: "Admin-Goods-ajaxGetAttr",
			type: 'post',
			dataType: 'json',
			data: {typeId: type_id},
			success:function(data){
				var tc = data.data;
				if(data.err_code==0) {
					if (tc.length > 0) {
						  Posts.html(init_html(tc));
					}else{
						  Posts.html(data.err_msg);
					}   
				}else {
					Posts.html('');
					layer.msg(data.err_msg,{icon:2});
				 }
			}
		})
		}else{
			$('#attr').children().remove();
		}
	});

	init_html = function (data) {
		var _html='';
		$.each(data,function(index, object){
			if(object.attr_values != '' && object.attr_type == 0){
				var attrs= new Array(); //定义一数组
				var option = '';
				attrs=object.attr_values.split(","); //字符分割 
				for (i=0;i<attrs.length ;i++ ) {
					option +="<option>"+attrs[i]+"</option>"
				}
				val = "<select val="+object.id+" name='attr_value'>"+
						option+
	                 "</select>";
			}else if(object.attr_values == '' && object.attr_type == 0){
				val = "<input type='text' val="+object.id+" name='attr_value' placeholder='' class='form-control'>"
			}else if(object.attr_values != '' && object.attr_type == 1){
				var attrs= new Array(); //定义一数组
				var option = '';
				attrs=object.attr_values.split(","); //字符分割 
				for (i=0;i<attrs.length ;i++ ) {
					option +="<option>"+attrs[i]+"</option>"
				}
				val = "<div class='form-group row only'>"+
						"<div class='col-sm-2'>"+
						"<select  name='attr_values'>"+
	                        option+
	                   "</select>"+
	                   "</div>"+
	                   "<label class='col-sm-2 control-label no-padding-right' for='username'>价格</label>"+
	                   "<div class='col-sm-3'>"+
	                   "<input type='text' name='attr_price' placeholder='' class='form-control'>"+
	                   "</div>"+
	                   "<span class='addAttr'>[+]</span>"+
	                   "</div>"
	                   
			}
			_html +="<div val="+object.id+" class='form-group'>"+
                        "<label class='col-sm-2 control-label no-padding-right'  for='username'>"+object.attr_name+"</label>"+
                        "<div class='col-sm-6'>"+
                            val+
                        "</div>"+
                    "</div>"
		});
		return _html;
	}
	$(document).on('click','.addAttr',function(event) {
		_remove = $(this);
		if($(this).html() == '[+]'){
			var newDiv = $(this).parent().parent().find('.only').html().replace("[+]","[-]");
  			$(this).parent().parent().append("<div class='form-group newDiv'>"+newDiv +"</div>");
  			$('.newDiv').find('.addAttr').attr('value','');
  			var goodsId =$('input[name=goodsId]').val();
  			if(goodsId){
  				$(this).parent().parent().find('.newDiv select[name=old_attr_value]').attr('name','attr_values');//修改的时候
  				$(this).parent().parent().find('.newDiv input[name=old_attr_price]').attr('name','attr_price');//修改的时候
  			}
  			num = $(this).parent().parent().find('.form-group').size();
  			if(num >= $(this).parent().find('option').size()){
  				$(this).last().html('');
  			}
			
		}else if($(this).html() == '[-]'){
			attrId = $(this).attr('value');
			if(attrId){
				layer.confirm('确定删除吗？同时会删除货品中的设置哦', {
	  			btn: ['确定', '在想想'] //可以无限个按钮
			},function(){
				
				$.ajax({
					url: 'Admin-Goods-delGoodsAttr',
					type: 'post',
					dataType: 'json',
					data: {attrId: attrId},
					success:function(data){
				 		if(data.err_code == 0){
	 						layer.msg(data.err_msg, {icon: 6});
	 						num = _remove.parent().parent().find('.form-group').size();
				  			_remove.parent().remove();
				  			if(num == 2){
				  				$('.only').find('.addAttr').html('[+]');
				  			}
				 		}else{
				 			layer.msg(data.err_msg, {icon: 7});
				 		}
				 	}
				})
			})
			}else{
				num = $(this).parent().parent().find('.form-group').size();
	  			$(this).parent().remove();
	  			if(num == 2){
	  				$('.only').find('.addAttr').html('[+]');
	  			}
			}	
		}	
  });
})