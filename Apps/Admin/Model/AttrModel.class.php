<?php
namespace Admin\Model;
use Admin\Model\BaseModel;
class AttrModel extends BaseModel{
	/**
	 * 查找所有的属性列表分页
	 * @param [type] $data [description]
	 */
	public function getAttrs($param,&$count,&$attrData){
		$where = array();
		$where['type_id'] = $param['typeId'];
		$count = $this->where($where)->count();
		if($count){
			if(empty($param['page']) && empty($param['limit'])){
				$attrData = $this->where($where)->select();
			}else{
				$attrData = $this->where($where)->page($param['page'],$param['limit'])->select();
			}
		}
	}

	public function getAttr($param,$lock = false,$field = '*'){
		if($lock){
			return $this->field($field)->where($param)->lock(true)->find();
		}else{
			return $this->field($field)->where($param)->find();
		}
	}
	/**
	 * 属性添加
	 * @param [type] $data [description]
	 */
	public function addAttr($data){
		return $this->add($data);
	}

	/**
	 * 属性修改
	 * @param  [type] $data   [description]
	 * @param  [type] $attrId [description]
	 * @return [type]         [description]
	 */
	public function editAttr($data,$attrId){
		$where['id'] = $attrId;
		return $this->where($where)->save($data);
	}

	/**
	 * 属性删除
	 */
	public function delAttr($attrId){
		$where = array();
		$where['id'] = $this->getIDParamExt($attrId);
		return $this->where($where)->delete();
	}

	public function getAttrById($attrIdArr){
		$where = array();
		$where['id'] = $this->getIDParamExt($$attrIdArr);
		return $this->where($where)->select();
	}
}