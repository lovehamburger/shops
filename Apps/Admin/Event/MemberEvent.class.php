<?php

/**
 * 资金管理事件
 * luofc 2017.2.20
 * @abstract 全部查询的数据都要从主库走
 */
namespace Admin\Event;
use Admin\Event\BaseEvent;

class MemberEvent extends BaseEvent {
	
	public function _checkLevelId($levelId,$lock = 'false'){
		if(empty($levelId)) return array_err(999,'会员等级标识不能为空');
		$mMember = D('Member');
		$param['id'] = $levelId;
		$res = $mMember->getMemberLevel($param,$lock);
		if(empty($res)){
			return array_err(404,'您查看的会员等级不存在，请核实');
		}
		return $res;
	}
}