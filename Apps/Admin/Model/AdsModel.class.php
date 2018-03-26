<?php
namespace Admin\Model;
use Admin\Model\BaseModel;
class AdsModel extends BaseModel{
    /**
     * 获取广告位
     * @param $param
     * @return mixed
     */
	public function getPos($param){
		return M('adpos')->where()->page($param['curr_page'],$param['page_count'])->select(); 
	}

    /**
     * 获取广告位的数量
     * @param $param
     * @return mixed
     */
	public function getPosCount($param){
		return M('adpos')->where($param)->count();
	}
	/**
	 * 添加广告位
	 * @param [type] $data [description]
	 */
	public function setPos($data){
		return M('adpos')->add($data);
	}

	/**
	 * 修改广告位
	 * @param [type] $data [description]
	 */
	public function editPos($data){     
		return M('adpos')->save($data);
	}

	/**
	 *获取指定广告位
	 */
	public function getPosById($posId,$lock){
		$where['id'] = $posId;
		if($lock){
			$res = M('adpos')->lock(true)->where($where)->find();
		}else{
			$res = M('adpos')->where($where)->find();
		}
		return $res;
	}

    /**
     * 获取广告
     */
	public function getAds($param){
        return M('ad')->page($param['cure_page'],$param['page_count'])->where()->select();
    }

    /**
     * 获取广告
     */
	public function getAdsCount($param){
        return M('ad')->where()->count();
    }

	/**
	 * 添加广告
	 * @param $data
	 * @return mixed
	 */
    public function addAds($data){
		return M('ad')->add($data);
	}

	public function editAds($data,$adsId){
    	$where['id'] = $adsId;
		return M('ad')->where($where)->save($data);
	}

	public function getAdsById($adsId,$lock){
		$where['id'] = $adsId;
		if($lock){
			return M('adpos')->lock(true)->where($where)->find();
		}
		return M('adpos')->where($where)->find();
	}
}