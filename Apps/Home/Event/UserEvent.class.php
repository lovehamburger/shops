<?php
/**
 * 用户管理事件
 * @author chenlh 2018.3.26
 * @abstract 用户管理事件
 */

namespace Home\Event;
use Home\Event\BaseEvent;

class UserEvent extends BaseEvent {

    /**
     * 校验员工昵称
     */
    public function _checkLogin($userName,$passWord,$verifyCode){
        if (empty($userName)) {
            return array_err('45454','用户名不能为空!');
        }
        if (!checkTel($userName) && !checkEmail($userName)) {
            return array_err('77849','账号格式不正确');
        }
        if (empty($passWord)) {
            return array_err('45455','登陆密码不能为空!');
        }
        if (mb_strlen($passWord) < 6){
            return array_err('45453','登陆密码不能小于6位!');
        }
        if (empty($verifyCode)) {
            return array_err('45454','验证码不能为空!');
        }
        //校验验证码是否正确
        if(!checkVerify($verifyCode)) return array_err('45424','验证码输入错误!');
        //开始验证是否正确
    }

    public function _vCode($userName,$verifyCode,$type){
        if($type !== 1 && $type !== 2) return array_err('45452','注册类型错误!');

        if(empty($userName)) return array_err('45454','用户名不能为空!');

        if (!checkTel($userName) && !checkEmail($userName)) return array_err('77849','账号格式不正确');

        if(!checkVerify($verifyCode)) return array_err('45424','验证码输入错误!');

        if($type == 1 && S('tp_config')['sms_regis_sms_enable'] == 1){
            //验证手机号码是否已经被注册使用
            $field = 'user_id';
            $checkMobile = $this->_checkMobile($userName,$field);
            if($checkMobile == true){
                return array_err(9876,'该用户已注册,请确认');
            }
            //这边开始设置手机短信验证码
           $a=  makeVerifyCode('REGISTER_VERITY_CODE',$userName,SMS_9721536);
            echo'<pre>'; 
                print_r($a);
            echo'</pre>';
            die();
        }

        if($type == 2 && S('tp_config')['sms_regis_sms_enable'] == 1){
            //验证邮箱号码是否已经被注册使用
            $field = 'user_id';
            $checkEmail = $this->_checkEmail($userName,$field);
            if($checkEmail == true){
                return array_err(9876,'该用户已注册,请确认');
            }
            //这边验证邮箱验证码是否正确
        }
    }

    /**
     * @param $mobile
     * @param $field
     * @return bool
     * 检查手机号码是否已经被注册
     */
    public function _checkMobile($mobile,$field){
        $res = D('Home/User')->getUserInfoByMobile($mobile,$field);
        if($res){
            return true;
        }
        return false;
    }

    /**
     * @param $mobile
     * @param $field
     * @return bool
     * 检查手机号码是否已经被注册
     */
    public function _checkEmail($email,$field){
        $res = D('Home/User')->getUserInfoByEmail($email,$field);
        if($res){
            return true;
        }
        return false;
    }
}
