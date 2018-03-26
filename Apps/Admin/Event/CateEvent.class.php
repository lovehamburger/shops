<?php

/**
 * 资金管理事件
 * luofc 2017.2.20
 * @abstract 全部查询的数据都要从主库走
 */
namespace Admin\Event;
use Admin\Event\BaseEvent;

class CateEvent extends BaseEvent {
    
 	public function _checkCateId($cateId,$lock = false){
		if(empty($cateId)) return array_err(997,'商品分类标识不能为空');
		$mCate = M('Cate');
		$where['id'] = $cateId;
		if($lock){
			$cateRes = $mCate->where($where)->find();
		}else{
			$cateRes = $mCate->where($where)->lock(true)->find();
		}
		if(empty($cateRes)){
			return array_err(998,'不存在您要的商品分类标识');
		}
		return $cateRes;
	}
}