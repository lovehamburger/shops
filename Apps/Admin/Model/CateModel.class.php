<?php
namespace Admin\Model;
use Think\Model;
class CateModel extends Model{
	/**
	 * 获取所有的商品分类
	 * @return [type] [description]
	 */
	public function catesTree(){
		$catesTree = S('catesTree');
		if(empty($catesTree)){
			$cateRes = $this->select();
			return $this->cateSort($cateRes);
		}
		return $catesTree;
	}

	protected function cateSort($data,$id=0,$level=0){
		static $catesTree = array();
		foreach ($data as $key => $value) {
			if($value['pid'] == $id){
				$value['level'] = $level;
				$value['html'] = str_repeat('|——',$level);//显示出层级
				$catesTree[] = $value;
				$this->cateSort($data,$value['id'],$level+1);
			}
		}
		S('catesTree',$catesTree);
		return  $catesTree;
	}

	public function setCate($data){
		return $this->add($data);
	}

	//删除商品分类
	public function delCate($cateId){
		$id = $this->getChild($this->select(),$cateId);
		$id[] = $cateId;
		return $this->delete(implode(',',$id));
	}

	public function getChild($data,$cateId){
		static $arr = [];

		foreach ($data as $key => $value) {
			if($value['pid'] == $cateId){
				$arr[] = $value['id'];
				$this->getChild($data,$value['id']);
			}
		}
		return $arr;
	}
}