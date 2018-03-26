<?php
namespace Admin\Model;
use Admin\Model\BaseModel;
class BrandModel extends BaseModel{
	/**
	 * 查找品牌单条数据
	 * @param [type] $data [description]
	 */
	public function getBrand($param,$lock = false,$field = '*'){
		if($lock){
			return $this->where($param)->lock(true)->field($field)->find();
		}else{
			return $this->where($param)->field($field)->find();
		}
	}

	/**
	 * 获取所有的品牌列表
	 * @return [type] [description]
	 */
	public function getBrands($param){
		return $this->where($this->_makeParam($param))
		->page($param['page'],$param['limit'])->select();
	}

	/**
	 * 获取所有的品牌列表
	 * @return [type] [description]
	 */
	public function getBrandsLock($param = '',$lock = false){
		if($lock){
			return $this->lock(true)->where($this->_makeParam($param))->select();
		}else{
			return $this->where($this->_makeParam($param))->select();
		}
		
	}

	/**
	 * 删除品牌
	 */
	public function delBrand($param){
		return $this->where($this->_makeParam($param))->delete();
	}

	/**
	 * 获取所有的品牌列表
	 * @return [type] [description]
	 */
	public function getBrandsCount($param){
		return $this->where($this->_makeParam($param))
		->count();
	}

	/**
	 * 添加品牌数据
	 * @param [type] $data [description]
	 */
	public function setBrand($data){
		return $this->add($data);
	}

	/**
	 * 修改品牌
	 */
	public function editBrand($data){
		return $brandId = $this->save($data);
	}




	public function _makeParam($param,$prefix = ''){
		$code = array();
		if(!empty($param['brandName'])){
			$code[$prefix.'brand_name'] = $param['brandName'];
		}
		if(!empty($param['brandId'])){
			$code[$prefix.'id'] = $this->getIDParamExt($param['brandId']);
		}
		return $code;
	}
}