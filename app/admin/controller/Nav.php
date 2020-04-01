<?php
declare (strict_types = 1);

namespace app\admin\controller;
use app\admin\BaseController;
use think\facade\Db;
use think\facade\View;

class Nav extends BaseController
{
    /**
     * 分组列表
     * @Author   听雨
     * @DateTime 2019-11-21
     * @return   [type]
     */
    public function index()
    {
        if($this->request->isAjax())
        {
            $param = $this->param;
            
            $list = Db::name('nav_group')
                    ->paginate([
                        'list_rows' => isset($param['limit']) ? $param['limit'] : 20,
                        'page'      => isset($param['page']) ? $param['page'] : 1,
                        'query'     => $param
                    ]);
            return vae_table_assign(0,'',$list); 
        }
        else
        {
            return View::fetch();
        }
    }

    /**
     * 添加导航组
     * @Author   听雨
     * @DateTime 2019-11-21
     */
    public function addGroup()
    {
        if($this->request->isPost()) 
        {
            $param = $this->param;

            $this->validate($param,[
                'name'      => 'require|unique:nav_group',
                'key'       => 'require|unique:nav_group',
            ]);
            
            if(false == Db::name('nav_group')->strict(false)->field(true)->insert($param)) {
                return vae_assign(202,'创建失败，请稍后再试');
            }
            return vae_assign(200,'创建成功');
        } 
        else 
        {
            return View::fetch();
        }
        
    }

    /**
     * 修改导航组
     * @Author   听雨
     * @DateTime 2019-11-22
     * @return   [type]     [description]
     */
    public function editGroup()
    {
        if($this->request->isPost()) 
        {
            $param = $this->param;

            $this->validate($param,[
                'id'        => 'require',
                'name'      => 'require|unique:nav_group',
                'key'       => 'require|unique:nav_group',
            ]);
            
            if(false == Db::name('nav_group')->strict(false)->field(true)->update($param)) {
                return vae_assign(202,'修改失败，请稍后再试');
            }

            return vae_assign(200,'修改成功');
        } 
        else 
        {
            $id = $this->param['id'];
            $data = Db::name('nav_group')->find($id);
            empty($data)?vae_assign(202,'查询的数据不存在'):View::assign('data',$data);
            return View::fetch();
        }
    }

    /**
     * 删除导航组
     * @Author   听雨
     * @DateTime 2019-11-22
     * @return   [type]     [description]
     */
    public function deleteGroup()
    {
        $id    = $this->param["id"];
        if(!$id) {
            return vae_assign(202,'缺少必要条件');
        }
        $count = Db::name('nav')->where(["nav_group_id" => $id])->count();
        if ($count > 0) {
            return vae_assign(202,"该分组下还存在导航，请先删除导航数据");
        }
        if (Db::name('nav_group')->delete($id) == false) {
            return vae_assign(202,"删除失败！");
        }
        return vae_assign(200,"删除成功！");
    }

    public function navIndex()
    {
        $param = $this->param;

        if($this->request->isAjax())
        {
            if(empty($param['nav_group_id']))
            {
                return vae_assign(202,'非法请求');
            }
            
            $list = Db::name('nav')
                    ->where('nav_group_id',$param['nav_group_id'])
                    ->order('sort desc')
                    ->paginate([
                        'list_rows' => isset($param['limit']) ? $param['limit'] : 20,
                        'page'      => isset($param['page']) ? $param['page'] : 1,
                        'query'     => $param
                    ]);
            return vae_table_assign(0,'',$list); 
        }
        else
        {
            View::assign('nav_group_id',$param['nav_group_id']);
            return View::fetch();
        }
    }

    public function addNav()
    {
        $param = $this->param;

        if($this->request->isPost()) 
        {
            $this->validate($param,[
                'title'        => 'require',
                'nav_group_id' => 'require',
            ]);
            
            if(false == Db::name('nav')->strict(false)->field(true)->insert($param)) {
                return vae_assign(202,'创建失败，请稍后再试');
            }
            return vae_assign(200,'创建成功');
        } 
        else 
        {
            View::assign('nav_group_id',$param['nav_group_id']);
            return View::fetch();
        }
        
    }

    public function editNav()
    {
        if($this->request->isPost()) 
        {
            $param = $this->param;

            $this->validate($param,[
                'id'           => 'require',
                'title'        => 'require',
            ]);
            
            if(false == Db::name('nav')->strict(false)->field(true)->update($param)) {
                return vae_assign(202,'修改失败，请稍后再试');
            }

            return vae_assign(200,'修改成功');
        } 
        else 
        {
            $id = $this->param['id'];
            $data = Db::name('nav')->find($id);
            empty($data)?vae_assign(202,'查询的数据不存在'):View::assign('data',$data);
            return View::fetch();
        }
    }

    public function deleteNav()
    {
        $id    = $this->param["id"];
        if(!$id) {
            return vae_assign(202,'缺少必要条件');
        }
        if (Db::name('nav')->delete($id) == false) {
            return vae_assign(202,"删除失败！");
        }
        return vae_assign(200,"删除成功！");
    }
}
