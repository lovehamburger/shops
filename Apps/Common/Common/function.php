<?php
/**
* 错误码封装
* @param  int $errCode 错误码
* @param  string $errMsg 错误内容
* return  array
* @author YangXB 2016.6.12
*/
function array_err($errCode, $errMsg){
	return array('err_code' => $errCode, 'err_msg' => $errMsg);
}
/**
 * 设置验证码
 * @return [type] [description]
 */
function verify(){
	$config =    array(
		'length'      =>    3,     // 验证码位数
		'useNoise'    =>    true, // 关闭验证码杂点
		'codeSet' => '0123456789',
		);
		$Verify = new \Think\Verify($config);
		ob_clean();
		return $Verify->entry();
}

/**
 * 检验验证码
 * @return [type] [description]
 */
function checkVerify($code, $id = ''){
	$config = array(
		'reset' => false // 验证成功后是否重置，—————这里才是有效的。异步验证码要保证验证码不重置
	);
	$verify = new \Think\Verify($config);
    return $verify->check($code,$id);
}
/**
 * 上传图片
 * @return [type] [description]
 */
function upload($config){
    $upload = new \Think\Upload($config);// 实例化上传类
    // 上传文件 
    $info   =   $upload->upload();
    if(!$info) {// 上传错误提示错误信息
        return array_err(8888,$upload->getError());
    }else{// 上传成功
        return $info;
    }
}

function setThumb($width,$height,$open,$save){
	$image = new \Think\Image(); 
	$image->open($open);        
	// 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.jpg
	$image->thumb($width,$height)->save($save);
}

/**
 * 验证手机号码是否正确
 */
function checkTel($tel){
    return (preg_match("/^1[0-9]{10}$/",$tel)) ?true:false;
}
/**
 * 验证手邮箱是否正确
 */
function checkEmail($mail){
    $checkmail = "/\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/";//定义正则表达式
    if(preg_match($checkmail,$mail)){
        return true;
	}else{
        return false;
	}
}

/**
 * 生成密码的混淆码
 * @param unknown $p_iShop
 * @param unknown $p_iUserID
 */
function randPswSalter()
{
    $str = "j8f4kOMsKJqXSe2cnzHvAFWmDaVCZ0BQEpw69Ty5RtIUhGbrLxg1PodYu73iNl";

    $str = str_shuffle($str);

    return substr($str, 0, 5); // 从0开始截取位置5位
}
?> 
