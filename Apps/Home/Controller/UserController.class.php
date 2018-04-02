<?php
namespace Home\Controller;
use Home\Controller\BaseController;

class UserController extends BaseController {
	/**
	 * 登录页面
	 * @return [type] [description]
	 */
	public function login(){
		$this->display('user/login');
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
		$checkLogin = A('Home/User','Event')->_checkLogin($userName,$passWord,$verifyCode);
        if ($checkLogin['err_code'] > 0) {
            $this->ajaxReturn($checkLogin);
        }
	}

    /**
     * 注册页面
     */
	public function reg(){
        $this->display('user/reg'); 
	}

    /**
     * 注册数据验证
     */
	public function _checkReg(){



		

	}

    /**
     * 验证号码及验证码
     */
	public function vCode(){
	    $type = I('post.type/d',1);
	    $userName = I('post.username','','trim');
	    $verifyCode = I('post.verify_code','','trim');
	    $vCode = A('Home/User','Event')->_vCode($userName,$verifyCode,$type);
	    if($vCode['err_code'] > 0){
            $this->ajaxReturn($vCode);
        }
        $this->ajaxReturn(array_err(0,'短信发送成功'));
	}
}