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
}
