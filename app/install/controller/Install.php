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
namespace app\install\controller;
use app\BaseController;
use think\facade\View;
use app\install\validate\Index;
use mysqli;

class Install extends BaseController
{
    public function _initialize()
    {
        parent::_initialize();

        static $vaeIsInstalled;
        if (empty($vaeIsInstalled)) {
            $vaeIsInstalled = file_exists(root_path() . 'install.lock');

            if ($vaeIsInstalled) {
                return json('你已经安装过该系统!');
            }
        }
    }

    public function index()
    {
        return View::fetch('step1');
    }

    public function step2()
    {
        if(!$this->request->param('accept') or $this->request->param('accept') !== 1)
        {
            json(['code'=>0,'请先接受上一步的条款']);
        }
        $data = [
            'pdo' => class_exists('pdo')?1:0,
            'pdo_mysql' => extension_loaded('pdo_mysql')?1:0,
            'curl' => extension_loaded('curl')?1:0,
            'upload_size' => ini_get('file_uploads')?ini_get('upload_max_filesize'):0,
            'session' => function_exists('session_start')?1:0
        ];

        return View::fetch('',['data'=>$data]);
    }
    
    
    public function step3()
    {
        if(!$this->request->param('accept') or $this->request->param('accept') !== 2)
        {
            json(['code'=>0,'请先通过上一步的安装环境监测']);
        }
        return View::fetch();
    }

    public function createData()
    {
        if($this->request->isPost()){
            $data = $this->request->post();
            try {
                validate(Index::class)->check($data);
            } catch (ValidateException $e) {
                // 验证失败 输出错误信息
                return json(['code'=>0,'msg'=>$e->getMessage()]);
            }

            // 连接数据库
            $link=@new mysqli("{$data['DB_HOST']}:{$data['DB_PORT']}",$data['DB_USER'],$data['DB_PWD']);
            // 获取错误信息
            $error=$link->connect_error;
            if (!is_null($error)) {
                // 转义防止和alert中的引号冲突
                $error=addslashes($error);
                return json(['code'=>0,'msg'=>'数据库链接失败:'.$error]);die;
            }
            // 设置字符集
            $link->query("SET NAMES 'utf8'");
            if($link->server_info < 5.0){
                return json(['code'=>0,'msg'=>'请将您的mysql升级到5.0以上']);die;
            }
            // 创建数据库并选中
            if(!$link->select_db($data['DB_NAME'])){
                $create_sql='CREATE DATABASE IF NOT EXISTS '.$data['DB_NAME'].' DEFAULT CHARACTER SET utf8;';
                if(!$link->query($create_sql)){
                    return json(['code'=>0,'msg'=>'数据库连接失败']);die;
                }
                $link->select_db($data['DB_NAME']);
            }
            // 导入sql数据并创建表
            $vaethink_sql=file_get_contents(app_path() . 'data/vaethink.sql');
            $sql_array=preg_split("/;[\r\n]+/", str_replace("vae_",$data['DB_PREFIX'],$vaethink_sql));
            foreach ($sql_array as $k => $v) {
                if (!empty($v)) {
                    $link->query($v);
                }
            }

            //插入管理员
            $username    = $data['username'];
            $password    = $data['password'];
            $nickname    = 'Admin';
            $thumb       = '/static/admin_static/images/vae.jpg';
            $salt       = substr(str_shuffle('qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890'), 10, 20);
            $password    = md5(md5($password.$salt).$salt);
            $create_time = time();
            $update_time = time();

            $caeate_admin_sql = "INSERT INTO ".$data['DB_PREFIX']."admin ".
            "(username,pwd, nickname,thumb,salt,create_time,update_time) "
            ."VALUES "
            ."('$username','$password','$nickname','$thumb','$salt','$create_time','$update_time')";
            if(!$link->query($caeate_admin_sql)) {
                return json(['code'=>0,'msg'=>'创建管理员信息失败']);
            }
            $link->close();
            $db_str="
<?php

return [

    // 默认使用的数据库连接配置
    'default'         => 'mysql',

    // 自定义时间查询规则
    'time_query_rule' => [],

    // 自动写入时间戳字段
    // true为自动识别类型 false关闭
    // 字符串则明确指定时间字段类型 支持 int timestamp datetime date
    'auto_timestamp'  => true,

    // 时间字段取出后的默认时间格式
    'datetime_format' => 'Y-m-d H:i:s',

    // 数据库连接配置信息
    'connections'     => [
        'mysql' => [
            // 数据库类型
            'type'        => 'mysql',
            // 服务器地址
            'hostname'           =>  '{$data['DB_HOST']}',
            // 数据库名
            'database'           =>  '{$data['DB_NAME']}', 
            // 用户名
            'username'           =>  '{$data['DB_USER']}',
            // 密码
            'password'           =>  '{$data['DB_PWD']}',
            // 端口
            'hostport'           =>  '{$data['DB_PORT']}',
            // 数据库表前缀
            'prefix'             =>  '{$data['DB_PREFIX']}', 
            // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
            'deploy'            => 0,
            // 数据库读写是否分离 主从式有效
            'rw_separate'       => false,
            // 读写分离后 主服务器数量
            'master_num'        => 1,
            // 指定从服务器序号
            'slave_no'          => '',
            // 是否严格检查字段是否存在
            'fields_strict'     => true,
            // 是否需要断线重连
            'break_reconnect'   => false,
            // 监听SQL
            'trigger_sql'       => true,
            // 开启字段缓存
            'fields_cache'      => false,
            // 字段缓存路径
            'schema_cache_path' => app()->getRuntimePath() . 'schema' . DIRECTORY_SEPARATOR,
        ],

        // 更多的数据库配置信息
    ]
];";

                
            // 创建数据库配置文件
            if(false == file_put_contents(root_path() . "config/database.php",$db_str)) {
                return json(['code'=>0,'msg'=>'创建数据库配置文件失败，请检查目录权限']);
            }
            if(false == file_put_contents(root_path() . "install.lock",'vaeThink安装鉴定文件，勿删！！！！！此次安装时间：'.date('Y-m-d H:i:s',time()))) {
                return josn(['code'=>0,'msg'=>'创建安装鉴定文件失败，请检查目录权限']);
            }
            
            return json(['code'=>1,'msg'=>'安装成功']);
        }
    }  
}

