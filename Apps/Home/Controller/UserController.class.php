<?php
namespace Home\Controller;
use Home\Controller\BaseController;

class UserController extends BaseController {
	/**
	 * 登录页面
	 * @return [type] [description]
	 */
	public function login(){
		$this->display();
	}

	/**
	 * 设置验证码
	 * @return [type] [description]
	 */
	public function verify(){
		verify();
	}

	/**
	 * 登录
	 * @return [type] [description]
	 */
	public function ajaxLogin(){
		$this->_inputAjax();
		$userName = trim(I('post.username'));
		$passWord = trim(I('post.password'));
		$verifyCode = trim(I('post.verify_code'));
        D('Home/Ads')->getAds();
		A('Home/User','Event')->_checkLogin();
	}
}