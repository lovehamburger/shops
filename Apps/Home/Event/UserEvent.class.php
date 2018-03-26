<?php
/**
 * 用户管理事件
 * @author chenlh 2018.3.26
 * @copyright 晨丰科技有限公司
 * @abstract 用户管理事件
 */

namespace Home\Event;
use Home\Event\BaseEvent;

class UserEvent extends BaseEvent {

    /**
     * 校验员工昵称
     */
    public function _checkLogin($nickName){
        //判断不能为空
        if(empty($nickName)){
            return array_err(3001, '员工昵称不能为空');
        }

        //长度判断
        $minLen = C('STAFF.NICKNAME_MIN_LENGTH');
        $maxLen = C('STAFF.NICKNAME_MAX_LENGTH');
        if(!checkLength($nickName, $minLen, $maxLen)){
            return array_err(3002, '员工昵称的长度应在'.$minLen.'到'.$maxLen.'位之间');
        }

        //敏感词汇校验
        $sWords = checkWords($nickName);
        if(!empty($sWords)){
            return array_err(3003, '员工昵称存在敏感词汇：'.$sWords);
        }

        return $this->__SUCCESS;
    }
}