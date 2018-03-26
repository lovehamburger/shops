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
        echo "444411";
    }
}
