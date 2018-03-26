<?php
namespace Admin\Model;
use Think\Model;
class AdminModel extends Model{

	/**
	 * 验证用户登录信息
	 */
	public function checkLogin($userName,$passWord){
		$where['username'] = $userName;
		$adminRes = $this->where($where)->find();
		if($adminRes){
			if($adminRes['password'] == md5($passWord)){
				$auth = array(
					'id'=>$adminRes['id'],
					'username'=>$adminRes['username'],
					'ip'=>$adminRes['ip'],
					);
				session('AdminInfo',$auth);
				return true;
			}
		}
		return false;

	}

	/**
	 * 增加管理员
	 * @param [type] $data [description]
	 */
	public function setAdmin($data){
		return $this->add($data);
	}

	/**
	 * 删除管理员
	 * @param  [type] $adminId [description]
	 * @return [type]          [description]
	 */
	public function delAdmin($adminId){
		return $this->delete($adminId);
	}

	/**
	 * 获取所有管理员
	 * @param  string $param [description]
	 * @return [type]        [description]
	 */
	public function getAdmins($param = ''){
		return $this->where($this->_makeParam($param))->page($param['page'],$param['limit'])->select();
	}

	public function getAdminCount($param){
		return $this->where($this->_makeParam($param))->count();
	}	

	/**
	 * 修改数据
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function editAdmin($data){
		return $this->save($data);
	}

	public function _makeParam($param,$prefix = ''){
		$code = array();
		if(!empty($param['username'])){
			$code[$prefix.'username'] = $param['username'];
		}
		if(!empty($param['adminId'])){
			$code[$prefix.'id'] = $param['adminId'];
		}
		if(!empty($param['id'])){
			$code[$prefix.'id'] = array('NEQ',$param['id']);
		}
		return $code;
	}
}