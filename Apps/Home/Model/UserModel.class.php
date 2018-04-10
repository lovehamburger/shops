<?php
namespace Home\Model;
use Think\Model;
class UserModel extends Model{

	public function getUserInfoByMobile($mobile,$field){
        $param['member_mobile'] = $mobile;
        return M('member')->where($param)->getField($field);
    }

    public function getUserInfoByName($name,$field){
		$param['member_name'] = $name;
		return M('member')->where($param)->getField($field);
	}

    public function getUserInfoByEmail($email,$field){
        $param['member_email'] = $email;
        return M('member')->where($param)->getField($field);
    }

    public function addUser($dataRes){
       return M('member')->add($dataRes);
    }

    public function login($userName,$password){
       
    }
}