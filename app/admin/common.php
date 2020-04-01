<?php
// 这是系统自动生成的公共文件
use think\facade\Request;

//针对layui数据列表的返回数据方法
function vae_table_assign($code=0, $msg='', $data, $httpCode=200, $header = [], $options = []){
    $res['code'] = $code;
    $res['msg'] = $msg;
    if(is_object($data)) {
        $data = $data->toArray();
    }
    if(!empty($data['total'])){
        $res['count'] = $data['total'];
    } else {
        $res['count'] = 0;
    }
    $res['data'] = $data['data'];
    $response = \think\Response::create($res, "json",$httpCode, $header, $options);
    throw new \think\exception\HttpResponseException($response);
}

//接收参数方法，方便后期解密
function vae_input($name='')
{
	if($name) {
		$param = Request::param($name);
	}
	else
	{
		$param = Request::param();
	}
	return $param;
}

//随机字符串，默认长度10
function vae_set_salt($num = 10){
    $str = 'qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890';
    $salt = substr(str_shuffle($str), 10, $num);
    return $salt;
}

//递归排序
function vae_set_recursion($result,$pid=0,$format="L "){
    /*记录排序后的类别数组*/
    static $list=array();
 
    foreach ($result as $k => $v){
        if($v['pid']==$pid){
            if($pid!=0){
                $v['title']=' '.$format.$v['title'];
            }
            /*将该类别的数据放入list中*/
            $list[]=$v;
            vae_set_recursion($result,$v['id'],"  ".$format);
        }
    }
 
    return $list;
}

function vae_list_to_tree($list, $pk = 'id', $pid = 'pid', $child = 'children', $root = 0)
{
    // 创建Tree
    $tree = array();
    if (is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}

//vaeThink加密方式
function vae_set_password($pwd, $salt){
    return md5(md5($pwd.$salt).$salt);
}