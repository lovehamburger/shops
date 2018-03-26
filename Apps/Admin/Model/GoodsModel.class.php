<?php
namespace Admin\Model;
use Admin\Model\BaseModel;
class GoodsModel extends BaseModel{
	/**
	 * 商品添加
	 * @param [type] $data [description]
	 */
	public function setGoods($data){
		$goods = array();
		$data['goods']['addtime'] = time();
		//设置商品编号
		$data['goods']['goods_sn'] = time().rand(1,9999999);     
		$goodsId = $this->add($data['goods']);
		if($goodsId <= 0){
			$this->rollback();
			return array_err(665,'商品数据添加失败1,请稍后重试');
		}
		if(!empty($data['levelPrice'])){
			$memberPrice = array();
			foreach ($data['levelPrice'] as $key => $value) {
				$memberPrice['level_id'] = $value['id'];
				$memberPrice['price'] = $value['level'];
				$memberPrice['goods_id'] = $goodsId;
				if(M('member_price')->add($memberPrice) <= 0){
					M('member_price')->rollback();
					return array_err(665,'商品数据添加失败2,请稍后重试');
					break;
				};
			}
		}
		if(!empty($data['goodsImg'])){ 
			$goodsPic = array();
			foreach ($data['goodsImg'] as $key => $value) {
				$goodsPic['max_thumb'] = $value['max_thumb'];
				$goodsPic['sm_thumb'] = $value['sm_thumb'];
				$goodsPic['original'] = $value['original'];
				$goodsPic['goods_id'] = $goodsId;
				if(M('goods_pic')->add($goodsPic) <= 0){
					M('goods_pic')->rollback();
					return array_err(665,'商品数据添加失败3,请稍后重试');
					break;
				};
			}      
		}
		if(!empty($data['goodAttr'])){
			foreach ($data['goodAttr'] as $k => $v) {
				$goodsAttr['goods_id'] = $goodsId;
				if(is_array($v)){
					foreach ($v as $k1 => $v1) {
						$goodsAttr['attr_id'] = $k;
						$goodsAttr['attr_value'] = $v1['attr_values'];
						$goodsAttr['attr_price'] = $v1['attr_price'];
						$flag = M('goods_attr')->add($goodsAttr);
						if($flag <= 0){
							return array_err(664,'商品数据添加失败4,请稍后重试');
							break;
						}
					}
				}else{
					$goodsAttr['attr_id'] = $k;
					$goodsAttr['attr_value'] = $v;
					$goodsAttr['attr_price'] = 0;
					$flag = M('goods_attr')->add($goodsAttr);
					if($flag <= 0){
						return array_err(664,'商品数据添加失败4,请稍后重试');
						break;
					}
				}
			}
		}
		M()->commit();
		return array_err(0,'添加商品成功!');
	}


	/**
	 * 商品修改
	 * @param [type] $data [description]
	 */
	public function editGoods($data,$goodsId){
		$goods = array();
		$data['goods']['addtime'] = time();
		//修改时间
		$data['goods']['updatetime'] = time();
		$where['id'] = $goodsId;
		$goodFlag = $this->where($where)->save($data['goods']);
		if($goodFlag === false){
			$this->rollback();
			return array_err(664,'商品数据修改失败1,请稍后重试');
		}
		if(!empty($data['levelPrice'])){
			//查看是否存在商品的会员价格
			$memberPriceRes = $this->getMemberPriceByGoodId($goodsId);
			//全部删除在添加
			if($memberPriceRes){
				$flag = M('member_price')->where($where)->delete();
				if($flag === false){
					$this->rollback();
					return array_err(665,'商品数据修改失败9,请稍后重试');
				}
			}
			$memberPrice = array();
			foreach ($data['levelPrice'] as $key => $value) {
				$memberPrice['level_id'] = $value['id'];
				$memberPrice['price'] = $value['level'];
				$memberPrice['goods_id'] = $goodsId;
				if(M('member_price')->add($memberPrice) <= 0){
					M('member_price')->rollback();
					return array_err(665,'商品数据修改失败2,请稍后重试');
					break;
				};
			}
		}
		if(!empty($data['goodsImg'])){ 
			$goodsPic = array();
			foreach ($data['goodsImg'] as $key => $value) {
				$goodsPic['max_thumb'] = $value['max_thumb'];
				$goodsPic['sm_thumb'] = $value['sm_thumb'];
				$goodsPic['original'] = $value['original'];
				$goodsPic['goods_id'] = $goodsId;
				if(M('goods_pic')->add($goodsPic) <= 0){
					M('goods_pic')->rollback();
					return array_err(665,'商品数据修改失败3,请稍后重试');
					break;
				};
			}      
		}
		if(!empty($data['goodAttr'])){
			foreach ($data['goodAttr'] as $k => $v) {
				$goodsAttr['goods_id'] = $goodsId;
				if(is_array($v)){
					foreach ($v as $k1 => $v1) {
						$goodsAttr['attr_id'] = $k;
						$goodsAttr['attr_value'] = $v1['attr_values'];
						$goodsAttr['attr_price'] = $v1['attr_price'];
						$flag = M('goods_attr')->add($goodsAttr);
						if($flag <= 0){
							return array_err(664,'商品数据修改失败4,请稍后重试');
							break;
						}
					}
				}else{
					$goodsAttr['attr_id'] = $k;
					$goodsAttr['attr_value'] = $v;
					$goodsAttr['attr_price'] = 0;
					$flag = M('goods_attr')->add($goodsAttr);
					if($flag <= 0){
						return array_err(664,'商品数据修改失败4,请稍后重试');
							break;
					}
				}
			}
		}
		if(!empty($data['oldGoodAttr'])){
			unset($goodsAttr['attr_id']);
			foreach ($data['oldGoodAttr'] as $k => $v) {
				if(is_array($v)){
					foreach ($v as $k1 => $v1) {
						$where['id'] = $v1['goods_attr'];
						$goodsAttr['attr_value'] = $v1['attr_values'];
						$goodsAttr['attr_price'] = $v1['attr_price'];
						$flag = M('goods_attr')->where($where)->save($goodsAttr);
						if($flag === false){
							return array_err(659,'商品数据修改失败4,请稍后重试');
							break;
						}
					}
				}else{
					$where['id'] = $k;
					$goodsAttr['attr_value'] = $v;
					$goodsAttr['attr_price'] = 0;
					$flag = M('goods_attr')->where($where)->save($goodsAttr);
					if($flag === false){
						return array_err(658,'商品数据修改失败4,请稍后重试');
							break;
					}
				}
			}
		}
		M()->commit();
		return array_err(0,'修改商品成功!');
	}

