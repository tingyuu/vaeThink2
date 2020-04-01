<?php
declare (strict_types = 1);

namespace app\middleware;
use think\facade\Db;

class Route
{
    public function handle($request, \Closure $next)
    {
        // 检查是否完成安装
        if(file_exists(root_path() . 'install.lock'))
        {
            $module = app('http')->getName();
            if(!file_exists(root_path() . '/route/'.$module.'/app.php'))
            {
                $route = Db::name('route')->where('module',$module)->where('status',1)->count();
                if($route > 0)
                {
                    if (!file_exists(root_path() . '/route/'.$module))
                    {
                        mkdir (root_path() . '/route/'.$module,0777);
                    }
                    $route_str='
<?php
use think\facade\Route;
use think\facade\Db;
use think\facade\Cache;

$module = app("http")->getName();

if(Cache::has("route_".$module)) {
    $runtimeRoute = Cache::get("route".$module);
} else {
    $runtimeRoute = Db::name("route")->where("module",$module)->where("status", 1)->order("create_time asc")->column("full_url","url");
    Cache::set("route".$module,$runtimeRoute);
}

foreach ($runtimeRoute as $k => $v) {
    Route::rule($k,$v);
}';

                
                    // 创建应用路由配置文件
                    if(false == file_put_contents(root_path() . '/route/'.$module.'/app.php',$route_str)) {
                        return abort(404,'创建路由配置文件失败，请检查route目录的权限');
                    }
                }
            }
        }
        
        return $next($request);
    }
}
