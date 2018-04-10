<?php

/**
 * 信息发送公用库
 * @author yangxb 2016.5.14
 * @copyright 晨丰科技有限公司
 * @abstract 含短息、邮件等发送
 */

/**
 * 发送ERP消息
 * @param string $msgData 消息内容
 */
function Send_ErpMQ($msgData){
    vendor("RabbitMQ.rabbitmq#class");
    $mq = new \RabbitMQ(C('ERPS.ORDER_EXCHANGE_NAME'), '', C('ERPS.ORDER_QUEUE_NAME'));
    return $mq->send($msgData);
}

/**
 * 发送MQ队列消息-聊天使用
 * @param int $userID 用户标识
 * @param string $msg 消息内容
 */
function Send_MQMsg($userID, $msg){
    //获取消息ID
    $nowTime = _NOW_TIME();
    $data = array(
        'from_user_id' => 0,
        'to_user_id' => $userID,
        'message' => $msg,
        'create_date' => $nowTime,
        'state' => 0
    );

    $msgID = D('user_chat_logs')->add($data);
    if(false === $msgID){
        return array_err(3700, '生成消息标识出错，发送消息失败');
    }

    //生成消息数组
    $msgData = array(
        'err_code' => 0,
        'err_msg' => '',
        'msg_id' => $msgID,
        'from_user' => 0,
        'to_user' => $userID,
        'msg_content' => $msg,
        'msg_time' => $nowTime
    );

    vendor("RabbitMQ.rabbitmq#class");
    $mq = new \RabbitMQ('', '', 'Queue.Chat.'.$userID);
    if(!$mq->send(json_encode($msgData)/*, 'Route.Chat.'.$userID*/)){
        return array_err(3701, '发送MQ消息失败');
    }

    return array_err(0, 'success');
}

/**
 * 系统邮件发送函数
 * @param array $mailObject 用户标识
 * @return boolean
 */
function _Send_Mail($mailObject){
    $userID = $mailObject['userID'];
    $to = $mailObject['to'];
    $name = $mailObject['name'];
    $subject = $mailObject['subject'];
    $body = $mailObject['body'];
    $attachment = $mailObject['attachment'];
    return Send_Mail($userID, $to, $name, $subject, $body, $attachment);
}

/**
* 系统邮件发送函数
* @param string $userID 用户标识
* @param string $to    接收邮件者邮箱
* @param string $name  接收邮件者名称
* @param string $subject 邮件主题
* @param string $body    邮件内容
* @param string $attachment 附件列表
* @return boolean
*/
function Send_Mail($userID, $to, $name, $subject = '', $body = '', $attachment = null){
    vendor('PHPMailer.PHPMailerAutoload');
    $config = C('THINK_EMAIL');
    //var_dump($config);
    //vendor('PHPMailer.class#phpmailer'); //从PHPMailer目录导class.phpmailer.php类文件
    $mail             = new PHPMailer(); //PHPMailer对象
    $mail->CharSet    = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
    $mail->IsSMTP();  // 设定使用SMTP服务
    $mail->SMTPDebug  = 0;                     // 关闭SMTP调试功能
    // 1 = errors and messages
    // 2 = messages only
    $mail->SMTPAuth   = true;                  // 启用 SMTP 验证功能
    $mail->SMTPSecure = 'tls';                 // 使用安全协议 tls ssl
    $mail->Host       = $config['SMTP_HOST'];  // SMTP 服务器
    $mail->Port       = $config['SMTP_PORT'];  // SMTP服务器的端口号
    $mail->Username   = $config['SMTP_USER'];  // SMTP服务器用户名
    $mail->Password   = $config['SMTP_PASS'];  // SMTP服务器密码
    $mail->SetFrom($config['FROM_EMAIL'], $config['FROM_NAME']);
    $replyEmail       = $config['REPLY_EMAIL']?$config['REPLY_EMAIL']:$config['FROM_EMAIL'];
    $replyName        = $config['REPLY_NAME']?$config['REPLY_NAME']:$config['FROM_NAME'];
    $mail->AddReplyTo($replyEmail, $replyName);
    $mail->Subject    = $subject;
    $mail->MsgHTML($body);
    $mail->AddAddress($to, $name);
    if(is_array($attachment)){ // 添加附件
        foreach ($attachment as $file){
            is_file($file) && $mail->AddAttachment($file);
        }
    }

    //返回信息
    $resp = array();

    if($mail->Send()){
        $resp['err_code'] = '0';
        $resp['err_msg'] = '邮件发送成功.';
    }else{
        $resp['err_code'] = '1';
        $resp['err_msg'] = $mail->ErrorInfo;
    }

    //记录邮件发送日志
    $mailLog = array();
    $mailLog['user_id'] = $userID;
    $mailLog['mail_addr'] = $to;
    $mailLog['subject'] = $subject;
    $mailLog['body'] = $body;
    $mailLog['err_code'] = $resp['err_code'];
    $mailLog['err_msg'] = $resp['err_msg'];
    $mailLog['create_date'] = date("Y-m-d H:i:s", NOW_TIME);
    M('mail_send_log')->add($mailLog);;

    return $resp;
}

