<?php
namespace Home\Model;
use Think\Model;
class AdsModel extends Model{
	public function getAds($adsPos,$count,$field){
		$param['posid'] = $adsPos;
		$param['ison'] = 1;
		return M('ad')->field($field)->where($param)->limit($count)->select();
	}
}