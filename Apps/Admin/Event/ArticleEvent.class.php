<?php

/**
 * 资金管理事件
 * chenlh 2017.2.20
 * @abstract 全部查询的数据都要从主库走
 */
namespace Admin\Event;
use Admin\Event\BaseEvent;

class ArticleEvent extends BaseEvent {
	
	public function _checkArticleId($articleId,$lock = false){
		if(empty($articleId)) return array_err(999,'文章标识不能为空');
		$param['id'] = $articleId;
		$res = D('Article')->getArticle($param,$lock);
		if(empty($res)) return array_err(998,'文章标识不存在,请核实!');
		return $res;
	}
}