<?php
namespace Admin\Controller;
use Admin\Controller\BaseController;
class GoodsController extends BaseController {
	public function index(){
		$this->display('Goods/index');
	}

	/**
	 * 商品列表
	 * @return [type] [description]
	 */
	public function ajaxGetGoodList(){
		$this->_inputAjax();
		$mGoods = D('Goods');
		$param['page'] = I('post.currPage',1);
		$param['limit'] = I('post.pageCount',10);
		$count = $mGoods->getGoodsCount($param);
		if($count > 0){
			$field = 'sp_goods.id,sp_goods.goods_name,sp_goods.sm_thumb,sp_goods.shop_price,sp_goods.market_price,
			sp_goods.onsale,sc.catename
			,sb.brand_name';
			$returnRes = array_err(0,'success');
			$returnRes['count'] = $count;
			$returnRes['data'] = $mGoods->getGoodsList($param,$field);
		}else{
			$returnRes = array_err(0,'暂无数据');
			$returnRes['count'] = 0;
			$returnRes['data'] = [];
		}
		$this->ajaxReturn($returnRes);   
	}


	/**
	 * 增加修改商品数据 @todo 修改
	 * @return [type] [description]
	 */
	public function publish(){
		//商品分类
		$goodsId = I('get.goodsId');
		if(!empty($goodsId)){
			$goodRes = array();
			$checkGoodsId = $this->_checkGoodsId($goodsId,false,$goodRes);
			if($checkGoodsId['err_code'] > 0) $this->error($checkGoodsId['err_msg']);
			$this->assign('goodRes',$goodRes);//会员的基本信息
			//获取会员等级价格
			$mGoods = D('Goods');
			$PriceRes = $mGoods->getMemberPriceByGoodId($goodsId);
			$PriceArr = array();
			if(!empty($PriceRes)){
				foreach ($PriceRes as $key => $value) {
					$PriceArr[$value['level_id']] = $value['price'];
				}
			}
			$this->assign('PriceArr',$PriceArr);//会员价格
			$goodsAttr = $mGoods->getAttrByGoodId($goodsId);//商品属性
			$goodsAttrs=array();
	        foreach ($goodsAttr as $k => $v) {
	            $goodsAttrs[$v['attr_id']][]=$v;
	        }    
			$attr = M('attr')->where(array('type_id'=>$goodRes['type_id']))->select();//类型属性
			$goodsPicRes = $mGoods->getGoodsPic($goodsId);//获取图片
			$pic = array();
			foreach ($goodsPicRes as $key => $value) {
			   	$pic[$value['id']]['original'] = $value['original'];
			   	$pic[$value['id']]['max_thumb'] = $value['max_thumb'];
			   	$pic[$value['id']]['sm_thumb'] = $value['sm_thumb'];
			}
			$this->assign('goodsPicRes',$goodsPicRes);
			$this->assign('pic',$pic);
			$this->assign('goodsAttrs',$goodsAttrs);
			$this->assign('attr',$attr);
		}
		$mCate = D('Cate');
		$cateRes = $mCate->catesTree();
		$this->assign('cateRes',$cateRes);
		//商品品牌
		$brandRes = D('Brand')->getBrandsLock();
		//获取会员等级
		$mMember = D('Member');
		$Levels = $mMember->getMemberLevels();
		//获取商品类型
		$this->assign('goodsType',D('goods')->getAllGoodsType());
		$this->assign('brandRes',$brandRes);
		$this->assign('Levels',$Levels);
		$this->display('Goods/add');
	}
	/**
	 * 商品异步修改
	 * @return [type] [description]
	 */
	public function ajaxEdit(){
		$this->_inputAjax();
		$goodsId = I('post.goodsId');
		$checkGoodsId = $this->_checkGoodsId($goodsId,true);
		if($checkGoodsId['err_code'] > 0) $this->ajaxReturn($checkGoodsId);
		$data= [];
		$checkInfo = $this->_checkData($data);
		if($checkInfo['err_code'] > 0) $this->ajaxReturn($checkInfo);
		M()->startTrans();
		$returnArr = D('Goods')->editGoods($data,$goodsId);
		$this->ajaxReturn($returnArr);
	}

