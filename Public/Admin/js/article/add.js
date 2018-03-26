$(function(){
	ue= UE.getEditor('article_desc');
	$('.article').click(function(event) {
		var title =$('#title').val();
		var cate_id =$('#cate_id option:selected').val();
		var articleId = $('#articleId').val();
		var article_desc =ue.getContent();
		if(title == ''){
			layer.msg('文章名称不能为空',{icon:7});
			return ;
		}
		if(cate_id == ''){
			layer.msg('文章栏目不能为空',{icon:7});
			return ;
		}
		if(article_desc == ''){
			layer.msg('文章内容不能为空',{icon:7});
			return ;
		}
		var data = {};
		data.title = title
		data.cate_id = cate_id
		data.article_desc = article_desc

		var url = 'add';
		if(articleId){
			var url = 'edit';
			data.articleId = articleId;
		}
		$.ajax({
			url: 'Admin-article-'+url,
			type: 'post',
			dataType: 'json',
			data: data,
			success:function(data) {
				if(data.err_code == 0){    
					layer.msg(data.err_msg, {icon: 6});
					setTimeout(
						'window.location.href = "Admin-article-index"',1000)
			   }else{
				   layer.msg(data.err_msg, {icon: 7});

			   }
			}
		})
	});
})