/**
 * 系统短信发送函数
 * @param array $smsObject 短信输入数组
 * @return json
 */

function _Send_SMS($smsObject)
{
    $userID = $smsObject['userID'];
    $recNum = $smsObject['recNum'];
    $templateCode = $smsObject['templateCode'];
    $smsParam = $smsObject['smsParam'];
    return Send_SMS($userID, $recNum, $templateCode, $smsParam);
}

/**
 * 系统短信发送函数
 * @param string $scene 短信发送场景
 * @param string $recNum 接收短信的手机号码
 * @param string $templateCode  短信模板编号
 * @param string $smsParam 短信参数，传参规则{"key":"value"}，key的名字须和申请模板中的变量名一致，多个变量之间以逗号隔开
 * @param string $smsType 短信类型，默认normal
 * @return json
 */
function Send_SMS($scene, $recNum, $templateCode, $smsParam){
    $msgLog = array();
    //参数判断
    if($scene == ''){
        $msgLog['err_code'] = 'isv.SMS_EMPTY_USERID';
        $msgLog['err_msg'] = '使用场景不能为空';
        return $msgLog;
    }

    //参数判断
    if($recNum == ''){
        $msgLog['err_code'] = 'isv.SMS_EMPTY_RECNUM';
        $msgLog['err_msg'] = '接收短信的手机号码不能为空';
        return $msgLog;
    }

    //参数判断
    if($templateCode == ''){
        $msgLog['err_code'] = 'isv.SMS_EMPTY_TEMPLATECODE';
        $msgLog['err_msg'] = '短信模板不能为空';
        return $msgLog;
    }

    //参数判断
    if($smsParam == ''){
        $msgLog['err_code'] = 'isv.SMS_EMPTY_SMSPARAM';
        $msgLog['err_msg'] = '短信参数不能为空';
        return $msgLog;
    }

    vendor('Aliyun.aliyun-php-sdk-core.Config');
    vendor('Aliyun.aliyun-php-sdk-core.Autoloader.Autoloader');
    vendor('Aliyun.QuerySendDetailsRequest');
    vendor('Aliyun.SendSmsRequest');
    vendor('Aliyun.aliyun-php-sdk-core.DefaultAcsClient');
    $ts = C('THINK_SMS');
    $accessKeyId = $ts['APPKEY'];
    //阿里云KeyId
    $accessKeySecret = $ts['APPSECRET'];
    //阿里云KeySecret
    //短信API产品名
    $product = "Dysmsapi";
    //照写就行了
    //短信API产品域名
    $domain = "dysmsapi.aliyuncs.com";
    //照着写就行了
    //暂时不支持多Region
    $region = "cn-hangzhou";
    $profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);
    DefaultProfile::addEndpoint("cn-hangzhou", "cn-hangzhou", $product, $domain);
    $acsClient = new DefaultAcsClient($profile);
    $request = new SendSmsRequest();
    //必填-短信接收号码。支持以逗号分隔的形式进行批量调用，批量上限为20个手机号码,批量调用相对于单条调用及时性稍有延迟,验证码类型的短信推荐使用单条调用的方式
    $request->setPhoneNumbers($recNum);
    //这里填你要发送的电话号码
    //必填-短信签名
    $request->setSignName($ts['APPSIGNNAME']);
    //这里就是刚才让你记住的项目签名
    //必填-短信模板Code
    $request->setTemplateCode($templateCode);
    //这里就是模板CODE
    //选填-假如模板中存在变量需要替换则为必填(JSON格式)
    $request->setTemplateParam($smsParam);
    //选填-发送短信流水号
    $request->setOutId("1234");//照填就行了
    //发起访问请求
    try {
        $status = 1;
        $acsResponse = $acsClient->getAcsResponse($request); 
    } catch (\Exception $e) {
        $status = -1;
        $mag = $e->getMessage();
    }
    //记录日志
    $data['mobile'] = $recNum;
    $data['session_id'] = session_id();
    $data['add_time'] = time();
    //$data['code'] = $smsParam->code();
    $data['status'] = $status;
    $data['msg'] = $recNum;
    $data['scene'] = $scene;
    $data['error_msg'] =$mag;
    M('sms_log')->add($data);
    return $data;
}