	public function add(){
		$this->_inputAjax();
		$data= [];
		$checkInfo = $this->_checkData($data);      
		if($checkInfo['err_code'] > 0) $this->ajaxReturn($checkInfo);
		M()->startTrans();
		$returnArr = D('Goods')->setGoods($data);
		$this->ajaxReturn($returnArr);
	}

	public function del(){
		$this->_inputAjax();
		$goodsId = I('post.goodsId');
		$checkGoodsId = $this->_checkGoodsId($goodsId,true);
		if($checkGoodsId['err_code'] > 0) $this->ajaxReturn($checkGoodsId);
		M()->startTrans();
		$returnRes = D('Goods')->delGoods($goodsId);
		if($returnRes['err_code'] > 0){
			M()->rollback();
			$this->ajaxReturn($returnRes);
		}else{
			M()->commit();
			$this->ajaxReturn($returnRes);
		}
	}

	/***************************商品库存******************************/
	public function product(){
		$goodsId = I('get.goodsId');
		$mGoods = D('goods');
		$res = $mGoods->getProduct($goodsId);
		
		if(empty($res)){
			$this->error('没有您要的商品货品信息,请重新编辑哦');
		}
		$productRes = $mGoods->getProducts($goodsId);
		foreach ($productRes as $key => $value) {
			$productRes[$key]['goods_attr_arr'] = explode(',',$value['goods_attr']);
		}    
		$this->assign('productRes',$productRes);
		$this->assign('res',$res);
		$this->display('Goods/productLst');
	}
	/**
	 * 添加商品货品库存
	 * @return [type] [description]
	 */
	public function ajaxAddProduct(){
		$this->_inputAjax();
		$goodsId = I('post.goodsId');
		$checkInfo = $this->_checkGoodsId($goodsId,false);
		if($checkInfo['err_code'] > 0) $this->ajaxReturn($checkInfo);
		$product = json_decode(htmlspecialchars_decode(I('post.product')),true);
		//在添加之前执行删除
		M()->startTrans();
		$mGoods = D('Goods');
		$flag = $mGoods->delProduct($goodsId);
		if($flag === false){
			M()->rollback();
			return $this->ajaxReturn(array_err(777,'添加失败,重试后仍然无效请联系管理员'));
		}
		$data['goods_id'] = $goodsId;
		$data['product'] = $product;
		//检查数据是否存在重复或者超过限制   @todo
		$flag = $mGoods->addProduct($goodsId,$product);
		if($flag === true){
			M()->commit();
			$this->ajaxReturn(array_err(0,'添加成功'));
		}else{
			M()->rollback();
			$this->ajaxReturn(array_err(776,'添加失败'));
		}
	}

	public function delGoodsPic(){
		$this->_inputAjax();
		$picId = I('post.picId');
		if(empty('picId')) $this->ajaxReturn(array_err(99,'图标标识不能为空'));
		M()->startTrans();
		$where['id'] = $picId;
		$mGoodsPic = M('goods_pic');
		$picRes = $mGoodsPic->where($where)->lock(true)->find();
		if(empty($picRes)){
			$this->ajaxReturn(array_err(98,'没有您要的图片,请核实'));
		}
		$flag = $mGoodsPic->delete($picId);
		if($flag !== false){
			$mGoodsPic->commit();
			@unlink($picRes['original']);
			@unlink($picRes['max_thumb']);
			@unlink($picRes['sm_thumb']);
			return $this->ajaxReturn(array_err(0,'图片删除成功'));
		}else{
			$mGoodsPic->rollback();
			return $this->ajaxReturn(array_err(97,'图片删除失败'));
		}

	}

	protected function _checkGoodsId($goodsId,$lock = false,&$goodRes){
		if(empty($goodsId)) return array_err(653,'商品标识不能为空');
		$param['id'] = $goodsId; 
		$goodRes = D('Goods')->getGoods($param,$lock);
		if(empty($goodRes)) return array_err(654,'没有您要的商品信息');
		return array_err(0,'success');
	}

