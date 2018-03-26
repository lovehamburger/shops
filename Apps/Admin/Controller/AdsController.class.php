<?php
namespace Admin\Controller;
use Admin\Controller\BaseController;

class AdsController extends BaseController {
	/**
	 * 广告位的设置
	 * @return [type] [description]
	 */
	public function index(){
		$this->display('Ads/posIndex');
	}
	/**
	 * 广告位的列表
	 * @return [type] [description]
	 */
	public function ajaxPosList(){
		$this->_inputAjax();
		$param = I('post.param');
		$res = array_err(0,'success');
		$res['count'] = D('Ads')->getPosCount($param);
		$res['data'] = array();
		if($res['count'] > 0){
			$res['data'] = D('Ads')->getPos($param);
		}
		$this->ajaxReturn($res);
	}
	
	public function pushAdsPos(){
		$adsPosId = I('get.adsPosId/d');
		if (!empty($adsPosId)) {
			$posRes = array();
			$checkPosId = $this->_checkPosId($adsPosId,false,$posRes);
			if($checkPosId['err_code'] > 0) return $this->error($checkPosId['err_msg']);
			$this->assign('posRes',$posRes);
		}
		$this->display('ads/posAdd');
	}

	/**
	 * 添加广告位
	 */
	public function setPos(){
		$this->_inputAjax();
		$data = [];
		$checkInfo = $this->_checkPos($data);
		if($checkInfo['err_code'] > 0 ) return  $this->ajaxReturn($checkInfo);
		$posId= D('Ads')->setPos($data);
		if($posId > 0){
			$this->ajaxReturn(array_err(0,'添加广告位成功'));
		}
		$this->ajaxReturn(array_err(778,'添加广告位失败'));
	}

	/**
	 * 广告列表的显示
	 * @return [type] [description]
	 */
	public function adsList(){
		$this->display('ads/adsList');
	}

    /**
     * 广告列表
     */
    public function adsAjaxList(){
		$this->_inputAjax();
		$param = I('post.param');
		$mAds = D('Ads');
		$count = $mAds->getAdsCount($param);
		$adsRes = array_err(0,'success');
		$adsRes['count'] = $count;
		if($count){
			$adsRes['data'] = $mAds->getAds($param);
		}
		$this->ajaxReturn($adsRes);
    }

	/**
	 * 广告的添加
	 */
    public function addAds(){
		$this->_inputAjax();
		$data = array();
		$this->_checkAds($data);
		$adsId = D('Ads')->addAds($data);
		if($adsId > 0){
			$this->ajaxReturn(array_err(0,'添加广告成功'));
		}
		$this->ajaxReturn(array_err(889,'添加广告失败'));
	}

	/**
	 * 修改广告
	 */
	public function editAds(){
		$this->_inputAjax();
		$data = [];
		$adsId = I('post.adsId/d');
		$checkAdsId = $this->_checkAdsId($adsId,true);
		if($checkAdsId['err_code'] > 0 ) return  $this->ajaxReturn($checkAdsId);
		$checkInfo = $this->_checkAds($data);
		if($checkInfo['err_code'] > 0 ) return  $this->ajaxReturn($checkInfo);
		M()->startTrans();
		$flag = D('Ads')->editAds($data,$adsId);
		if($flag !== false){
			M()->commit();
			$this->ajaxReturn(array_err(0,'修改广告成功'));
		}
		M()->rollback();
		$this->ajaxReturn(array_err(778,'修改广告失败'));
	}

	/**
	 * 验证广告
	 * @param $adsId
	 * @param bool $lock
	 * @param string $adsRes
	 */
	protected function _checkAdsId($adsId,$lock = false,&$adsRes = ''){
		if(empty($adsId)) return array_err(777,'广告标识不能为空');
		$adsRes = D('Ads')->getAdsById($adsId,$lock);
		if(empty($adsRes)) return array_err(98,'不存在你要的广告数据，请核实');
		return array_err(0,success);
	}

	/**
	 * 验证广告数据是否正确
	 */
	protected function _checkAds(&$data){
		$adname = I('post.adname');
		if(empty($adname)) return array_err(555,'广告名称不能位空');
		$type = I('post.type/d');
		if($type != 1 && $type != 2){
			return array_err(554,'广告类型错误');
		}
		$picure = I('post.picure');
		if(empty($picure)) return array_err(553,'广告图片不能位空');
		$link = I('post.link');
		if(empty($link)) return array_err(552,'广告图片链接不能位空');
		$ison = I('post.ison/d');
		if($ison != 0 && $ison != 1) return array_err(551,'广告上架标识错误');
		$posid = I('post.posid/d');
		if(empty($posid)) return array_err(550,'广告位置设置错误或不能位空');
		$checkPosId = $this->_checkPosId($posid);
		if($checkPosId['err_code'] > 0) return array_err($checkPosId);
		return array_err(0,'success');
	}

	/**
	 * 修改广告位
	 */
	public function editPos(){
		$this->_inputAjax();
		$data = [];
		$postId = I('post.posId');
		$checkPosId = $this->_checkPosId($postId,true);
		if($checkPosId['err_code'] > 0 ) return  $this->ajaxReturn($checkPosId);
		$checkInfo = $this->_checkPos($data,$postId);
		if($checkInfo['err_code'] > 0 ) return  $this->ajaxReturn($checkInfo);
		M()->startTrans();
		$flag = D('Ads')->editPos($data);
		if($flag !== false){
			M()->commit();
			$this->ajaxReturn(array_err(0,'修改广告位成功'));
		}
		M()->rollback();
		$this->ajaxReturn(array_err(778,'修改广告位失败'));
	}

	protected function _checkPosId($postId,$lock = false,&$posRes = ''){
		if(empty($postId)) return array_err(99,'广告位标识不能为空');
		$posRes = D('Ads')->getPosById($postId,$lock);
		if(empty($posRes)) return array_err(98,'不存在你要的广告位数据，请核实');
		return array_err(0,'success');
	}

	protected function _checkPos(&$data,$postId = ''){
		$pname = I('post.pname');
		if(empty($pname)) return array_err(888,'广告位置名称不能为空');
		$width = I('post.width');
		if(empty($width)) return array_err(887,'广告位置宽度不能为空');
		if(!is_numeric($width) || $width <= 0) return array_err(887,'广告位置宽度必须是正整数');
		$height = I('post.height');
		if(empty($height)) return array_err(886,'广告位置高度不能为空');
		if(!is_numeric($height) || $height <= 0) return array_err(886,'广告位置高度必须是正整数');
		$param['pname'] = $pname;
		if($postId){
			$param['id'] = array('NEQ',$postId);
			$data['id'] = $postId;
		}
		$count = D('Ads')->getPosCount($param);
		if($count > 0) return array_err(554,'广告位置名称已经存在,请更换哦');
		$data['pname'] = $pname;
		$data['width'] = $width;
		$data['height'] = $height;
		return  array_err(0,'success');
	}
}