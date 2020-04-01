<?php
declare (strict_types = 1);

namespace app\admin\controller;
use app\admin\BaseController;
use think\facade\Db;
use think\facade\View;

class Content extends BaseController
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
            
            $list = Db::name('content_group')
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
                'name'          => 'require|unique:content_group',
                'key'           => 'require|unique:content_group',
                'cate_group_id' => 'require',
            ]);
            
            if(false == Db::name('content_group')->strict(false)->field(true)->insert($param)) {
                return vae_assign(202,'创建失败，请稍后再试');
            }
            return vae_assign(200,'创建成功');
        } 
        else 
        {
            View::assign('cate_group',Db::name('cate_group')->select()->toArray());
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
                'id'            => 'require',
                'name'          => 'require|unique:content_group',
                'key'           => 'require|unique:content_group',
                'cate_group_id' => 'require',
            ]);
            
            if(false == Db::name('content_group')->strict(false)->field(true)->update($param)) {
                return vae_assign(202,'修改失败，请稍后再试');
            }

            return vae_assign(200,'修改成功');
        } 
        else 
        {
            $id = $this->param['id'];
            $data = Db::name('content_group')->find($id);
            empty($data)?vae_assign(202,'查询的数据不存在'):View::assign('data',$data);
            View::assign('cate_group',Db::name('cate_group')->select()->toArray());
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
        $count = Db::name('content')->where(["content_group_id" => $id])->count();
        if ($count > 0) {
            return vae_assign(202,"该分组下还存在内容，请先删除内容数据");
        }
        if (Db::name('content_group')->delete($id) == false) {
            return vae_assign(202,"删除失败！");
        }
        return vae_assign(200,"删除成功！");
    }

    public function contentIndex()
    {
        $param = $this->param;

        if($this->request->isAjax())
        {
            if(empty($param['content_group_id']))
            {
                return vae_assign(202,'非法请求');
            }
            
            $list = Db::name('content')
                    ->where('content_group_id',$param['content_group_id'])
                    ->order('sort create_time desc')
                    ->paginate([
                        'list_rows' => isset($param['limit']) ? $param['limit'] : 20,
                        'page'      => isset($param['page']) ? $param['page'] : 1,
                        'query'     => $param
                    ])
                    ->each(function($item, $key){
                        // $item['img']         = explode(',',$item['img']);
                        $item['create_time'] = date('Y-m-d',$item['create_time']);
                        $item['cate_name']   = Db::name('cate')->where('id',$item['cate_id'])->value('title');
                        return $item;
                    });
            return vae_table_assign(0,'',$list); 
        }
        else
        {
            View::assign('content_group_id',$param['content_group_id']);
            return View::fetch();
        }
    }

    public function addContent()
    {
        $param = $this->param;

        if($this->request->isPost()) 
        {
            $this->validate($param,[
                'title'            => 'require',
                'img'              => 'require',
                'content_group_id' => 'require',
            ]);

            // $param['img'] = implode(',', $param['img']);
            
            if(false == Db::name('content')->strict(false)->field(true)->insert($param)) {
                return vae_assign(202,'创建失败，请稍后再试');
            }
            return vae_assign(200,'创建成功');
        } 
        else 
        {
            View::assign('content_group_id',$param['content_group_id']);
            View::assign('cate',Db::name('cate')->where('cate_group_id',Db::name('content_group')->where('id',$param['content_group_id'])->value('cate_group_id'))->select()->toArray());
            return View::fetch();
        }
        
    }

    public function editContent()
    {
        if($this->request->isPost()) 
        {
            $param = $this->param;

            $this->validate($param,[
                'id'               => 'require',
                'title'            => 'require',
                'img'              => 'require',
            ]);
            
            if(false == Db::name('content')->strict(false)->field(true)->update($param)) {
                return vae_assign(202,'修改失败，请稍后再试');
            }

            return vae_assign(200,'修改成功');
        } 
        else 
        {
            $id = $this->param['id'];
            $data = Db::name('content')->find($id);
            empty($data)?vae_assign(202,'查询的数据不存在'):View::assign('data',$data);
            View::assign('cate',Db::name('cate')->where('cate_group_id',Db::name('content_group')->where('id',$data['content_group_id'])->value('cate_group_id'))->select()->toArray());
            return View::fetch();
        }
    }

    public function deleteContent()
    {
        $id    = $this->param["id"];
        if(!$id) {
            return vae_assign(202,'缺少必要条件');
        }
        if (Db::name('content')->delete($id) == false) {
            return vae_assign(202,"删除失败！");
        }
        return vae_assign(200,"删除成功！");
    }
}