	protected function _checkData(&$data){ 
		$goodsName = I('post.goodsName');
		if(empty($goodsName)) return array_err(999,'商品名称不能为空');
		$data['goods']['goods_name'] = $goodsName;
		$cateId = I('post.cateId');
		$checkCateId = A('Cate','Event')->_checkCateId($cateId);
		if($checkCateId['err_code'] > 0) return $checkCateId;
		$data['goods']['cate_id'] = $cateId;
		$typeId = I('post.typeId');
		$checkTypeId = $this->_checkTypeId($typeId,false);
		if($checkTypeId['err_code'] > 0) return $checkTypeId;
		$data['goods']['type_id'] = $typeId;
		$brandId = I('post.brandId');
		if(!empty($brandId)){
			$checkBrandId = A('Brand','Event')->_checkBrandId($brandId);
			if($checkBrandId['err_code'] > 0) return $checkBrandId;
			$data['goods']['brand_id'] = $brandId;
		}
		$imgurl = I('post.imgurl');
		if(!empty($imgurl)){
			$data['goods']['original'] = $imgurl;
			$arr = pathinfo($data['goods']['original']);
			foreach (C('GOODS_IMG') as $key => $value) {
				$data['goods'][$key] = $arr['dirname'].'/thumb/'.$arr['filename'].'_'.$value['WIDTH']
				.'X'.$value['HEIGHT'].'.'.$arr['extension'];
			}
		}
		$marketPrice = I('post.marketPrice','','trim');
		if(empty($marketPrice)) return array_err(997,'市场价格不能为空');
		if($marketPrice < 0 || !is_numeric($marketPrice)) return array_err(996,'您输入的市场价格必须大于0或者必须是数字');
		$data['goods']['market_price'] = $marketPrice;
		$shopPrice =I('post.shopPrice');
		if($shopPrice < 0 || !is_numeric($shopPrice)) return array_err(995,'您输入的本店价格必须大于0或者必须是数字');
		$data['goods']['shop_price'] = $shopPrice;
		$goodsWeight = I('post.goodsWeight');
		if(empty($goodsWeight))  return array_err(994,'您输入的商品重量必须大于0或者必须是数字');
		$data['goods']['goods_weight'] = $goodsWeight;
		$onSale = I('post.onSale');
		if($onSale!=1 && $onSale!=0) return array_err(993,'上下架标识错误');
		$data['goods']['onsale'] = $onSale;
		$goodsDesc =addslashes(I('post.goodsDesc'));
		if(empty($goodsDesc)) return array_err(992,'商品描述不能为空');
		$data['goods']['goods_desc'] = $goodsDesc;
		$levelPrice = json_decode(htmlspecialchars_decode(I('post.levelPrice')),true);
		if(!empty($levelPrice)){
			foreach ($levelPrice as $key => $value) {
				if(!is_numeric($value['level'])){
					return array_err(991,'会员价格必须是数字');
				}
			}
			$checkLevel = $this->_checkLevel($levelPrice);
			if($checkLevel['err_code'] > 0) return $checkLevel;
			$data['levelPrice'] = $levelPrice;
		}
		$images = I('post.images');
		if(!empty($images)){
			foreach ($images as $key => $value) {
	 			$goodsImg[] = json_decode(htmlspecialchars_decode($value),true);
	 		}
			$data['goodsImg'] = $goodsImg;
		}
		$goodAttr = I('post.goodAttr');
		if(!empty($goodAttr)){
			$goodAttr = json_decode(htmlspecialchars_decode(I('post.goodAttr')),true);
			$data['goodAttr'] = $goodAttr;
		}
		$oldGoodAttr = I('post.oldGoodAttr');
		if(!empty($oldGoodAttr)){
			$oldGoodAttr = json_decode(htmlspecialchars_decode(I('post.oldGoodAttr')),true);
			$data['oldGoodAttr'] = $oldGoodAttr;
		}
		return array_err(0,'success');
	}

	protected function _checkLevel($levelPrice){
		$leverId = array_column($levelPrice,'id');
		$param['id'] = array('in',implode(',',$leverId));
		$field = 'id';
		if(count(D('Member')->getMemberLevels($param,$field)) != count($levelPrice))  return array_err(555,'会员等级类型错误');
		return array_err(0,'success');
	}

