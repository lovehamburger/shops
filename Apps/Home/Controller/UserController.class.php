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
        D('Home/User')->login($userName,$passWord);
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
	public function checkReg(){
		$this->_inputAjax();
		$type = I('post.type/d');
        $memberName = I('post.membername','','trim');
	    $userName = I('post.username','','trim');
	    $verifyCode = I('post.verify_code','','trim');
	    $checkEP = A('Home/User','Event')->_checkEP($userName,$verifyCode,$memberName,$type);//调用已经存在的验证码和手机/邮箱的验证
	    if($checkEP['err_code'] > 0){
            $this->ajaxReturn($checkEP);
        }
	    $code = I('post.code','','trim');
	    $passWord = I('post.password','','trim');
	    $passWord2 = I('post.password2','','trim');
	    $data = array('passWord' => $passWord,'passWord2' => $passWord2,'code' => $code,'userName' => $userName);
	    $checkReg = A('Home/User','Event')->_checkReg($data);
	    if($checkReg['err_code'] > 0){
            $this->ajaxReturn($checkReg);
        }
        $dataRes['mobile_validated'] = 0;
        $dataRes['email_validated'] = 0;
	    //验证短信验证码是否正确
        if($type == 1){
        	if(S('setting')['regis_sms_enable'] == 1){
        		if(empty($code)) return array_err(22223,'短信验证码不能为空');
        		$checkVerifyCode = _checkVerifyCode('REGISTER_VERITY_CODE',$data['userName'],$data[
                'code'],2);
            	if($checkVerifyCode['err_code'] > 0) $this->ajaxReturn($checkVerifyCode);
            	$dataRes['mobile_validated'] = 1;
        	}
            $dataRes['mobile'] = $userName;
        }
        //验证短信验证码是否正确
        if($type == 2){
        	if(S('setting')['regis_smtp_enable'] == 1){
        		if(empty($code)) return array_err(22223,'邮箱验证码不能为空');
        		$checkVerifyCode = _checkVerifyCode('REGISTER_VERITY_CODE',$data['userName'],$data[
                'code'],2);
            	if($checkVerifyCode['err_code'] > 0) $this->ajaxReturn($checkVerifyCode);
            	$dataRes['email_validated'] = 1;
        	}
            $dataRes['email'] = $userName;
        }
        //生成随机混淆码
        $randPswSalter = _randPswSalter();
        $dataRes['password'] = md5(md5($passWord).$randPswSalter);
        $dataRes['is_distribut'] = 0;
        $dataRes['is_lock'] = 0;
        $dataRes['total_amount'] = 0;
        $dataRes['discount'] = 1;
        $dataRes['level'] = 1;
        $dataRes['psw_salt'] = $randPswSalter;
        $dataRes['reg_time'] = time();
        $dataRes['pay_points'] = 0;
        $dataRes['underling_number'] = 0;//用户下线总数???
        $dataRes['distribut_money'] = 0;
        $dataRes['frozen_money'] = 0;
        $dataRes['user_money'] = 0;
        $dataRes['birthday'] = 0;
        $dataRes['sex'] = 0;
        $dataRes['distribut_level'] = 0;
        $dataRes['first_leader'] = 0;
        $dataRes['second_leader'] = 0;//第二个上级
        $dataRes['third_leader'] = 0;//第三个上级
        //$dataRes['push_id'] = 0;推送ID
        //$dataRes['message_mask'] = 0;消息掩码
        //$dataRes['token'] = 0;用于app 授权类似于session_id
        //$dataRes['openid'] = 0;第三方唯一标示
        //$dataRes['oauth'] = 0;第三方来源 wx weibo alipay
        //$dataRes['token'] = 0;用于app 授权类似于session_id
        //$dataRes['nickname'] = 0;第三方返回昵称
        //$dataRes['push_id'] = 0;推送ID
        
        // $userID = D('Home/User')->addUser($dataRes);
        // if($userID > 0 ){
        // 	$this->ajaxReturn(array_err(0,'注册成功'));
        // }
        // $this->ajaxReturn(array_err(1120,'注册失败'));
        $this->ajaxReturn(array_err(0,'注册成功'));
	}

    /**
     * 验证号码及验证码
     */
	public function vCode(){
		$this->_inputAjax();
	    $type = I('post.type/d',1);
        $memberName = I('post.membername','','trim');
	    $userName = I('post.username','','trim');
	    $verifyCode = I('post.verify_code','','trim');
	    $checkEP = A('Home/User','Event')->_checkEP($userName,$verifyCode,$memberName,$type);
	    if($checkEP['err_code'] > 0){
            $this->ajaxReturn($checkEP);
        }

	    if($type == 1){
	    	if(S('setting')['regis_sms_enable'] !=  1) $this->ajaxReturn(array_err(1120,'手机验证暂未开启,请核实'));
	    	$makeVerify = makeVerifyCode('REGISTER_VERITY_CODE',$userName,SMS_130210261); 
	    	if($makeVerify['err_code'] > 0){
	    		$this->ajaxReturn($makeVerify);
	    	}
	    }

	    if($type == 2){
	    	if(S('setting')['regis_sms_enable'] != 1) $this->ajaxReturn(array_err(1120,'邮箱验证暂未开启,请核实'));
	    	$makeVerify = makeVerifyCode('REGISTER_VERITY_CODE',$userName,SMS_130210261);
	    	if($makeVerify['err_code'] > 0){
	    		$this->ajaxReturn($makeVerify);
	    	}
	    }

	    
        $this->ajaxReturn(array_err(0,'发送成功'));
	}
}