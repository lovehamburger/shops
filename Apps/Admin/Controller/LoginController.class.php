<?php
namespace Admin\Controller;
use Think\Controller;
class LoginController extends Controller {
	/**
	 * 登录页面
	 * @return [type] [description]
	 */
	public function index(){ 
		$this->display('./login');
	}
	/**
	 * 登录
	 * @return [type] [description]
	 */
	public function login(){
		if(IS_AJAX){
			$checkInfo = $this->_checkLogin();
			$this->ajaxReturn($checkInfo);
		}else{
			$this->error('非法登录');
		}
	}
	/**
	 * 设置验证码
	 */
	public function setVerify(){
		verify();
	}

	protected function _checkLogin(){
		$code = I('post.verify','','htmlspecialchars,trim');
		$userName = I('post.userName','','htmlspecialchars,trim');
		$passWord = I('post.passWord','','htmlspecialchars,trim');
		if(empty($userName)){
			return array_err(77771,'用户名不能为空');
		}
		if(empty($passWord)){
			return array_err(77772,'密码不能为空');
		}
		if(empty($code)) return array_err(77773,'验证码不能为空');
		if(checkVerify($code) === false) return array_err(77774,'验证码错误');
		//验证用户名和密码是否正确
		$flag = D('admin')->checkLogin($userName,$passWord);
		if($flag === true){
			return array_err(0,'登录成功');
		}else{
			return array_err(77775,'用户名或密码错误');			
		}
	}


}