	/**
	 * 上传图片
	 * @return [type] [description]
	 */
	public function upload(){
		$info = upload(C('UPLOAD_GOODS'));
		$up_img=$info['original']['savepath'].$info['original']['savename'];
		//设置小图片
		$width = C('GOODS_IMG'); 
		$open = C('UPLOAD_GOODS.rootPath').$up_img;
		$fileName = pathinfo($up_img ,PATHINFO_FILENAME);
		$saveDir = C('UPLOAD_GOODS.rootPath').$info['original']['savepath'].'thumb/';
		if(!is_dir($saveDir)) mkdir($saveDir);

		foreach (C('GOODS_IMG') as $key => $value) {
			$save = $saveDir.$fileName.'_'."{$value['WIDTH']}".'X'."{$value['HEIGHT']}".'.'.pathinfo($up_img ,PATHINFO_EXTENSION);
			setThumb($value['WIDTH'],$value['HEIGHT'],$open,$save);
		}
		echo"<script>imgid=parent.document.getElementById('imgid');imgid.src='".C('UPLOAD_GOODS.rootPath')."{$up_img}'</script>";//将图片显示到页面
		echo"<script>imgurl=parent.document.getElementById('imgurl');imgurl.value='".C('UPLOAD_GOODS.rootPath')."{$up_img}'</script>"; 
	}

	/**
	 * 上传图片
	 * @return [type] [description]
	 */
	public function uploadPic(){
		$info = upload(C('UPLOAD_GOODS'));    
		$up_img=$info['Filedata']['savepath'].$info['Filedata']['savename'];
		//设置小图片
		$width = C('GOODS_IMG_DETAIL'); 
		$fiel = C('UPLOAD_GOODS.rootPath').$up_img;
		$fileName = pathinfo($up_img ,PATHINFO_FILENAME);
		$saveDir = C('UPLOAD_GOODS.rootPath').$info['Filedata']['savepath'].'thumb/';
		if(!is_dir($saveDir)) mkdir($saveDir);

		foreach (C('GOODS_IMG_DETAIL') as $key => $value) {
			$save[$key] = $saveDir.$fileName.'_'."{$value['WIDTH']}".'X'."{$value['HEIGHT']}".'.'.pathinfo($up_img ,PATHINFO_EXTENSION);
			setThumb($value['WIDTH'],$value['HEIGHT'],$fiel,$save[$key]);
		}
		$save['original'] = $fiel;
		$this->ajaxReturn($save);
	}

	/****************************************商品类型**************************/
	/**
	 * 商品类型列表
	 * @return [type] [description]
	 */
	public function getType(){
		$mGoods = D('Goods');
		$goodType = $mGoods->getGoodsTypes();
		$this->assign('goodType',$goodType);
		$this->display('Goods/typeLst');
	}

	/**
	 * 商品类型列表
	 * @return [type] [description]
	 */
	public function ajaxGetType(){
		$this->_inputAjax();
		$mGoods = D('Goods');
		$param = [];
		$param['limit'] = I('post.pageCount',10);
		$param['page'] = I('post.currPage',1);
		$param['typeId'] = I('post.typeId');
		$count = $mGoods->getGoodsTypeCount($param);
		if($count > 0){
			$goodType = array_err(0,'success');
			$goodType['data'] = $mGoods->getGoodsTypes($param);
			$goodType['count'] = $count;
		}else{
			$goodType = array_err(0,'不存在商品类型');
			$goodType['data'] = [];
		}
		$this->ajaxReturn($goodType);
	}

	/**
	 *添加商品类型
	 */
	public function addType(){
		$typeId = I('get.typeId');
		if($typeId){
			$goodsTypeRes = [];
			$checkInfo = $this->_checkTypeId($typeId,false,$goodsTypeRes);
			if($checkInfo['err_code'] > 0) $this->error($checkInfo['err_msg']);
			$this->assign('typeRes',$goodsTypeRes);
		}
		$this->display('Goods/typeAdd');
	}

