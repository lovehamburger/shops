<?php
namespace Home\Model;
use Think\Model;
class UserModel extends Model{
	public function getUserInfoByMobile($mobile,$field){
		$param['mobile'] = $mobile;
		return M('users')->where($param)->getField($field);
	}

    public function getUserInfoByEmail($email,$field){
        $param['email'] = $email;
        return M('users')->where($param)->getField($field);
    }
}