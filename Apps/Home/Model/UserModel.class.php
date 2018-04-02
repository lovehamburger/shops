<?php
namespace Home\Model;
use Think\Model;
class UserModel extends Model{
	public function getUserInfoByPhone($adsPos,$count,$field){
		$param['posid'] = $adsPos;
		$param['ison'] = 1;
		return M('ad')->field($field)->where($param)->limit($count)->select();
	}

    public function getUserInfoByEmail($adsPos){
        $param['posid'] = $adsPos;
        $param['ison'] = 1;
        return M('ad')->field($field)->where($param)->limit($count)->select();
    }
}