	/**
	 *修改商品类型
	 */
	public function ajaxEditType(){
		$this->_inputAjax();
		$typeId = I('post.typeId');
		$checkTypeId = $this->_checkTypeId($typeId,true);
		if($checkTypeId['err_code'] > 0) $this->ajaxReturn($checkTypeId);
		$data  = [];
		$checkInfo = $this->_checkType($data,$typeId);
		if($checkInfo['err_code'] > 0) $this->ajaxReturn($checkInfo);
		$mGoods = D('Goods');
		$mGoods->startTrans();
		$flag = $mGoods->editGoodsType($data);
		if($flag !== false){
			$mGoods->commit();
			$this->ajaxReturn(array_err(0,'修改商品类型成功'));
		}
		$mBrand->rollback();
		$this->ajaxReturn(array_err(971,'修改商品类型失败'));
	}
	/**
	 * 删除商品类型
	 * @return [type] [description]
	 */
	public function ajaxDelType(){
		$this->_inputAjax();
		$typeId = I('post.typeId');
		$checkCateId = $this->_checkTypeId($typeId,true);
		if($checkCateId['err_code'] > 0) $this->ajaxReturn($checkCateId);
		$mGoods = D('Goods');
		if($mGoods->delGoodsType($typeId) === false){
			$mGoods->rollback();
			$this->ajaxReturn(array_err(789,'删除失败'));
		}else {
			$mGoods->commit();
			$this->ajaxReturn(array_err(0,'删除成功'));

		};
	}
	/**
	 *增加商品类型 
	 * @return [type] [description]
	 */
	public function ajaxAddType(){
		$this->_inputAjax();
		$data = [];
		$checkInfo = $this->_checkType($data);
		if($checkInfo['err_code'] > 0){
			$this->ajaxReturn($checkInfo);
		}
		$mGoods = D('Goods');
		//添加
		$typeId = $mGoods->setGoodsType($data);
		if($typeId > 0){
			$this->ajaxReturn(array_err(0,'添加商品类型成功'));
		}
		$this->ajaxReturn(array_err(991,'添加商品类型失败'));
	}
	/*****************************商品属性*****************************/
	/**
	 * 获取商品属性的页面
	 * @return [type] [description]
	 */
	public function getAttr(){
		$this->display('Goods/attrLst');
	}
	/**
	 * 异步获取商品属性
	 * @return [type] [description]
	 */
	public function ajaxGetAttr(){
		$this->_inputAjax();
		$typeId = I('post.typeId');
		$param['page'] = I('post.currPage');
		$param['limit'] = I('post.pageCount');
		$checkTypeId = $this->_checkTypeId($typeId,false);
		if ($checkTypeId['err_code'] > 0) $this->ajaxReturn($checkTypeId);
		$param['typeId']= $typeId;
		$arrReturn = array_err(0,'success');
		$arrReturn['count'] = 0;
		$arrReturn['data'] = array();
		D('Attr')->getAttrs($param,$arrReturn['count'],$arrReturn['data']);
		foreach ($arrReturn['data'] as $key => $value) {
			foreach (C('ATTR_TYPE') as $key1 => $value1) {
				if($key1 == $value['attr_type']){
					$arrReturn['data'][$key]['attr_type_name'] = $value1;
				}
			}
		}
		$this->ajaxReturn($arrReturn);
	}
	/**
	 * 添加修改商品属性页面
	 */
	public function addAttr(){
		$attrId = I('get.attrId');
		if($attrId){
			$checkAttrId = $this->_checkAttrId($attrId,false);  
			if($checkAttrId['err_code'] > 0) $this->error($checkAttrId['err_msg']);
			$this->assign('attrRes',$checkAttrId);
		}
		$mGoods = D('Goods');
		$goodType = $mGoods->getGoodsTypes();
		$this->assign('goodType',$goodType);
		$this->display('Goods/addAttr');
	}

	/**
	 * 添加商品属性
	 * @return [type] [description]
	 */
	public function ajaxAddAttr(){
		$this->_inputAjax();
		$data = [];
		$checkAttr = $this->_checkAttrData($data);
		if($checkAttr['err_code'] > 0) $this->ajaxReturn($checkAttr);
		$attrId = D('attr')->addAttr($data);
		if($attrId > 0){
			$this->ajaxReturn(array_err(0,'添加属性成功'));
		}else{
			$this->ajaxReturn(array_err(666,'添加属性失败'));			
		}
	}

	/**
	 * 修改商品属性
	 * @return [type] [description]
	 */
	public function ajaxEditAttr(){
		$this->_inputAjax();
		$attrId = I('post.attrId');
		$checkAttrId = $this->_checkAttrId($attrId,true);
		if($checkAttrId['err_code'] > 0){
			$this->ajaxReturn($checkAttrId);
		}
		$data = [];
		$checkAttr = $this->_checkAttrData($data,$attrId);
		if($checkAttr['err_code'] > 0) $this->ajaxReturn($checkAttr);
		$mAttr = D('attr');
		$mAttr->startTrans();
		$flag = $mAttr->editAttr($data,$attrId);
		if($attrId > 0){
			$mAttr->commit();
			$this->ajaxReturn(array_err(0,'修改属性成功'));
		}else{
			$mAttr->rollback();
			$this->ajaxReturn(array_err(666,'修改属性失败'));			
		}
	}

