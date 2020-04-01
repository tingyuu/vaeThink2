<?php
// +----------------------------------------------------------------------
// | vaeThink [ Programming makes me happy ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.vaeThink.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 听雨 < 389625819@qq.com >
// +---------------------------------------------------------------------
namespace app\admin\controller;
use app\admin\BaseController;
use think\facade\Config;
use think\facade\View;

class Conf extends BaseController
{
    /**
     * 配置首页
     * @Author   听雨
     * @DateTime 2020-03-16
     * @return   [type]     [description]
     */
    public function index()
    {
        // web配置
        $webconf = Config::get('web');
        View::assign('webConf',[
            'title'            => empty($webconf['title']) ? '' : $webconf['title'],
            'keywords'         => empty($webconf['keywords']) ? '' : $webconf['keywords'],
            'desc'             => empty($webconf['desc']) ? '' : $webconf['desc'],
            'logo'             => empty($webconf['logo']) ? '' : $webconf['logo'],
            'icp'              => empty($webconf['icp']) ? '' : $webconf['icp'],
            'code'             => empty($webconf['code']) ? '' : $webconf['code'],
        ]);

        // email配置
        $emailconf = Config::get('email');
        View::assign('emailConf',[
            'smtp'     => empty($emailconf['smtp']) ? '' : $emailconf['smtp'],
            'username' => empty($emailconf['username']) ? '' : $emailconf['username'],
            'password' => empty($emailconf['password']) ? '' : $emailconf['password'],
            'port'     => empty($emailconf['port']) ? '' : $emailconf['port'],
            'email'    => empty($emailconf['email']) ? '' : $emailconf['email'],
            'from'     => empty($emailconf['from']) ? '' : $emailconf['from'],
        ]);

        // 大鱼配置
        $dayuconf = Config::get('dayu');
        View::assign('dayuConf',[
            'appkey'     => empty($dayuconf['appkey']) ? '' : $dayuconf['appkey'],
            'secretkey' => empty($dayuconf['secretkey']) ? '' : $dayuconf['secretkey'],
            'FreeSignName' => empty($dayuconf['FreeSignName']) ? '' : $dayuconf['FreeSignName']
        ]);

        return View::fetch();
        
    }

    /**
     * 配置提交
     * @Author   听雨
     * @DateTime 2020-03-16
     * @return   [type]     [description]
     */
    public function confSubmit()
    {
        $param = $this->param;

        switch ($param['type']) {
            case 'web':
                $this->webConfSubmit($param);
                break;

            case 'email':
                $this->emailConfSubmit($param);
                break;
            
            default:
                $this->dayuConfSubmit($param);
                break;
        }
    }

    //提交网站信息
    private function webConfSubmit($param)
    {
        $this->validate($param,[
            'title|网站标题'          => 'require',
        ]);


        $conf = "<?php return ['title'=>'{$param["title"]}','keywords'=>'{$param["keywords"]}','logo'=>'{$param["logo"]}','desc'=>'{$param["desc"]}','icp'=>'{$param["icp"]}','code'=>'{$param["code"]}'];";

        try {
            file_put_contents(config_path() . "web.php",$conf);
        } catch (\Exception $e) {
            return vae_assign(202,'配置失败:'.$e->getMessage());
        }
        
        return vae_assign(200,'配置成功');
    }

    //提交邮箱配置
    private function emailConfSubmit($param)
    {
        $this->validate($param,[
            'smtp|smtp服务器'    => 'require',
            'username|账户'      => 'require',
            'password|密码'      => 'require',
            'port|端口'          => 'require',
            'email|发件邮箱'     => 'require',
            'from|发件人'        => 'require',
        ]);

        $conf = "<?php return ['smtp'=>'{$param["smtp"]}','username'=>'{$param["username"]}','password'=>'{$param["password"]}','port'=>'{$param["port"]}','email'=>'{$param["email"]}','from'=>'{$param["from"]}'];";

        try {
            file_put_contents(config_path() . "email.php",$conf);
        } catch (\Exception $e) {
            return vae_assign(202,'配置失败:'.$e->getMessage());
        }
        
        return vae_assign(200,'配置成功');
    }

    //提交大鱼短信配置
    private function dayuConfSubmit($param)
    {
        $this->validate($param,[
            'appkey'             => 'require',
            'secretkey'          => 'require',
            'FreeSignName|签名'   => 'require',
        ]);

        $conf = "<?php return ['appkey'=>'{$param["appkey"]}','secretkey'=>'{$param["secretkey"]}','FreeSignName'=>'{$param["FreeSignName"]}'];";

        try {
            file_put_contents(config_path() . "dayu.php",$conf);
        } catch (\Exception $e) {
            return vae_assign(202,'配置失败:'.$e->getMessage());
        }
        
        return vae_assign(200,'配置成功');
    }
}
