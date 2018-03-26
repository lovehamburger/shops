<?php
namespace Admin\Controller;
use Admin\Controller\BaseController;
class BrandController extends BaseController {

	public function index(){
		$this->display('Brand/index');
	}

	public function list(){
		//权限和ajax验证
		$param['page'] = I('post.currPage',1);
		$param['limit'] = I('post.pageCount',10);
		$param['brandName'] = I('post.brandName');
		$count = D('Brand')->getBrandsCount($param);
		if($count > 0){
			$res = array_err(0,'success');
			$res['data'] = D('Brand')->getBrands($param);
			$res['count'] = $count;
			$this->ajaxReturn($res);
		}else{
			$this->ajaxReturn(array_err(0,'不存在品牌分类'));
		}
	}

	public function publish(){
		$brandId = I('get.brandId');
		if(!empty($brandId)){
			//检查
			$checkRes = A('Brand','Event')->_checkBrandId($brandId);
			if($checkRes['err_code'] > 0) $this->error($checkRes['err_msg']);
			$this->assign('brandRes',$checkRes);
		}
		$this->display('Brand/add');
	}
	/**
	 * 品牌添加
	 */
	public function add(){
		$this->_inputAjax();
		$data = [];
		$checkInfo = $this->_checkDate($data);
		if($checkInfo['err_code'] > 0){
			$this->ajaxReturn($checkInfo);
		}
		//添加
		$brandId = D('Brand')->setBrand($data);
		if($brandId > 0){
			$this->ajaxReturn(array_err(0,'添加品牌成功'));
		}
		$this->ajaxReturn(array_err(991,'添加品牌失败'));
	}
	/**
	 * 品牌修改
	 * @return [type] [description]
	 */
	public function edit(){
		$this->_inputAjax();
		$brandId = I('post.brandId');
		$checkRes = A('Brand','Event')->_checkBrandId($brandId,true);
		if($checkRes['err_code'] > 0) $this->ajaxReturn($checkRes);
		$data = [];
		$checkInfo = $this->_checkDate($data,$brandId);
		if($checkInfo['err_code'] > 0){
			$this->ajaxReturn($checkInfo);
		}
		//修改
		$mBrand = D('Brand');
		$mBrand->startTrans();
		$flag = $mBrand->editBrand($data);
		if($flag !== false){
			$mBrand->commit();
			if($checkRes['brand_logo']){
				if(file_exists($checkRes['brand_logo'])){
					unlink($checkRes['brand_logo']);
				}
			}
			$this->ajaxReturn(array_err(0,'修改品牌成功'));
		}
		$mBrand->rollback();
		$this->ajaxReturn(array_err(991,'修改品牌失败'));
	}

	public function del(){
		$this->_inputAjax();
		$brandIdRes = json_decode(htmlspecialchars_decode(I('post.brandId')), true);
		$param['brandId'] = $brandIdRes;
		$mBrand = D('Brand');
		$mBrand->startTrans();
		$res = $mBrand->getBrandsLock($param,true);
		if(count($res) != count($brandIdRes)) $this->ajaxReturn(array_err(776,'存在非法标识,请核实'));
		$flag = $mBrand->delBrand($param);

		if($flag === false){
			$mBrand->rollback();
			$this->ajaxReturn(array_err(555,'删除品牌失败哦'));
		}
		foreach ($res as $key => $value) {
			if($value['brand_logo']){
				if(file_exists($value['brand_logo'])){
					unlink($value['brand_logo']);
				}
			}
		}
		$mBrand->commit();
		$this->ajaxReturn(array_err(0,'删除品牌成功'));

	}



	public function _checkDate(&$data,$brandId){
		$imgUrl = I('post.imgUrl');
		$brandName = I('post.brandName');
		$brandUrl = I('post.brandUrl');
		if(empty($brandName)) return array_err(999,'品牌名称不能为空');
		//判断是否有被使用
		$param['brand_name'] = $brandName;
		if($brandId) {
			$param['id'] = array('NEQ',$brandId);
			$data['id'] = $brandId;
		}
		$field = 'id';
		$brandId = D('Brand')->getBrand($param,$field);
		if(!empty($brandId)) return array_err(997,'该品牌已经被使用了，请更换哦!');
		$data['brand_logo'] = $imgUrl;
		$data['brand_name'] = $brandName;
		$data['brand_url'] = $brandUrl;
		return array_err(0,'success');
	}
	/**
	 * 上传图片
	 * @return [type] [description]
	 */
	public function upload(){
		$info = upload(C('ADMIN_UPLOAD_BRAND'));
		$up_img=$info['brand_logo']['savepath'].$info['brand_logo']['savename'];
		//设置小图片
		$width = C('GOOD_BREAD.WIDTH');
		$height = C('GOOD_BREAD.HEIGHT');
		$open = C('ADMIN_UPLOAD_BRAND.rootPath').$up_img;
		$fileName = pathinfo($up_img ,PATHINFO_FILENAME);
		$saveDir = C('ADMIN_UPLOAD_BRAND.rootPath').$info['brand_logo']['savepath'];
		if(!is_dir($saveDir)) mkdir($saveDir);
		$save = $saveDir.$fileName.'.'.pathinfo($up_img ,PATHINFO_EXTENSION);
		setThumb($width,$height,$open,$save);
		echo"<script>imgid=parent.document.getElementById('imgid');imgid.src='".C('ADMIN_UPLOAD_BRAND.rootPath')."{$up_img}'</script>";//将图片显示到页面
		echo"<script>imgurl=parent.document.getElementById('imgurl');imgurl.value='{$save}'</script>"; 
	}
}