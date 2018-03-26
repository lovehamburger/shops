<?php
namespace Admin\Controller;
use Admin\Controller\BaseController;
class MemberController extends BaseController {

	public function index(){
		$this->display('Member/index');
	}

	public function level(){
		$mMember = D('Member');
		$res = $mMember->getMemberLevels();
		$this->assign('res',$res);   
		$this->display('Member/level');
	}
	/**
	 * 异步显示会员等级列表  预留
	 * @return [type] [description]
	 */
	public function levelList(){
		$this->_inputAjax();
		$mMember = D('Member');
		$res = $mMember->getMemberLevels();
		if(empty($res)){
			$this->ajaxReturn(array_err(0,'暂无会员等级数据'));
		}else{
			$levelRes = array_err(0,'success');
			$levelRes['data'] = $res;
			$this->ajaxReturn($levelRes);
		}
	}

	public function publishlevel(){
		$levelId = I('get.levelId');
		if(!empty($levelId)){
			$levelRes = A('Member','Event')->_checkLevelId($levelId);        
			if($levelRes['err_code'] > 0) $this->error($levelRes['err_msg']);
			$this->assign('levelRes',$levelRes);
		}
		$this->display('Member/addLevel');
	}
	/**
	 * 会员等级添加
	 */
	public function addLevel(){
		$this->_inputAjax();
		$data = [];
		$checkInfo = $this->_checkLevelData($data);      
		if($checkInfo['err_code'] > 0) $this->ajaxReturn($checkInfo);
		$mMember = D('Member');
		$flag = $mMember->setMemberLevel($data);
		if($flag > 0){
			$this->ajaxReturn(array_err(0,'success'));
		}
		$this->ajaxReturn(array_err(999,'会员等级添加失败'));
	}
	/**
	 * 会员等级修改
	 * @return [type] [description]
	 */
	public function editLevel(){
		$this->_inputAjax();
		$levelId = I('post.levelId');
		$checkRes = A('Member','Event')->_checkLevelId($levelId,true);
		if($checkRes['err_code'] > 0) return $this->ajaxReturn($checkRes);
		$data = [];
		$checkInfo = $this->_checkLevelData($data);
		if($checkInfo['err_code'] > 0) $this->ajaxReturn($checkInfo);
		$data['id'] = $levelId;
		$mMember = D('Member');
		$mMember->startTrans(); 
		$flag = $mMember->editMemberLevel($data);
		if($flag !== false){
			$mMember->commit();
			$this->ajaxReturn(array_err(0,'会员等级修改成功'));
		}
		$mMember->rollback();
		$this->ajaxReturn(array_err(999,'会员等级添加失败'));
	}

	protected function _checkLevelData(&$data,$levelId = ''){
		$levelName = I('post.level_name');
		if(empty($levelName)) return array_err(3110,'等级名称不能为空');
		//判断会员等级名称是否被使用
		$mMember = D('Member');
		$param['level_name'] = $levelName;
		$member = $mMember->getMemberLevelByName($levelName,$levelId); 
		$pointsMin = I('post.points_min');
		if(empty($pointsMin)) return array_err(3120,'等级下限不能为空');
		if($pointsMin < 0) return array_err(3121,'等级下限不能小于0');
		$pointsMax = I('post.points_max');
		if(empty($pointsMax)) return array_err(3130,'等级上限不能为空');
		if($pointsMin < 0) return array_err(3131,'等级上限不能小于0');
		$rate = I('post.rate');
		if(empty($rate)) return array_err(3140,'等级折扣不能为空');
		if($rate < 0 && $rate > 100) return array_err(3150,'会员折扣率必须在0到100之间');
		$data['points_min'] = $pointsMin;
		$data['points_max'] = $pointsMax;
		$data['level_name'] = $levelName;
		$data['rate'] = $rate;
		return array_err(0,'success');
	}
}