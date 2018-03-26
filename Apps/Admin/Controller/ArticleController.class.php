<?php
namespace Admin\Controller;
use Admin\Controller\BaseController;
class ArticleController extends BaseController {

	//文章栏目待开发

	public function index(){
		$this->display('Article/index');
	}

	public function list(){
		//权限和ajax验证
		$this->_inputAjax();
		$param['page'] = I('post.currPage',1);
		$param['limit'] = I('post.pageCount',10);
		$count = D('Article')->getArticleCount($param);
		if($count > 0){
			$res = array_err(0,'success');
			$res['data'] = D('Article')->getArticles($param);
			$res['count'] = $count;
			$this->ajaxReturn($res);
		}else{
			$this->ajaxReturn(array_err(0,'不存在文章列表'));
		}
	}

	public function publish(){
		$articleId = I('get.ArticleId');
		$articleCate = D('Article')->getCate();   
		if(!empty($articleId)){
			//检查
			$checkRes = A('Article','Event')->_checkArticleId($articleId);
			if($checkRes['err_code'] > 0) $this->error($checkRes['err_msg']);
			$this->assign('articleRes',$checkRes);
		}
		$this->assign('articleCate',$articleCate);
		$this->display('Article/add');
	}
	/**
	 * 文章添加
	 */
	public function add(){
		$this->_inputAjax();
		$data = [];
		$checkInfo = $this->_checkDate($data);
		if($checkInfo['err_code'] > 0){
			$this->ajaxReturn($checkInfo);
		}
		//添加
		$articleId = D('Article')->setArticle($data);
		if($articleId > 0){
			$this->ajaxReturn(array_err(0,'添加文章成功'));
		}
		$this->ajaxReturn(array_err(991,'添加文章失败'));
	}
	/**
	 * 文章修改
	 * @return [type] [description]
	 */
	public function edit(){
		$this->_inputAjax();
		$articleId = I('post.articleId');
		$checkRes = A('Article','Event')->_checkArticleId($articleId,true);
		if($checkRes['err_code'] > 0) $this->ajaxReturn($checkRes);
		$data = [];
		$checkInfo = $this->_checkDate($data,$articleId);
		if($checkInfo['err_code'] > 0){
			$this->ajaxReturn($checkInfo);
		}
		//修改
		$mArticle = D('Article');
		$mArticle->startTrans();
		$data['id'] = $articleId;
		$flag = $mArticle->editArticle($data);
		if($flag !== false){
			$mArticle->commit();
			$this->ajaxReturn(array_err(0,'修改文章成功'));
		}
		$mArticle->rollback();
		$this->ajaxReturn(array_err(991,'修改文章失败'));
	}

	public function del(){
		$this->_inputAjax();
		$articleIdRes = json_decode(htmlspecialchars_decode(I('post.articleId')), true);
		$param['articleId'] = $articleIdRes;
		$mArticle = D('Article');
		$mArticle->startTrans();
		$res = $mArticle->getArticlesLock($param,true);   
		if(count($res) != count($articleIdRes)) $this->ajaxReturn(array_err(776,'存在非法标识,请核实'));
		$flag = $mArticle->delArticle($param);

		if($flag === false){
			$mArticle->rollback();
			$this->ajaxReturn(array_err(555,'删除文章失败哦'));
		}
		$mArticle->commit();
		$this->ajaxReturn(array_err(0,'删除文章成功'));

	}
	
	public function _checkDate(&$data,$articleId){
		$cate_id = I('post.cate_id');
		$title = I('post.title');
		$article_desc = I('post.article_desc');
		if(empty($article_desc)) return array_err(999,'文章内容不能为空');
		if(empty($title)) return array_err(998,'文章标题不能为空');
		if(empty($cate_id)) return array_err(997,'文章栏目不能为空');
		#@todo验证栏目是否存在
		$data['cateid'] = $cate_id;
		$data['title'] = $title;
		$data['content'] = $article_desc;
		return array_err(0,'success');
	}
}