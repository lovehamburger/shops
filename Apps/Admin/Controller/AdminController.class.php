<?php
namespace Admin\Controller;
use Admin\Controller\BaseController;
class AdminController extends BaseController {
	/**
	 * 显示系统管理员列表页面
	 * @param  string $value [description]
	 * @return [type]        [description]
	 */
	public function list(){
		$this->display();
	}

	//退出
	public function loginOut(){
		session(null);
		$this->success('您已经安全退出!');
	} 
	/**
	 * 异步获取管理员用户
	 * @return [type] [description]
	 */
	public function listShow(){
		//@todo权限
		$this->_inputAjax();
		$mAdmin = D('Admin');
		$param['page'] = I('post.currPage',1);
		$param['limit'] = I('post.pageCount',10);
		$adminCount = $mAdmin->getAdminCount($param);
		if($adminCount > 0){
			$adminRes = array_err(0,'success');
			$adminRes['data'] = $mAdmin->getAdmins($param);
			$adminRes['count'] = $adminCount;
		}else{
			$adminRes = array_err(0,'暂无用户!');
			$adminRes['data'] = [];
		}
		$this->ajaxReturn($adminRes);
	}

	public function publish(){
		$adminId = I('get.adminId');
		if(!empty($adminId)){
			$adminRes = $this->_checkAdminId($adminId);
			if($adminRes['err_code'] > 0){
				$this->error($adminRes['err_msg']);
				return ;
			}
			$publishInfo = '修改用户';
			$this->assign('publishInfo',$publishInfo);
			$this->assign('adminRes',$adminRes);
		}
		$this->display('Admin/add');
	}
	
	public function add(){
		$this->_inputAjax();
		$data = [];
		$info = $this->_checkAdmin($data);
		$mAdmin = D('Admin');
		$returnInfo = $mAdmin->setAdmin($data);
		$returnInfo > 0 ? $this->ajaxReturn(array_err(0,'添加用户成功!')) : $this->ajaxReturn(array_err(996,'添加用户失败!'));
	}

	public function edit(){
		$this->_inputAjax();
		$adminId  = I('post.adminId');
		//验证adminId
		$checkAdminId = $this->_checkAdminId($adminId,true);
		if($checkAdminId['err_code'] > 0) $this->ajaxReturn($checkAdminId);
		$data = [];
		$checkAdmin = $this->_checkAdmin($data,$adminId);
		if($checkAdmin['err_code'] > 0) $this->ajaxReturn($checkAdmin);
		$mAdmin = D('Admin');
		$mAdmin->startTrans();
		$data['id'] = $adminId;
		$returnInfo = $mAdmin->editAdmin($data);
		if($returnInfo === false){
			$mAdmin->rollback();
			$this->ajaxReturn(array_err(456,'修改失败'));
		}else{
			$mAdmin->commit();
			$this->ajaxReturn(array_err(0,'修改成功'));	
		}
	}

	public function del(){
		$this->_inputAjax();
		$adminId = I('post.adminId');
		$checkInfo = $this->_checkAdminId($adminId,true);

		if($checkInfo['err_code'] > 0) $this->ajaxReturn($checkInfo);
		$mAdmin = D('Admin');
		$mAdmin->startTrans();
		$flag = $mAdmin->delAdmin($adminId);
		if($flag === false){
			$mAdmin->rollback();
			$this->ajaxReturn(array_err(456,'删除失败'));
		}else{
			$mAdmin->commit();
			$this->ajaxReturn(array_err(0,'删除成功'));	
		}
	}
	/**
	 * 验证adminId
	 * @return [type] [description]
	 */
	protected function _checkAdminId($adminId,$lock = false){
		if(empty($adminId)) return array_err(888,'管理员标识不能为空!');
		$mspAdmin = M('admin');
		$where['id'] = $adminId;
		if($lock){
			$adminInfo = $mspAdmin->lock(true)->where($where)->find();
		}else{
			$adminInfo = $mspAdmin->where($where)->find();
		}
		if(empty($adminInfo)){
			return array_err(999,'管理员数据不存在!');
		}
		return $adminInfo;

	}

	protected function _checkAdmin(&$data,$adminId){
		$userName = I('post.userName','','htmlspecialchars,trim');
		$passWord = I('post.passWord','','htmlspecialchars,trim');
		if(empty($userName)) return array_err(999,'用户名不能为空!');
		if(!$adminId){
			if(empty($passWord)) return array_err(998,'密码不能为空!');
		}
		//判断用户是否已经注册
		$mAdmin = D('Admin');
		$param['username'] = $userName;
		$param['id'] = $adminId;
		$res = $mAdmin->getAdminCount($param);
		if($res > 0) return array_err(997,'该管理员用户已经注册了，请更换新账户哦!');
		$data['username'] = $userName;
		if(!empty($passWord)){
			$data['password'] = md5($passWord);
		}
		return array_err(0,'success');
	}
}
