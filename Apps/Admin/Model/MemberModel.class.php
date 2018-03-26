<?php
namespace Admin\Model;
use Admin\Model\BaseModel;
class MemberModel extends BaseModel{
	/**
	 * 查找会员等级数据
	 * @param [type] $data [description]
	 */
	public function getMemberLevel($param,$lock = false,$field = '*'){
		if($lock){
			return M('member_level')->where($param)->lock(true)->field($field)->find();
		}else{
			return M('member_level')->where($param)->field($field)->find();
		}
	}

	public function getMemberLevelByName($levelName,$leverId = ''){
		$param = [];
		$param['level_name'] = $levelName;     
		if($leverId) $param['id'] = array('NEQ',$leverId);
		return M('member_level')->where($param)->field($field)->find();
	}
	/**
	 * 添加会员等级
	 * @param [type] $data [description]
	 */
	public function setMemberLevel($data){
		return M('member_level')->add($data);
	}
	/**
	 * 修改会员等级
	 * @param [type] $data [description]
	 */
	public function editMemberLevel($data){
		return M('member_level')->save($data);
	}
	/**
	 * 查找所有的会员等级
	 * @return [type] [description]
	 */
	public function getMemberLevels($param = '',$field = '*'){
		return M('member_level')->field($field)->where($param)->select();
	}



	
	// public function _makeParam($param,$prefix = ''){
	// 	$code = array();
	// 	if(!empty($param['brandName'])){
	// 		$code[$prefix.'brand_name'] = $param['brandName'];
	// 	}
	// 	if(!empty($param['brandId'])){
	// 		$code[$prefix.'id'] = $this->getIDParamExt($param['brandId']);
	// 	}
	// 	return $code;
	// }
}