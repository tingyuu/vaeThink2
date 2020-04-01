<?php
declare (strict_types = 1);

namespace app\admin\controller;
use app\admin\BaseController;
use think\facade\Db;
use think\facade\View;

class Cate extends BaseController
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
            
            $list = Db::name('cate_group')
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
                'name'      => 'require|unique:cate_group',
                'key'       => 'require|unique:cate_group',
            ]);
            
            if(false == Db::name('cate_group')->strict(false)->field(true)->insert($param)) {
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
                'name'      => 'require|unique:cate_group',
                'key'       => 'require|unique:cate_group',
            ]);
            
            if(false == Db::name('cate_group')->strict(false)->field(true)->update($param)) {
                return vae_assign(202,'修改失败，请稍后再试');
            }

            return vae_assign(200,'修改成功');
        } 
        else 
        {
            $id = $this->param['id'];
            $data = Db::name('cate_group')->find($id);
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
        $count = Db::name('cate')->where(["cate_group_id" => $id])->count();
        if ($count > 0) {
            return vae_assign(202,"该分组下还存在分类，请先删除分类数据");
        }
        if (Db::name('cate_group')->delete($id) == false) {
            return vae_assign(202,"删除失败！");
        }
        return vae_assign(200,"删除成功！");
    }

    public function cateIndex()
    {
        $param = $this->param;

        if($this->request->isAjax())
        {
            if(empty($param['cate_group_id']))
            {
                return vae_assign(202,'非法请求');
            }
            
            $list = Db::name('cate')
                    ->where('cate_group_id',$param['cate_group_id'])
                    ->order('sort desc')
                    ->select()
                    ->toArray();
            return vae_assign(0,'',vae_set_recursion($list)); 
        }
        else
        {
            View::assign('cate_group_id',$param['cate_group_id']);
            return View::fetch();
        }
    }

    public function addCate()
    {
        $param = $this->param;

        if($this->request->isPost()) 
        {
            $this->validate($param,[
                'title'         => 'require',
                'cate_group_id' => 'require',
                'pid'           => 'require'
            ]);
            
            if(false == Db::name('cate')->strict(false)->field(true)->insert($param)) {
                return vae_assign(202,'创建失败，请稍后再试');
            }
            return vae_assign(200,'创建成功');
        } 
        else 
        {
            View::assign('cate_group_id',$param['cate_group_id']);
            View::assign('cate',Db::name('cate')->where('cate_group_id',$param['cate_group_id'])->select()->toArray());
            return View::fetch();
        }
        
    }

    public function editCate()
    {
        if($this->request->isPost()) 
        {
            $param = $this->param;

            $this->validate($param,[
                'id'           => 'require',
                'title'        => 'require',
                'pid'          => 'require'
            ]);
            
            if(false == Db::name('cate')->strict(false)->field(true)->update($param)) {
                return vae_assign(202,'修改失败，请稍后再试');
            }

            return vae_assign(200,'修改成功');
        } 
        else 
        {
            $id = $this->param['id'];
            $data = Db::name('cate')->find($id);
            empty($data)?vae_assign(202,'查询的数据不存在'):View::assign('data',$data);
            View::assign('cate',Db::name('cate')->where('cate_group_id',$data['cate_group_id'])->select()->toArray());
            return View::fetch();
        }
    }

    public function deleteCate()
    {
        $id    = $this->param["id"];
        if(!$id) {
            return vae_assign(202,'缺少必要条件');
        }
        if(Db::name('cate')->where('pid',$id)->count() > 0) {
            return vae_assign(202,'删除失败,该分类下还存在子分类');
        }
        if (Db::name('cate')->delete($id) == false) {
            return vae_assign(202,"删除失败！");
        }
        return vae_assign(200,"删除成功！");
    }
}
