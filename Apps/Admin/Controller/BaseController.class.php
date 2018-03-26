<?php
namespace Admin\Controller;
use Think\Controller;
class BaseController extends Controller {
	protected $adminUid;

	public function __construct(){
		parent::__construct();
	}

	public function _inputAjax(){
		if(!IS_AJAX) $this->ajaxReturn(array_err(80000, '非法请求'));
	}

	public function _initialize($value=''){
		if(empty(session('AdminInfo'))){
			if(IS_AJAX) $this->ajaxReturn(array_err(888888,'您未登录或者信息已过期，请重新登录'));
			echo "<script>window.location.href='admin-login';</script>";
			exit();
		};
	}
}