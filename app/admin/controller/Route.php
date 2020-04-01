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
use think\facade\Db;
use think\facade\View;
use think\facade\Cache;

class Route extends BaseController
{
    /**
     * 链接美化首页
     * @Author   听雨
     * @DateTime 2020-03-19
     * @return   [type]     [description]
     */
    public function index()
    {
        if($this->request->isAjax())
        {
            $param = $this->param;
            $where = array();
            if(!empty($param['keywords'])) {
                $where[] = ['id|full_url|url','like',$param['keywords'].'%'];
            }
            $route = Db::name('route')
                    ->order('create_time asc')
                    ->paginate([
                        'list_rows' => isset($param['limit']) ? $param['limit'] : 20,
                        'page'      => isset($param['page']) ? $param['page'] : 1,
                        'query'     => $param
                    ]);
            return vae_table_assign(0,'',$route);
        }
        else
        {
            return View::fetch();
        }
    }

    /**
     * 添加链接美化
     * @Author   听雨
     * @DateTime 2020-03-19
     */
    public function add()
    {
    	if($this->request->isPost())
        {
            $param = $this->param;
            $this->validate($param,[
                'module'         => 'require',
                'full_url'       => 'require|unique:route',
                'url'            => 'require|unique:route',
            ]);
            $param['create_time'] = time();
            
            if(false == Db::name('route')->strict(false)->field(true)->insert($param))
            {
                return vae_assign(202,'创建失败，请稍后再试');
            }
            Cache::delete('route_'.$param['module']);
            return vae_assign(200,'创建成功');
        }
        else
        {
            return View::fetch();
        }
    }

    /**
     * 修改链接美化
     * @Author   听雨
     * @DateTime 2020-03-19
     * @return   [type]     [description]
     */
    public function edit()
    {
        $param = $this->param;

        if($this->request->isPost())
        {
            $this->validate($param,[
                'module'         => 'require',
                'full_url'       => 'require|unique:route',
                'url'            => 'require|unique:route',
                'status'         => 'require',
                'id'             => 'require',
            ]);

            $param['update_time'] = time();
            
            if(false == Db::name('route')->strict(false)->field(true)->update($param))
            {
                return vae_assign(202,'修改失败，请稍后再试');
            }
            Cache::delete('route_'.$param['module']);
            return vae_assign(200,'修改成功');
        }
        else
        {
            $data = Db::name('route')->find($param['id']);
            empty($data)?vae_assign('202','查询的数据不存在'):View::assign('data',$data);
            return View::fetch();
        }
    }

    /**
     * 删除链接美化
     * @Author   听雨
     * @DateTime 2020-03-19
     * @return   [type]     [description]
     */
    public function delete()
    {
        $param    = $this->param;
        if (Db::name('Route')->delete($param['id']) !== false) {
            Cache::delete('route_'.$param['module']);
            return vae_assign(200,"删除成功！");
        } else {
            return vae_assign(202,"删除失败！");
        }
    }
}