/**
 * 发送微信模板消息
 * @param string $openID 用户微信ID
 * @param string $templateID 消息模板ID
 * @param array $data 发送数据
 * @param string $URL
 */

function Send_WX_TM($openID, $templateID, $data, $URL = ''){
    include_once APP_PATH.'Weixin/Common/function.php';
    return A('Weixin/Msg', 'Event')->SendTemplate($openID, $templateID, $data, $URL);
}

/**
 * 发送客服消息
 * @param string $openID 用户微信ID
 * @param string $msg 消息内容
 */
function Send_WX_CS($openID, $msg){
    include_once APP_PATH.'Weixin/Common/function.php';
    return A('Weixin/Msg', 'Event')->SendService($openID, $msg);
}

/**
 * 根据openid获取微信信息
 */
function getWxUser($openID){
    include_once APP_PATH.'Weixin/Common/function.php';
    return A('Weixin/Msg', 'Event')->getUserInfo($openID);
}

/**
 * 平台消息发送
 */
function _platMsgSend($userID, $msgType, $msgTemp, $msgParam){
    $msgData = array();
    $msgData['message_type'] = $msgType;
    $msgData['message_template'] = $msgTemp; //订单确认收货
    $msgData['message_param'] = $msgParam;
    $msgData['user_id'] = $userID;
    $msgData['create_date'] = _NOW_TIME();

    if(false === D('Messages')->add($msgData)){
        _WARNING('发送平台消息_platMsgSend失败', 'MSG');
        return false;
    }

    return true;
}

/**
 * 短信发送
 */
function _smsMsgSend($userID, $template, $msgParam, $smsPhones, $smsPrice){
    //查询用户可用余额，如果小于指定的值，就不发送短信
    $balance = D('AcctBalance')->getCanUseBalance($userID);
    if($balance < C('REMIND.Close_Sms_Balance')){
        //关闭余额提醒
        $data = array(
            'sms_status' => 0,
            'update_date' => _NOW_TIME()
        );
        D('message_config')->where('user_id = '.$userID)->save($data);
        return false;
    }


    $smsCode = $template['sms_code'];
    $payPrice = 0;
    foreach ($smsPhones as $row){
        $res = Send_SMS($userID, $row, $smsCode, $msgParam);
        _WARNING($res, 'MSG');

        //计算短信费用
        if(!empty($smsPrice) && empty($res['err_code'])){
            $payPrice += $smsPrice;
        }
    }

    //扣除短信费用
    if($payPrice > 0){
        $res = D('AcctBalancePayout')->payOutByPaymentPlan($userID, PAYMENT_PLAN_TYPE_PLAT, $payPrice, PAYOUT_TYPE_SMS);
        if(!empty($res['err_code'])){
            _BUSINESS('短信费用扣费失败。$userID = '.$userID.', msgTemp = '.$template['template_id']);
            return false;
        }
    }

    return true;
}

/**
 * 微信发送
 */