	public function delGoodsAttr($attrId){
		if(M('goods_attr')->delete($attrId) === false){
			return false;
		}
		$where['_string'] = "find_in_set($attrId,goods_attr)";
		$mProduct = M('product');
		$attrIdRes = $mProduct->where($where)->getField('id',true);
		if(!empty($attrIdRes)){
			$param['id'] = array('in',$attrIdRes);
			$flag = $mProduct->where($param)->delete();
			if($flag === false){
				return false;
			}
		}
		return true;
	}

	public function getGoods($param,$lock){
		if($lock){
			return $this->where($param)->lock(true)->find();
		}else{
			return $this->where($param)->lock(true)->find();
		}
	}

	/**
	 * 获取所有的商品列表
	 */
	public function getGoodsList($param,$field = '*'){
		$join = 'INNER JOIN sp_cate as sc ON sp_goods.cate_id=sc.id ';
		$join .= 'LEFT JOIN sp_brand as sb ON sp_goods.brand_id=sb.id ';
		$order = empty($param['order']) ? 'addtime desc' : $param['order'];
		return $this->field($field)->where($this->_makeParam($param))->page($param['page'],$param['limit'])
		->order($order)->join($join)->select();
	}

	/**
	 * 删除商品
	 * @param  [type] $goodsId [description]
	 * @return [type]          [description]
	 */
	public function delGoods($goodsId){
		$param['goods_id'] = $goodsId;
		//删除商品属性
		$attrRes = M('goods_attr')->lock(true)->where($param)->count();
		if($attrRes){
			$flag = M('goods_attr')->where($param)->delete();
			if($flag === false){
				return array_err(444,'删除商品属性失败,请稍后重试');
			}
		}
		//删除商品会员价格
		$goodsPrice = M('member_price')->lock(true)->where($param)->count();
		if($goodsPrice){
			$flag = M('member_price')->where($param)->delete();
			if($flag === false){
				return array_err(444,'删除商品会员价格失败,请稍后重试');
			}
		}

		//删除商品关联图片
		$goodsPic = M('goods_pic')->lock(true)->where($param)->select();
		if($goodsPic){
			//删除本地图片
			foreach ($goodsPic as $key => $value) {
				@unlink($value['original']);
				@unlink($value['max_thumb']);
				@unlink($value['sm_thumb']);
			}
			//删除数据库
			$flag = M('goods_pic')->where($param)->delete();
			if($flag === false){
				return array_err(444,'删除商品关联图片失败,请稍后重试');
			}
		}
		//删除商品
		unset($param['goods_id']);
		$param['id'] = $goodsId;
		$goods = $this->lock(true)->where($param)->find();
		if($goods){
			//删除本地图片
			@unlink($goods['original']);
			@unlink($goods['max_thumb']);
			@unlink($goods['mid_thumb']);
			@unlink($goods['sm_thumb']);
			//删除数据库
			$flag = M('goods')->where($param)->delete();
			if($flag === false){
				return array_err(444,'删除商品失败,请稍后重试');
			}
		}
		return array_err(0,'删除商品成功');

	}

