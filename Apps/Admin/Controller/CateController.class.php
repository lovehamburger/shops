<?php
namespace Admin\Controller;
use Admin\Controller\BaseController;
class CateController extends BaseController {
	public function index(){
		$mCate = D('Cate');
		$cateRes = $mCate->catesTree();
		$this->assign('cateRes',$cateRes);
		$this->display('Cate/index');
	}

	public function publish(){
		$cateId = I('get.cateId');
		if($cateId){
			$cate = A('Cate','Event')->_checkCateId($cateId,true);
			if($cate['err_code'] > 0){
				$this->error($cate['err_msg']);
				$this->ajaxReturn($cate);
			}
			$this->assign('cate',$cate);        
		}
		$mCate = D('Cate');
		$cateRes = $mCate->catesTree();
		$this->assign('cateRes',$cateRes);
		$this->display('Cate/add');
	}

	public function add(){
		$this->_inputAjax();
		$data = [];
		$checkCate = $this->_checkCate($data);
		if($checkCate['err_code'] > 0) $this->ajaxReturn($checkCate);        
		$cateId = D('Cate')->setCate($data);
		if($cateId > 0){
			S('catesTree',NULL);
			$this->ajaxReturn(array_err(0,'添加商品分类成功'));
		}else{
			$this->ajaxReturn(array_err(555,'添加商品分类失败'));
		}
	}

	public function edit(){
		$this->_inputAjax();
		$id = I('post.id');
		$data = [];
		$checkCate = $this->_checkCate($data,true);
		if($checkCate['err_code'] > 0) $this->ajaxReturn($checkCate);
		$checkCateId = A('Cate','Event')->_checkCateId($id,true);
		if($checkCateId['err_code'] > 0) $this->ajaxReturn($checkCateId);
		$mCate = D('cate');
		$mCate->startTrans();
		$cateId = $mCate->where(array('id'=>$id))->save($data);
		if($cateId !== false){
			S('catesTree',NULL);
			$mCate->commit();
			$this->ajaxReturn(array_err(0,'修改商品分类成功'));
		}else{
			$mCate->rollback();
			$this->ajaxReturn(array_err(555,'修改商品分类失败'));
		}
	}

	public function del(){
		$id = I('post.id');
		$checkCateId = A('Cate','Event')->_checkCateId($id);
		if($checkCateId['err_code'] > 0) $this->ajaxReturn($checkCateId);
		$mCate = D('Cate');
		$mCate->startTrans();
		$res = $mCate->delCate($id); 
		if($res <= 0){
			$mCate->rollback();
			$this->ajaxReturn(array_err(0,'删除商品分类失败'));
		}else{
			S('catesTree',NULL);
			$mCate->commit();
			$this->ajaxReturn(array_err(0,'删除商品分类成功'));
		}
	}

	protected function _checkCate(&$data,$lock = false){
		$cateName = I('post.cateName');
		$cateId = I('post.cateId');
		if($cateId != 0){
			$mCate = M('Cate');
			$where['id'] = $cateId;
			if($lock){
				$cateRes = $mCate->where($where)->find();
			}else{
				$cateRes = $mCate->where($where)->lock(true)->find();
			}
		}
		$data['catename'] = $cateName;
		$data['pid'] = $cateId;
		if(empty($cateName)) return array_err(999,'分类名称不能为空');
		if($cateId == 0) return array_err(0,'success');
		if(empty($cateRes)){
			return array_err(1000,'不存在您要的商品分类标识');
		}
		return array_err(0,'success');
	}

	protected function _checkCateId($cateId,$lock = false){
		if(empty($cateId)) return array_err(997,'商品分类标识不能为空');
		$mCate = M('Cate');
		$where['id'] = $cateId;
		if($lock){
			$cateRes = $mCate->where($where)->find();
		}else{
			$cateRes = $mCate->where($where)->lock(true)->find();
		}
		if(empty($cateRes)){
			return array_err(998,'不存在您要的商品分类标识');
		}
		return $cateRes;
	}

}