function _wxMsgSend($userID, $template, $wxParam){
    $wxCode = $template['wx_code'];
    if(empty($wxCode)){
        return false;
    }

    //获取用户的openid
    $userInfo = D('User')->getUserInfoByID($userID, 'login_name, wx_open_id')[0];
    $loginName = $userInfo['login_name'];
    $openID = $userInfo['wx_open_id'];

    if(empty($openID)){
        return false;
    }

    $wxParam['first'] = '亲爱的'.$loginName.'您有一条['.$template['template_name'].']消息';
    $res = Send_WX_TM($openID, $wxCode, $wxParam, C('WEB-BASE.WEB_URL').$wxParam['URL']);
    _WARNING($res, 'MSG');

    //记录微信发送日志
    $data = array(
        'msg_id' => $res['msgid'],
        'user_id' => $userID,
        'wx_template' => $template['template_id'],
        'wx_param' => json_encode($wxParam),
        'err_code' => $res['errcode'],
        'err_msg' => $res['errmsg'],
        'create_date' => _NOW_TIME()
    );

    if(false === D('wx_send_log')->add($data)){
        _WARNING('记录微信日志失败', 'MSG');
    }

    return $res;
}

/**
 * 消息发送ALL
 */
function SendMsg($msgData){
    $userID = $msgData['userID'];
    $msgType = $msgData['msgType'];
    $msgTemp = $msgData['msgTemp'];
    $msgParam = $msgData['msgParam'];
    $wxParam = $msgData['wxParam'];

    //获取用户的配置情况
    $msgConfig = D('MessageConfig')->getUserConfig($userID);
    $configContent = $msgConfig['config_content'];

    if(empty($msgConfig) || empty($configContent)){
        $platRemind = 1;
        $smsRemind = 0;
        $wxRemind = 0;
    }else{
        $smsStatus = $msgConfig['sms_status'];
        $smsPhones = explode(',', $msgConfig['sms_phones']);
        $smsPrice = $msgConfig['sms_price'];
        $wxStatus = $msgConfig['wx_status'];

        $configContent = json_decode($configContent, true);
        $platRemind = $configContent[$msgTemp]['pr'];
        $smsRemind = !empty($smsStatus) && $configContent[$msgTemp]['sr']; //@短信发送是要扣钱的哦
        $wxRemind = !empty($wxStatus) && $configContent[$msgTemp]['wr']; //@微信发送是需要关注公众号的哦
    }

    //发送平台
    if($platRemind){
        _platMsgSend($userID, $msgType, $msgTemp, $msgParam);
    }

    //获取模板
    $template = D('Messages')->getMessageTemplate($msgTemp);

    //发送短信
    if($smsRemind){
        _smsMsgSend($userID, $template, $msgParam, $smsPhones, $smsPrice);
    }

    //发送微信
    if($wxRemind){
        _wxMsgSend($userID, $template, $wxParam);
    }
}

/**
 * 发送MQ广播消息
 * @param $msg string 消息
 */
function sendBCMQ($jsonMsg){

    //创建连接
    $conn = new \AMQPConnection();
    $conn->setHost(C("ANDROID.MQ_HOST"));
    $conn->setPort(C("ANDROID.MQ_PORT"));

    //登录
    $conn->setLogin(C("ANDROID.MQ_USER"));
    $conn->setPassword(C("ANDROID.MQ_PASS"));

    //连接
    $conn->connect() or die("Cannot connect to the MQ!\n");

    try{
        $channel = new \AMQPChannel($conn);
        $exchange = new \AMQPExchange($channel);
        $exchange->setName(C("ANDROID.EX_BC_NAME"));

        $exchange->setType(AMQP_EX_TYPE_FANOUT); //fanout;
        $exchange->declare();

        //发送广播消息
        $exchange->publish($jsonMsg);

        //关闭连接
        $conn->disconnect();

    }catch(\AMQPConnectionException $mqe){
        die("MQ Exception");
    }

}

/**
 * APP广播通知
 */
function AppBroadCast($contentTitle, $contentText, $url, $subText = "晨丰科技", $ticker = "您有一条丰云新消息提醒"){
    $msg = array(
        "msg_type"=> 100,
        "content_title" => $contentTitle,
        "content_text" => $contentText,
        "sub_text" => $subText,
        "ticker" => $ticker,
        "url" => $url
    );

    sendBCMQ(json_encode($msg));
}