	/**
	 * 获取所有的商品数
	 */
	public function getGoodsCount($param){
		return $this->where($this->_makeParam($param))->count();
	}

	/**
	 * 查询商品类型
	 * @param  [type] $param [description]
	 * @param  string $field [description]
	 * @return [type]        [description]
	 */
	public function getGoodsType($param,$field = '*'){
		return M('type')->field($field)->where($param)->find();
	}

	/**
	 * 查询所有商品类型分页
	 * @param  [type] $param [description]
	 * @param  string $field [description]
	 * @return [type]        [description]
	 */
	public function getGoodsTypes($param){
		return M('type')->where($this->_makeParamType($param))->page($param['page'],$param['limit'])->select();
	}

	/**
	 * 获取所有的商品类型
	 */
	public function getAllGoodsType(){
		return M('type')->select();
	}
	/**
	 * 查询商品类型数量
	 * @param  [type] $param [description]
	 * @param  string $field [description]
	 * @return [type]        [description]
	 */
	public function getGoodsTypeCount($param){
		return M('type')->where($this->_makeParamType($param))->count();
	}


	/**
	 * 添加商品类型
	 * @param [type] $data [description]
	 */
	public function setGoodsType($data){
		return M('type')->add($data);
	}

	/**
	 * 修改商品类型
	 * @param [type] $data [description]
	 */
	public function editGoodsType($data){
		return M('type')->save($data);
	}

	/**
	 * 删除商品类型
	 * @param [type] $data [description]
	 */
	public function delGoodsType($typeId){
		$where['type_id'] = $typeId;
		$flag = M('attr')->where($where)->delete();
		if($flag === false) return false;
		return M('type')->delete($typeId);
	}

	/**
	 * 删除货品信息
	 */
	public function delProduct($goodsId){
		$where['goods_id'] = $goodsId;
		return M('product')->where($where)->delete();
	}

	/**
	 * 添加货品信息
	 */
	public function addProduct($goodsId,$product){
		if(!empty($product) && is_array($product)){
			foreach ($product as $key => $value) {
				$data['goods_attr'] = implode(',',$value['goods_attr']);
				$data['goods_number'] = $value['goods_number'];
				$data['goods_id'] = $goodsId;
				$productId = M('product')->add($data);
				if($productId <=0 ){
					return false;
				}
			}
			return true;
		}
		
	}
	/**
	 * 获取商品拥有的货品信息
	 * @param  [type] $goodsId [description]
	 * @return [type]          [description]
	 */
	public function getProduct($goodsId){
		$where['goods_id'] = $goodsId;
		$where['attr_type'] = 1;
		$join = 'INNER JOIN sp_attr AS sa ON ga.attr_id=sa.id';
		$field = 'ga.*,sa.attr_name';
		$res = M('goods_attr as ga')->field($field)->join($join)->where($where)->select();    
		$newArr = [];
		foreach ($res as $key => $value) {
			$newArr[$value['attr_name']][] = $value;
		}
		return $newArr;
	}

	/**
	 * 获取商品的货品信息
	 * @param  [type] $goodsId [description]
	 * @return [type]          [description]
	 */
	public function getProducts($goodsId,$field = '*'){
		$where['goods_id'] = $goodsId;
		return M('product')->field($field)->where($where)->select();    
	}
	/**
	 * 获取会员价格
	 * @return [type] [description]
	 */
	public function getMemberPriceByGoodId($goods_id){
		$where['goods_id'] = $goods_id;
		return M('member_price')->where($where)->select();
	}
	/**
	 * 获取商品属性
	 * @param  [type] $goods_id [description]
	 * @return [type]           [description]
	 */
	public function getAttrByGoodId($goods_id){
		$where['goods_id'] = $goods_id;
		return M('goods_attr')->where($where)->select();
	}
	/**
	 * 获取商品图片
	 * @param  [type] $goodsId [description]
	 * @return [type]          [description]
	 */
	public function getGoodsPic($goodsId){
		$where['goods_id'] = $goodsId;
		return M('goods_pic')->where($where)->select();
	}


	protected function _makeParam($param,$prefix = ''){
		$code = array();
		if(!empty($param['brandName'])){
			$code[$prefix.'brand_name'] = $param['brandName'];
		}
		if(!empty($param['brandId'])){
			$code[$prefix.'id'] = $this->getIDParamExt($param['brandId']);
		}
		return $code;
	}
	
	protected function _makeParamType($param,$prefix = ''){
		$code = array();
		if(!empty($param['typeName'])){
			$code[$prefix.'type_name'] = $param['typeName'];
		}
		if(!empty($param['typeId'])){
			$code[$prefix.'id'] = $this->getIDParamExt($param['typeId']);
		}
		return $code;
	}
}