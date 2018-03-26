<?php
namespace Admin\Model;
use Think\Model;
class BaseModel extends Model{
	/**
     * 获取数组条件-适用于int
     * @author YangXB 2016.6.29
     * @param $value
     * @return array
     */
    public function getIDParamExt($value)
    {
        if(is_int($value) || is_float($value)){
            return $value;
        }elseif(is_string($value)){
            if(stripos($value, ',') === false){
                return strlen($value) <= 10 ? intval($value) : floatval($value);
            }else{
                return array('exp', 'in ('.$value.')');
            }
        }else{
            return array('in', $value);
        }
    }
}