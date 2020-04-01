<?php
// 应用公共文件
use think\facade\Config;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * 返回json数据，用于接口
 * @Author   听雨
 * @DateTime 2020-03-31
 * @param    integer    $code     [description]
 * @param    string     $msg      [description]
 * @param    array      $data     [description]
 * @param    string     $url      [description]
 * @param    integer    $httpCode [description]
 * @param    array      $header   [description]
 * @param    array      $options  [description]
 * @return   [type]               [description]
 */
function vae_assign($code=200, $msg="OK", $data=[], $url='', $httpCode=200, $header = [], $options = []){
    $res=['code'=>$code];
    $res['msg']=$msg;
    $res['url']=$url;
    if(is_object($data)){
        $data=$data->toArray();
    }
    $res['data']=$data;
    $response = \think\Response::create($res, "json",$httpCode, $header, $options);
    throw new \think\exception\HttpResponseException($response);
}

/**
 * 发邮件
 * @Author   听雨
 * @DateTime 2020-03-20
 * @param    [type]     $toemail    收件邮箱
 * @param    [type]     $toName     收件人称呼
 * @param    [type]     $title      邮件标题
 * @param    [type]     $content    邮件正文
 * @param    string     $fromEmail  发件邮箱
 * @param    string     $fromName   发件人称呼
 * @param    string     $replyEmail 回复邮箱
 * @param    string     $replyName  回复人称呼
 * @return   [type]                 [description]
 */
function vae_send_email($toemail, $toName, $title, $content, $fromEmail = "", $fromName = "", $replyEmail = "", $replyName=""){
    $config = Config::get('email');

    if(NULL == $config) {
        abort(0,'请先在系统->配置->邮箱配置中配置您的SMTP信息且完成提交');
    }

    $fromEmail  = $fromEmail?$fromEmail:$config['email'];
    $fromName   = $fromName?$fromName:$config['from'];
    $replyEmail = $replyEmail?$replyEmail:$fromEmail;
    $replyName  = $replyName?$replyName:$fromName;

    $mail = new PHPMailer(true);
    
    //Server settings
    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                  // Enable verbose debug output
    $mail->isSMTP();                                        // Send using SMTP
    $mail->CharSet    = "utf8";                             // 编码格式为utf8，不设置编码的话，中文会出现乱码
    $mail->Host       = $config['smtp'];                    // Set the SMTP server to send through
    $mail->SMTPAuth   = true;                               // Enable SMTP authentication
    $mail->Username   = $config['username'];                // SMTP username
    $mail->Password   = $config['password'];                // SMTP password
    $mail->SMTPSecure = 'ssl';                              // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
    $mail->Port       = $config['port'];                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

    //Recipients
    $mail->setFrom($fromEmail, "=?UTF-8?B?".base64_encode($fromName)."?=");
    $mail->addAddress($toemail, "=?UTF-8?B?".base64_encode($toName)."?=");                    // Add a recipient
    // $mail->addAddress('ellen@example.com');               // Name is optional
    $mail->addReplyTo($replyEmail, "=?UTF-8?B?".base64_encode($replyName)."?=");
    // $mail->addCC('cc@example.com');
    // $mail->addBCC('bcc@example.com');

    // Attachments
    // $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

    // Content
    $mail->isHTML(true);                                     // Set email format to HTML
    $mail->Subject = "=?UTF-8?B?".base64_encode($title)."?=";
    $mail->Body    = $content;
    // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
}

/**
 * 阿里大鱼发短信
 * @Author   听雨
 * @DateTime 2020-03-16
 * @param    [type]     $phone [description]
 * @param    [type]     $param [description]
 * @param    [type]     $code  [description]
 * @param    string     $type  [description]
 * @return   [type]            [description]
 */
