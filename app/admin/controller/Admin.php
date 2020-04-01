<?php
declare (strict_types = 1);

namespace app\admin\controller;
use app\admin\BaseController;
use think\facade\Db;
use think\facade\Cache;
use think\facade\View;
use think\facade\Session;

class Admin extends BaseController
{
    /**
     * 管理员列表
     * @Author   听雨
     * @DateTime 2019-11-21
     * @return   [type]
     */
    public function index()
    {
        if($this->request->isAjax())
        {
            $param = $this->param;
            $where = [];
            if(!empty($param['keywords'])) {
                $where[] = ['nickname|username|desc','like',$param['keywords'].'%'];
            }
            $list = Db::name('admin')
                    ->field('id,username,nickname,status,last_login_time,desc,thumb,groups,last_login_ip')
                    ->where($where)
                    ->paginate([
                        'list_rows' => isset($param['limit']) ? $param['limit'] : 20,
                        'page'      => isset($param['page']) ? $param['page'] : 1,
                        'query'     => $param
                    ])
                    ->each(function($item, $key){
                        $item['groupName']       = implode('|',Db::name('admin_group')->where('id','in',explode(',',$item['groups']))->column('title'));
                        $item['last_login_time'] = date('Y-m-d H:i:s',$item['last_login_time']);
                        return $item;
                    });
            return vae_table_assign(0,'',$list); 
        }
        else
        {
            return View::fetch();
        }
    }

    /**
     * 添加管理员
     * @Author   听雨
     * @DateTime 2019-11-21
     */
    public function add()
    {
        if($this->request->isPost()) 
        {
            $param = $this->param;

            $this->validate($param,[
                'username'       => 'require|unique:admin',
                'password'       => 'require|confirm',
                'nickname'       => 'require',
                'thumb'          => 'require',
                'group_id'       => 'require',
            ]);

            $param['salt']        = vae_set_salt(20);
            $param['pwd']         = vae_set_password($param['password'],$param['salt']);
            $param['groups']      = implode(',',$param['group_id']);
            $param['create_time'] = time();
            
            if(false == Db::name('admin')->strict(false)->field(true)->insert($param)) {
                return vae_assign(202,'创建失败，请稍后再试');
            }
            return vae_assign(200,'创建成功');
        } 
        else 
        {
            View::assign('group',Db::name('admin_group')->where('status',1)->select()->toArray());
            return View::fetch();
        }
        
    }

    /**
     * 修改管理员
     * @Author   听雨
     * @DateTime 2019-11-22
     * @return   [type]     [description]
     */
    public function edit()
    {
        if($this->request->isPost()) 
        {
            $param = $this->param;

            $this->validate($param,[
                'id'             => 'require',
                'nickname'       => 'require',
                'thumb'          => 'require',
                'group_id'       => 'require',
            ]);

            // 不允许其他管理员修改id=1的管理员
            if($param['id'] == 1 and Session::get('admin_tiken')['id'] !== 1)
            {
                return vae_assign(202,'不允许其他人修改系统所有者');
            }

            unset($param['username']);
            if(!empty($param['password'])) {
                //重置密码
                if(empty($param['password_confirm']) or $param['password_confirm'] !== $param['password']) {
                    return vae_assign(202,'两次密码不一致');
                }
                $param['salt'] = vae_set_salt(20);
                $param['pwd']  = vae_set_password($param['password'],$param['salt']);
            } else {
                unset($param['pwd']);
                unset($param['salt']);
            } 
            $param['groups']      = implode(',',$param['group_id']);
            $param['update_time'] = time();
            
            if(false == Db::name('admin')->strict(false)->field(true)->update($param)) {
                return vae_assign(202,'修改失败，请稍后再试');
            }
            Cache::delete('uRulesSrc'.$param['id']);

            return vae_assign(200,'修改成功');
        } 
        else 
        {
            $id = $this->param['id'];
            $data = Db::name('admin')->find($id);
            empty($data)?vae_assign(202,'查询的数据不存在'):$data['groups']=explode(',', $data['groups']);
            View::assign('data',$data);
            View::assign('group',Db::name('admin_group')->where('status',1)->select()->toArray());
            return View::fetch();
        }
    }

    /**
     * 删除管理员
     * @Author   听雨
     * @DateTime 2019-11-22
     * @return   [type]     [description]
     */
    public function delete()
    {
        $id    = $this->param["id"];
        if(!$id) {
            return vae_assign(202,'缺少必要条件');
        }
        if ($id == 1) {
            return vae_assign(202,"系统拥有者，无法删除！");
        }
        if (Db::name('admin')->delete($id) == false) {
            return vae_assign(202,"删除失败！");
        }
        return vae_assign(200,"删除管理员成功！");
    }
}
