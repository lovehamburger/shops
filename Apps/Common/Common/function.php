<?php


function _NOW_TIME(){
    return date('Y-m-d :H:i:s');
}
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
 * 手机验证码生成公用函数
 * @param string $sessKey      写入session的键名
 * @param string $phoneNbr     发送短信的手机号码
 * @param string $smsTemplate  短息模版
 * @param int $sessMin         X分钟可以获取一次短信验证码
 * @todo chenlh: 这边存在一个BUG，用户切换SESSION可以恶意获取验证码，可能要配合IP和单独的redis缓存才可控制
 */
function makeVerifyCode($sessKey, $phoneNbr, $smsTemplate, $sessMin = 2){
    //先判断session里的KEY是否都生成了
    $verifyCode = session($sessKey);
    if(!empty($verifyCode['time'])){
        //判断当前时间与上次session的时间差是否超过了 指定的分钟数
        if(strtotime('+'.$sessMin.' min', strtotime($verifyCode['time'])) > time()){
            return array_err(300, '短信验证码'.$sessMin.'分钟内只能获取一次');
        }
    }

    //生成验证码
    $code = validationCode();
    //发送短信
    $res = Send_SMS("1", $phoneNbr, $smsTemplate, sprintf("{\"code\":\"%s\"}", $code, '好加快'));
    //写入session
    if($res['status'] != -1){
        $verifyCode = array(
            'phone' => $phoneNbr,
            'code' => $code,
            'time' => _NOW_TIME()
        );
        session($sessKey, $verifyCode);
        return array_err(0, '获取短信验证码成功');
    }

    return array_err(301, '获取短信验证码失败');
}

/**
 * 生成数字验证码
 * @abstract 32位这个最多生成的位数是10位，取决于操作系统
 * @param number $iNum 生成验证码的位数
 * todo::待优化
 * @author luofc 2016.5.23
 */
function validationCode($iNum = 6)
{
    $max = '1';
    $min = 1;

    $max .= str_repeat('0', $iNum);

    $max = intval($max) - 1;

    $strCode = strval(mt_rand($min, $max));

    return  $iNum > strlen($strCode) ? str_repeat('0', $iNum - strlen($strCode)).$strCode : $strCode;
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