function vae_send_sms($phone, $param, $code, $type = "normal")
{
    // 配置信息
    include root_path()."extend/dayu/top/TopClient.php";
    include root_path()."extend/dayu/top/TopLogger.php";
    include root_path()."extend/dayu/top/request/AlibabaAliqinFcSmsNumSendRequest.php";
    include root_path()."extend/dayu/top/ResultSet.php";
    include root_path()."extend/dayu/top/RequestCheckUtil.php";
    
    $c = new \TopClient();
    $conf = Config::get('dayu');
    $c ->appkey = $conf['appkey'];
    $c ->secretKey = $conf['secretkey'];

    $req = new \AlibabaAliqinFcSmsNumSendRequest();
    //公共回传参数，在“消息返回”中会透传回该参数。非必须
    $req ->setExtend("");
    //短信类型，传入值请填写normal
    $req ->setSmsType($type);
    //短信签名，传入的短信签名必须是在阿里大于“管理中心-验证码/短信通知/推广短信-配置短信签名”中的可用签名。
    $req ->setSmsFreeSignName($conf['FreeSignName']);
    //短信模板变量，传参规则{"key":"value"}，key的名字须和申请模板中的变量名一致，多个变量之间以逗号隔开。
    $req ->setSmsParam($param);
    //短信接收号码。支持单个或多个手机号码，传入号码为11位手机号码，不能加0或+86。群发短信需传入多个号码，以英文逗号分隔，一次调用最多传入200个号码。
    $req ->setRecNum($phone);
    //短信模板ID，传入的模板必须是在阿里大于“管理中心-短信模板管理”中的可用模板。
    $req ->setSmsTemplateCode($code);
    //发送
    $resp = $c ->execute($req);
}

/**
 * url中现有的参数+点击的连接中包含的参数合并计算
 * @Author   听雨
 * @DateTime 2020-03-16
 * @param    array      $params [description]
 * @param    string     $url    [description]
 * @return   [type]             [description]
 */
function vae_get_route_url($params = [], $url = '')
{
    $get = request()->param();
    foreach ($get as $urlparam => $value) {
        if (strpos($urlparam, $request()->action())) {
            unset($get[$urlparam]);
        } else {
            $get[$urlparam] = urldecode($value);
        }
    }

    if (is_array($params)) {
        $get = array_merge($get, $params);
    }
    if (empty($url)) {
        return url($request()->action(), $get);
    } else {
        return url($url, $get);
    }

}

/**
 * 根据导航组的标识获取导航数据集
 * @Author   听雨
 * @DateTime 2020-03-19
 * @param    string     $key [description]
 * @return   [type]          [description]
 */
function vae_get_nav(string $key)
{
    if(Cache::has('NAV_'.$key))
    {
        $nav = Cache::get('NAV_'.$key);
    }
    else
    {
        $groupId = Db::name('nav_group')->where('key',$key)->value('id');
        if(!$groupId)
        {
            return abort('404','导航组不存在');
        }
        $nav = Db::name('nav')->where('nav_group_id',$groupId)->field('title,icon,app_src,wechat_src,sort')->select()->toArray();
        foreach ($nav as $k => $v) {
            $nav[$k]['icon'] = Config::get('web.domain').$v['icon'];
        }
        Cache::set('NAV_'.$key,$nav);
    }
    return $nav;
}

/**
 * 根据轮播组的标识获取轮播数据集
 * @Author   听雨
 * @DateTime 2020-03-19
 * @param    string     $key [description]
 * @return   [type]          [description]
 */
function vae_get_slide(string $key)
{
    if(Cache::has('SLIDE_'.$key))
    {
        $slide = Cache::get('SLIDE_'.$key);
    }
    else
    {
        $groupId = Db::name('slide_group')->where('key',$key)->value('id');
        if(!$groupId)
        {
            return abort('404','轮播组不存在');
        }
        $slide = Db::name('slide')->where('slide_group_id',$groupId)->field('title,icon,src,sort')->select()->toArray();
        foreach ($slide as $k => $v) {
            $slide[$k]['icon'] = Config::get('web.domain').$v['icon'];
        }
        Cache::set('SLIDE_'.$key,$slide);
    }
    return $slide;
}