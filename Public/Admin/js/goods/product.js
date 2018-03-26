$(function(){
	$(document).on('click','.product',function(event){
		var tr = $(this).parent().parent();
		if(tr.find('.product').val() == '+'){
			var newTr = tr.clone();
			newTr.find('.product').val('-');
			tr.after(newTr);
		}else{
			tr.remove();
		}
	})
	/**
	 * 获取货品列表数据
	 * @param  {Array}  event) {		var       arrs [description]
	 * @return {[type]}        [description]
	 */
	$('.addProduct').click(function(event) {
		var goodsId = $('input[name=goodsId]').val();
		var arrs = [];
		$('.products').each(function(index, el) {
			var product = {};
			arr = [];
			$(this).find('select[name=attr_name]').each(function(index1, el) {
				arr.push($(this).val());
			});
			product['goods_attr'] = arr;
			product['goods_number'] = $(this).find('input[name=goods_number]').val();
			arrs.push(product);
		});
		var url = 'ajaxAddProduct';
		$.ajax({
			url: 'Admin-Goods-'+url,
			type: 'post',
			dataType: 'json',
			data: {product: JSON.stringify(arrs),
					goodsId:goodsId
			},
			success : function(data){
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
})