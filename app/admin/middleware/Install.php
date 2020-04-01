<?php
declare (strict_types = 1);

namespace app\admin\middleware;

class Install
{
    public function handle($request, \Closure $next)
    {
        if(!file_exists(root_path() . 'install.lock'))
        {
            return $request->isAjax()?vae_assign(202,'请先完成系统安装引导'):redirect((string)url('/install/install/index'));
        }
        
        return $next($request);
    }
}
