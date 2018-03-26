<?php

/**
 * 资金管理事件
 * chenlh 2017.2.20
 * @abstract 全部查询的数据都要从主库走
 */
namespace Admin\Event;
use Admin\Event\BaseEvent;

class BrandEvent extends BaseEvent {
	
	public function _checkBrandId($brandId,$lock = false){
		if(empty($brandId)) return array_err(999,'品牌标识不能为空');
		$param['id'] = $brandId;
		$res = D('Brand')->getBrand($param,$lock);
		if(empty($res)) return array_err(998,'品牌标识不存在,请核实!');
		return $res;
	}
}