	/**
	 * 删除商品类型属性
	 * @return [type] [description]
	 */
	public function ajaxDelAttr(){
		$this->_inputAjax();
		$mAttr = D('attr');
		$mAttr->startTrans();
		$attrIdArr = json_decode(htmlspecialchars_decode(I('post.attrIdArr')), true);
		$attrRes = $mAttr->getAttrById($attrIdArr,true);
		if(count($attrRes) != count($attrIdArr)) $this->ajaxReturn(array_err(665,'存在非法标识,请核实'));
		$flag = $mAttr->delAttr($attrIdArr);   
		if($flag > 0){
			$mAttr->commit();
			$this->ajaxReturn(array_err(0,'删除属性成功'));
		}else{
			$mAttr->rollback();
			$this->ajaxReturn(array_err(666,'删除属性失败'));			
		}
	}
	/**
	 * 删除商品属性
	 * @return [type] [description]
	 */
	public function delGoodsAttr(){
		$attrId = I('post.attrId');
		if(empty($attrId)) $this->ajaxReturn(777,'商品属性标识不能为空');
		M()->startTrans();
		$mGoods = D('Goods');
		$flag = $mGoods->delGoodsAttr($attrId);
		if($flag === true){
			$mGoods->commit();
			$this->ajaxReturn(array_err(0,'删除成功'));
		}else{
			$mGoods->rollback();
			$this->ajaxReturn(array_err(555,'删除失败,请稍后在试看看'));
		}

	}

	protected function _checkAttrId($attrId,$lock = false){
		if(empty($attrId)) return array_err(555,'属性标识不能为空');
		$param['id'] = $attrId;
		$res = D('attr')->getAttr($param,$lock);
		if(empty($res)) return array_err(556,'没有您要的属性');
		return $res;
	}

	protected function _checkAttrData(&$data,$attrId = ''){
		$attrName = I('post.attr_name');
		$attrType = I('post.attr_type');
		$attrValues = I('post.attr_values');
		$typeId = I('post.type_id');
		if(empty($attrName))  return array_err(658,'属性名称不能为空');
		if(empty($typeId))  return array_err(658,'商品类型不能为空');
		if($attrType != 0 && $attrType != 1)  return array_err(657,'未知的属性类型');
		if($attrType == 1){
			if(empty($attrName))  return array_err(656,'属性值不能为空');
		}
		$param['attr_name'] = $attrName;
		$param['type_id'] = $typeId;
		if(!empty($attrId)){
			$param['id'] = array('NEQ',$attrId);
		}
		$res = D('Attr')->getAttr($param);
		if($res) return array_err(654,'属性名称已被使用,请重新设置');
		$data['attr_name'] = $attrName;
		$data['attr_type'] = $attrType;
		$data['attr_values'] = $attrValues;
		$data['type_id'] = $typeId;
		return array_err(0,'success');
	}

	protected function _checkType(&$data,$typeId = ''){
		$typeName = I('post.typeName');
		if(empty($typeName)) return array_err(888,'类型名称不能为空');
		$param['type_name'] = $typeName;
		if($typeId){
			$param['id'] = array('NEQ',$typeId);
			$data['id'] = $typeId;
		}
		$field = 'id';
		if(D('Goods')->getGoodsType($param,$field)){
			return array_err(887,'类型名称已存在，请更换');
		};
		$data['type_name'] = $typeName;
		return array_err(0,'success');
	}
	/**
	 * 检查商品类型id
	 * @return [type] [description]
	 */
	protected function _checkTypeId($typeId,$lock = false,&$goodsTypeRes){
		if(empty($typeId)) return array_err(886,'商品类型标识不能为空');
		$where['id'] = $typeId;
		if($lock){
			$goodsTypeRes = M('type')->lock(true)->where($where)->find();
		}else{
			$goodsTypeRes = M('type')->where($where)->find();
		}
		if(empty($goodsTypeRes)) return array_err(985,'商品类型不存在,请核实!');
		return array_err(0,'success');
	}

}