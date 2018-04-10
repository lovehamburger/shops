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

    public function _checkEP($userName,$verifyCode,$memberName,$type){
        if($type !== 1 && $type !== 2) return array_err('45452','注册类型错误!');

        if(empty($memberName)) return array_err('45454','会员名不能为空!');

        if(!checkVerify($verifyCode)) return array_err('45424','验证码输入错误!');

        //验证会员名是否已经被注册
        //验证手机号码是否已经被注册使用
        $field = 'member_id';
        $checkMobile = $this->_checkName($memberName,$field);
        if($checkMobile == true){
            return array_err(9876,'会员名已注册,请确认');
        }

        if($type == 1){
            if(empty($userName)) return array_err('454521','手机号码不能为空!');
            if (!checkTel($userName)) return array_err('77849','手机号码格式不正确');
            //验证手机号码是否已经被注册使用
            $field = 'member_id';
            $checkMobile = $this->_checkMobile($userName,$field);
            if($checkMobile == true){
                return array_err(9876,'该手机已注册,请确认');
            }   
        }

        if($type == 2){
            if(empty($userName)) return array_err('454520','邮箱地址不能为空!');
            if (!checkEmail($userName)) return array_err('77849','邮箱地址格式不正确');
            //验证邮箱号码是否已经被注册使用
            $field = 'member_id';
            $checkEmail = $this->_checkEmail($userName,$field);
            if($checkEmail == true){
                return array_err(9876,'该邮箱已注册,请确认');
            }
        }
    }
    /**
     * 验证注册数据
     * @return [type] [description]
     */
    public function _checkReg($data){
        if(empty($data['passWord'])) return array('1121','密码不能为空');
        if (mb_strlen($data['passWord']) < 6){
            return array_err('45453','登陆密码不能小于6位!');
        }
        if(empty($data['passWord2'])) return array('1122','密码确认不能为空');
        if($data['passWord2'] != $data['passWord']) return array('1123','两次密码输入不一致');
        //之后这边可能要做密码强要求
        
        return array_err(0,'success');
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
     * 检查会员名是否已经被注册
     */
    public function _checkName($name,$field){
        $res = D('Home/User')->getUserInfoByName($name,$field);
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
