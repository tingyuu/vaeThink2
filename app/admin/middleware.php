<?php

return [
	//开启session中间件
	'think\middleware\SessionInit',
	//验证vaeThink是否完成安装
	\app\admin\middleware\Install::class,
	//验证管理员操作权限
	\app\admin\middleware\Auth::class,
];
