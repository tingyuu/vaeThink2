<?php
declare (strict_types = 1);

namespace app\admin\controller;
use app\admin\BaseController;
use think\facade\Db;
use think\facade\Cache;
use think\facade\View;
use think\facade\Session;
use think\facade\Config;

class Index extends BaseController
{
    /**
     * 后台首页
     * @Author   听雨
     * @DateTime 2019-11-21
     * @return   [type]
     */
    public function index()
    {
        View::assign('uInfo',Session::get('adminToken'));
        return View::fetch(); 
    }

    /**
     * 后台默认打开的控制台页面
     * @Author   听雨
     * @DateTime 2020-03-07
     * @return   [type]     [description]
     */
    public function console()
    {
        return View::fetch();
    }

    /**
     * 后台菜单接口
     * @Author   听雨
     * @DateTime 2020-03-07
     * @return   [type]     [description]
     */
    public function getAdminMenuList()
    {
        // Cache::delete('uRulesMenu'.$this->user['id']);
        if(!Cache::get('uRulesMenu'.$this->user['id']))
        {
            //用户所在权限组及所拥有的权限
            if($this->user['id'] == 1)
            {
                //id=1的管理员默认拥有所有权限
                $uRulesMenu = Db::name('admin_rule')->where('is_menu',1)->order('sort desc')->field('id,pid,title,src as href,font_family as fontFamily,icon')->select()->toArray();
            }
            else
            {
                $uGroupIds = explode(',',Db::name('admin')->where('id',$this->user['id'])->value('groups'));
                $uRuleIds  = Db::name('admin_group')->where('id','IN',$uGroupIds)->where('status',1)->column('rules','id');

                $uRules    = [];
                foreach ($uRuleIds as $k => $v) {
                  $uRules = array_keys(array_flip($uRules)+array_flip(explode(',',$v)));
                }

                //用户所拥有的所有菜单
                $uRulesMenu = Db::name('admin_rule')->where('id','in',$uRules)->where('is_menu',1)->order('sort desc')->field('id,pid,title,src as href,font_family as fontFamily,icon')->select()->toArray();
            }
            
            foreach ($uRulesMenu as $k => $v) {
                if(!empty($v['href'])) {
                    $uRulesMenu[$k]['href'] = (string)url($v['href']);
                }
            }

            // 在菜单列表的开头添加首页
            array_unshift($uRulesMenu,['id'=>-1,'pid'=>0,'title'=>'首页','href'=>'','fontFamily'=>'','icon'=>'layui-icon-home']);

            array_push($uRulesMenu,['id'=>-2,'pid'=>0,'title'=>'文档','href'=>'http://vaethink.com','fontFamily'=>'','icon'=>'layui-icon-read']);

            $uRulesMenu = vae_list_to_tree($uRulesMenu);
         
            Cache::tag('adminRulesSrc')->set('uRulesMenu'.$this->user['id'],$uRulesMenu,36000);
        }
        $uRulesMenu     = Cache::get('uRulesMenu'.$this->user['id']);
        
        return vae_assign(200,'',$uRulesMenu);
    }

    /**
     * 清空系统缓存
     * @Author   听雨
     * @DateTime 2020-03-09
     * @return   [type]     [description]
     */
    public function cacheClear()
    {
        try {
            Cache::clear();
        } catch (\Exception $e) {
            return vae_assign(202,$e->getMessage());
        }
        return vae_assign(200,'清除成功');
    }

    /**
     * 管理员退出登录
     * @Author   听雨
     * @DateTime 2020-03-07
     * @return   [type]     [description]
     */
    public function adminLogout()
    {
        try {
            Session::delete('adminToken');
        } catch (\Exception $e) {
            return vae_assign(202,$e->getMessage());
        }
        return vae_assign(200,'已为您注销登录,再会！',(string)url('admin/publicer/adminLoginPage'));
    }

    /**
     * 权限节点，用于添加和修改权限组
     * @Author   听雨
     * @DateTime 2020-03-11
     * @return   [type]     [description]
     */
    public function getRuleList()
    {
        if($this->request->isAjax())
        {
            $list = Db::name('admin_rule')
                    ->field('id,pid,title,is_menu')
                    ->select()
                    ->toArray();
            // 配合layui树形组件提供的额外数据
            foreach ($list as $k => $v) {
                // 默认全部展开
                // $list[$k]['spread'] = true;
                // 标注菜单和纯权限节点
                $v['is_menu'] == 1?$list[$k]['title'] = '<span class="layui-badge">权限</span> <span class="layui-badge layui-bg-blue">菜单</span> ' . $v['title']:$list[$k]['title'] = '<span class="layui-badge">权限</span> ' . $v['title'];

                // if(!empty($this->param['id'])){
                //     $sele = explode(',',Db::name('admin_group')->where('id',$this->param['id'])->value('rules'));
                //     if(in_array($v['id'], $sele)){
                //         // $list[$k]['checked'] = true;
                //     }else{
                //         $list[$k]['checked'] = false;
                //     }
                // }
            }
            return vae_assign(200,'',vae_list_to_tree($list));
        }
        else
        {
            return vae_assign(202,'非法请求');
        }
    }

    /**
     * 文件上传
     * @Author   听雨
     * @DateTime 2019-12-04
     * @return   [type]     [description]
     */
    public function upload(){
        // 获取表单上传文件 例如上传了001.jpg
        $param['image'] = request()->file('file');

        // dump($param['image']);die;

        $validate = \think\facade\Validate::rule([
            'image'  => 'require|fileSize:102400|fileExt:jpg,png,jpeg,gif',
        ]);

        if (!$validate->check($param)) {
            return vae_assign(202,$validate->getError());
        }
        // 

        $file = $param['image'];
        $savename = \think\facade\Filesystem::disk('public')->putFile( 'topic', $file);

        if($savename) {
            $path = Config::get('filesystem.disks.public.url');
            $data   = $path.'/'.$savename;
            return vae_assign(200,'上传成功',$data);
        }
        else
        {
            return vae_assign(202,'上传失败，请稍后再试');
        }
    }

    /**
     * 异常提示页
     * @Author   听雨
     * @DateTime 2020-03-12
     * @param    string     $msg [description]
     * @return   [type]          [description]
     */
    public function errorShow($msg = '你没有这个操作的权限呀~')
    {
        View::assign('msg',$msg);
        return View::fetch();
    }

    /**
     * 修改个人资料
     * @Author   听雨
     * @DateTime 2020-03-12
     * @return   [type]     [description]
     */
    public function editAdminInfo()
    {
        if($this->request->isPost())
        {
            $param = $this->param;

            $this->validate($param,[
                'nickname'       => 'require',
                'thumb'          => 'require',
            ]);

            if(!empty($param['password']))
            {
                //重置密码
                if(empty($param['password_confirm']) or $param['password_confirm'] !== $param['password']) {
                    return vae_assign(202,'两次密码不一致');
                }
                $param['salt'] = vae_set_salt(20);
                $param['pwd']  = vae_set_password($param['password'],$param['salt']);
            }
            else
            {
                unset($param['pwd']);
                unset($param['salt']);
            } 
            
            $param['update_time'] = time();
            $param['id']          = Session::get('adminToken')['id'];

            unset($param['groups']);
            
            if(false == Db::name('admin')->strict(false)->field(true)->update($param))
            {
                return vae_assign(202,'修改失败，请稍后再试');
            }

            return vae_assign(200,'修改成功,重新登录后生效');
        }
        else
        {
            View::assign('admin',Session::get('adminToken'));
            return View::fetch('admin@admin/edit_admin_info');
        }
    }
}
