<?php
declare (strict_types = 1);

namespace app\admin\controller;
use app\admin\BaseController;
use think\facade\Db;
use think\facade\Cache;
use think\facade\View;

class Menu extends BaseController
{
    /**
     * 列表
     * @Author   听雨
     * @DateTime 2019-11-21
     * @return   [type]
     */
    public function index()
    {
        if($this->request->isAjax())
        {
            $where = [];
            if(!empty($param['keywords'])) {
                // dump($param['keywords']);die;
                $where[] = ['title','like',$this->param['keywords'].'%'];
            }
            $list = Db::name('admin_rule')
                    ->field('id,pid,title,is_menu')
                    ->where($where)
                    ->select()
                    ->toArray();
            // 配合layui树形组件提供的额外数据
            foreach ($list as $k => $v) {
                // 默认全部展开
                // $list[$k]['spread'] = true;
                // 标注菜单和纯权限节点
                $v['is_menu'] == 1?$list[$k]['title'] = '<span class="layui-badge">权限</span> <span class="layui-badge layui-bg-blue">菜单</span> ' . $v['title']:$list[$k]['title'] = '<span class="layui-badge">权限</span> ' . $v['title'];
            }
            return vae_assign(200,'',vae_list_to_tree($list)); 
        }
        else
        {
            return View::fetch();
        }
    }

    /**
     * 添加
     * @Author   听雨
     * @DateTime 2019-11-21
     */
    public function add()
    {
        if($this->request->isAjax()) {
            $param = $this->param;

            $this->validate($param,[
                'pid'      => 'require',
                'title'    => 'require',
            ]);

            $param['is_menu'] = empty($param['is_menu'])?0:$param['is_menu'];
                
            if(false == Db::name('admin_rule')->strict(false)->field(true)->insert($param)) {
                return vae_assign(202,'修改失败，请稍后再试');
            }
            //清除所有菜单缓存
            Cache::tag('adminRulesSrc')->clear();
            return vae_assign(200,'添加成功');
        } else {
            View::assign('menu',Db::name('admin_rule')->select()->toArray());
            return View::fetch();
        }
    }

    /**
     * 修改
     * @Author   听雨
     * @DateTime 2019-11-22
     * @return   [type]     [description]
     */
    public function edit()
    {
        if($this->request->isPost()) {
            $param = $this->param;

            $this->validate($param,[
                'id'       => 'require',
                'pid'      => 'require',
                'title'    => 'require',
            ]);

            $param['is_menu'] = empty($param['is_menu'])?0:$param['is_menu'];
                
            if(false == Db::name('admin_rule')->strict(false)->field(true)->update($param)) {
                return vae_assign(202,'修改失败，请稍后再试');
            }
            //清除所有菜单缓存
            Cache::tag('adminRulesSrc')->clear();
            return vae_assign(200,'修改成功');
        } else {
            $id = $this->param['id'];

            if(!$id) {
                return vae_assign(202,'缺少必要条件');
            }

            $data = Db::name('admin_rule')->field('id,pid,title,src,is_menu,font_family,icon,sort')->find($id);

            if(empty($data)) {
                return vae_assign(202,'查询的数据不存在');
            }
            View::assign('data',$data);
            View::assign('menu',Db::name('admin_rule')->select()->toArray());
            return View::fetch();
        }
        
    }

    /**
     * 删除
     * @Author   听雨
     * @DateTime 2019-11-22
     * @return   [type]     [description]
     */
    public function delete()
    {
        $id    = $this->param['id'];
        if(!$id) {
            return vae_assign(202,'缺少必要条件');
        }
        $count = Db::name('admin_rule')->where(["pid" => $id])->count();
        if ($count > 0) {
            return vae_assign(202,"该菜单下还存在子菜单，请先删除子菜单！");
        }
        if (Db::name('admin_rule')->delete($id) == false) {
            return vae_assign(202,"删除失败！");
        }
        return vae_assign(200,"删除成功！");
    }
}
