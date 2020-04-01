<?php
declare (strict_types = 1);

namespace app\admin\middleware;
use think\facade\Db;
use think\facade\Cache;
use think\facade\Session;
use think\Ruquest;

class Auth
{
    public function handle($request, \Closure $next)
    {
        $pathInfo = explode('/',str_replace('.'.$request->ext(),'',$request->pathInfo()));

        if(count($pathInfo) !== 2)
        {
            // 由于中间件里不能正常使用$request->controller()获取用户访问的控制器及方法，因此只能通过pathInfo来获取url并进行截取，但这就要求用户访问时必须输入完整的url，不然就抛出异常
            return abort(202, '非法请求,请输入完整的url');
        }

        $controller = strtolower($pathInfo[0]);
        $action     = strtolower($pathInfo[1]);
        
        //验证用户登录
        if($controller !== 'publicer')
        {
            if(!Session::has('adminToken'))
            {
                return $request->isAjax()?vae_assign(404,'请先登录'):redirect((string)url('/admin/publicer/adminLoginPage'));
            }

            // 验证用户访问权限
            if($controller !== 'index')
            {
                if(!$this->checkAuth($controller,$action,Session::get('adminToken')['id']))
                {
                    return $request->isAjax()?vae_assign(202,'你没有权限！'):redirect((string)url('/admin/index/errorShow'));
                }
            }
        }
        
        return $next($request);
    }

    /**
     * 验证用户访问权限
     * @Author   听雨
     * @DateTime 2019-11-21
     * @param    string $controller 当前访问控制器
     * @param    string $action 当前访问方法
     * @param    string $uid 当前用户id
     * @return   [type]
     */
    protected function checkAuth($controller,$action,$uid)
    {
        // Cache::delete('uRulesSrc'.$uid);
        if($uid == 1)
        {
            // id=1的管理员默认拥有所有权限
            return true;
        }
        else
        {
            if(!Cache::get('uRulesSrc'.$uid))
            {

                //用户所在权限组及所拥有的权限
                $uGroupIds = explode(',',Db::name('admin')->where('id',$uid)->value('groups'));
                $uRuleIds  = Db::name('admin_group')->where('id','IN',$uGroupIds)->where('status',1)->column('rules','id');

                $uRules = [];
                foreach ($uRuleIds as $k => $v) {
                    $uRules = array_keys(array_flip($uRules)+array_flip(explode(',',$v)));
                }
                //用户所拥有的所有权限节点
                $uRulesSrc = Db::name('admin_rule')->where('id','in',$uRules)->column('src','id');
                Cache::tag('adminRulesSrc')->set('uRulesSrc'.$uid,$uRulesSrc,36000);
            }
            else
            {
                $uRulesSrc = Cache::get('uRulesSrc'.$uid);
            }

            if(!in_array((string)$controller.'/'.$action, $uRulesSrc))
            {
                return false;
            }
            
            return true;
        }
    }
}
