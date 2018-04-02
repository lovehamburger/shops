<?php
namespace Home\Controller;
use Home\Controller\BaseController;

class UserController extends BaseController {
	/**
	 * ��¼ҳ��
	 * @return [type] [description]
	 */
	public function login(){
		$this->display('user/login');
	}

	/**
	 * ������֤��
	 * @return [type] [description]
	 */
	public function verify(){
		verify();
	}

	/**
	 * ��¼
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
     * ע��ҳ��
     */
	public function reg(){
        $this->display('user/reg'); 
	}

    /**
     * ע��������֤
     */
	public function _checkReg(){

	}

    /**
     * ��֤���뼰��֤��
     */
	public function vCode(){
	    $type = I('post.type/d',1);
	    $userName = I('post.username','','trim');
	    $verifyCode = I('post.verify_code','','trim');
	    $vCode = A('Home/User','Event')->_vCode($userName,$verifyCode,$type);
	    if($vCode['err_code'] > 0){
            $this->ajaxReturn($vCode);
        }
        $this->ajaxReturn(array_err(0,'���ŷ��ͳɹ�'));
	}
}