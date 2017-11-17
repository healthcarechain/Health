<?php
/**
 * Created by PhpStorm.
 * User: 54646
 * Date: 2017/9/14
 * Time: 9:51
 */
//                   _ooOoo_
//                  o8888888o
//                  88" . "88
//                  (| -_- |)
//                  O\  =  /O
//               ____/`---'\____
//             .'  \\|     |//  `.
//            /  \\|||  :  |||//  \
//           /  _||||| -:- |||||-  \
//           |   | \\\  -  /// |   |
//           | \_|  ''\---/''  |   |
//           \  .-\__  `-`  ___/-. /
//         ___`. .'  /--.--\  `. . __
//      ."" '<  `.___\_<|>_/___.'  >'"".
//     | | :  `- \`.;`\ _ /`;.`/ - ` : | |
//     \  \ `-.   \_ __\ /__ _/   .-` /  /
//======`-.____`-.___\_____/___.-`____.-'======
//                   `=---='
//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
//         佛祖保佑       永无BUG
namespace app\admins\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Query;
use think\View;
class Role extends Common {
    public function index(){
        $arr = Db::table('health_node')->select();
        $data = $this->any_node($arr);
//        dump($data);die;
        return view('role_add',['data'=>$data]);
    }
    /**
    *封装权限的无限极分类
     * $arr 传值的数组
     *  $parent_id 父节点id 从0开始
     * $lev 等级从1开始
     */
  public function any_node($arr,$parent_id=0,$lev=1)
  {
      static $data = [];
      foreach($arr as $k=>$v){
          if($v['node_fid']==$parent_id)
          {
              $flg = str_repeat("_",$lev);
              $v['flg']=$flg;
              $v['lev']=$lev;
              $data[]=$v;
              $this->any_node($arr,$v['node_id'],$lev+1);
          }
      }
      return $data;
  }
  public function role_add(){
      $date = input();
      $res = array();
      $name = $date['role_name'];
      $OnlyName = Db::table('health_role')->where('role_name',$name)->find();
      if(!empty($OnlyName))
      {
          //角色已经存在
          $res['status']=1;
      }
      else
      {
          $node_id = isset($date['node_id'])?$date['node_id']:[];
          if(!empty($node_id))
          {
              $role_id = Db::table('health_role')->insertGetId(['role_name'=>$name,'role_desc'=>$date['role_desc']]);
              if($role_id)
              {
                  foreach($node_id as $_item)
                  {
                      $status = Db::table('role_node')->insert(['role_id'=>$role_id,'node_id'=>$_item]);
                  }
                  if($status)
                  {
                      //添加成功
                      $res['status']=0;
                  }
                  else
                  {
                      //添加失败
                      $res['status']=3;
                  }
              }
              else
              {
                  //用户名添加失败
                  $res['status']=4;
              }
          }
          else
          {
              //没选择权限
              $res['status']=2;
          }

      }
      return $res;exit;

  }
  public function role_list()
  {
   return view('role_list');
  }
  public function role_lis()
  {
      $result = input();
      $page = isset($result['page']) ? $result['page'] : 1;
      $page_size = 5;
      $total = db('health_role')->count();
      $total_page = ceil($total/$page_size);
      $offset = ($page-1)*$page_size+1;
      if(array_key_exists('page',$result)){
          unset($result['page']);
      }
      $res = [];
      $res['data'] = Db::table('health_role')->limit($page_size)->page($page)->select();
//        dump($res);die;
      if(!empty($res)){
          $params = array(
              'total_rows'=>$total,
              'method'=>'ajax',
              'now_page'=>$page,
              'ajax_func_name'=>'page',
              'list_rows'=>$page_size,
              'parameter'=>http_build_query($result),
          );
          $pags = new \test\Page($params);
//            var_dump($pags);die;
          $res['page']=$pags->show(3);
          $res['status']=1;
      }else{
          $res['status']=0;
      }
      return $res;

  }
  public function role_del(){
      $role_id = input('id');
      $result = Db::table('health_role')->where('role_id',$role_id)->delete();
      if($result){
          echo 1;
      }else{
          echo 0;
      }

  }
  public function role_update()
  {
      $id = input('id');
      $roleStatus = Db::table('health_role')->where('role_id',$id)->where('role_status',0)->find();
      if($roleStatus)
      {
          $Node = Db::table('health_node')->where('node_status',0)->select();
          $roleNode = Db::table('role_node')->where('role_id',$id)->select();
          $role_node = array_column($roleNode,'node_id');
//          dump($role_node);die;
          return view('role_update',[
              'role_id'=>$id,
              'roleStatus'=>$roleStatus,
              'node'=>$Node,
              'roleNode'=>$roleNode,
              'role_node'=>$role_node,
          ]);
      }


  }
  public function role_up()
  {
    $data = input();
    $role_name = $data['role_name'];
    $role_id = $data['role_id'];
    $role_desc = $data['role_desc'];
    $node = isset($data['node'])?$data['node']:[];//B
    $OnlyName = Db::table('health_role')->where('role_name',$role_name)->where('role_id','NEQ',$role_id)->find();
    $res = array();
    if(!$OnlyName)
    {
        $UpRole = Db::table('health_role')->where('role_id',$role_id)->update(['role_name'=>$role_name,'role_desc'=>$role_desc]);
        $NodeList = Db::table('role_node')->where('role_id',$role_id)->select();//A
        $Nods = [];
        foreach($NodeList as $v)
        {
            $Nods[]=$v['node_id'];
            if(!in_array($v['node_id'],$node))
            {
                Db::table('role_node')->where('node_id',$v['node_id'])->where('role_id',$role_id)->delete();
            }
        }
        foreach($node as $_item)
        {
            if(!in_array($_item,$Nods))
            {
                Db::table('role_node')->insert(['role_id'=>$role_id,'node_id'=>$_item]);
            }
        }
        //修改角色成功
        $res['status']=1;

    }
    else
    {
        //用户名已经存在
        $res['status']=0;
    }
    return $res;die;

  }
}