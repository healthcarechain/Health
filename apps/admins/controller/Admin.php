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

class Admin extends Common
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
    }

    public function index(){
        return $this->fetch('admin_list');
    }
    public function admin_lis(){
        $result = input();
        $page = isset($result['page']) ? $result['page'] : 1;
//        dump($page);die;
        $page_size = 7;
        $total = db('health_user')->count();
        $total_page = ceil($total/$page_size);
        $offset = ($page-1)*$page_size+1;
        if(array_key_exists('page',$result)){
            unset($result['page']);
        }
        $res = [];
        $res['data'] = Db::table('health_user')->limit($page_size)->page($page)->select();
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
    public function admin_lisd(){
        $role = Db::table('health_role')->select();
        return view('admin_add',['role'=>$role]);
    }
    public function admin_add(){
    $data = input();
    $return = [];
    if(!empty($data)){
        $user = Db::table('health_user')->where('username',$data['admin_user'])->find();
        if($user){
            //用户名已经存在
           $return['status']=1;
        }else{
            if(!empty($data['str'])){
                $admin_user = $data['admin_user'];
                $admin_pwd = md5($data['admin_pwd']);
                $admin_id = Db::table('health_user')->insertGetId([
                    'username'=>$admin_user,
                    'password'=>$admin_pwd,
                ]);
                $str = $data['str'];
                foreach($str as $k=>$v){
                    $info=Db::table("user_role")->insert(['user_id'=>$admin_id,'role_id'=>$v]);
                }
                if($info){
                    $return['status']=6;
                }
            }else{
                   //未选择权限
                 $return['status']=2;
               }
        }
    }else{
        //网络异常请求失败
         $return['status']=4;

    }
        return $return;die;
    }
    public function admin_jin(){
        $id=input('id');
        $data = Db::table('health_user')->where('id',$id)->find();
        if($data['user_status']==0){
            $result = Db::table('health_user')->where('id',$id)->update(['user_status'=>1]);
            $res['status']=1;
        }else{
            $result = Db::table('health_user')->where('id',$id)->update(['user_status'=>0]);
            $res['status']=0;
        }
        return $res;
    }
    /**修改管理员角色**/
    public function admin_role(){
     $id = input('id');
     if($id){
         $user = Db::table('health_user')->where('user_status',0)->where('id',$id)->find();
     }
     //取出所有角色
     $role = Db::table('health_role')->select();
     $arr = Db::table('user_role')->where('user_id',$id)->select();
     $role_id = array_column($arr,"role_id");
     return view('admin_update',
         ['role'=>$role,
          'user'=>$user,
          'role_id'=>$role_id,
          'admin_id'=>$id,
             ]);exit;
//     if(!empty($arr)){
//         /***
//         <pre>array(2) {
//         [0] =&gt; array(3) {
//         ["user_role_id"] =&gt; int(3)
//         ["user_id"] =&gt; int(43)
//         ["role_id"] =&gt; int(3)
//         }
//         [1] =&gt; array(3) {
//         ["user_role_id"] =&gt; int(4)
//         ["user_id"] =&gt; int(43)
//         ["role_id"] =&gt; int(4)
//         }
//         }
//          *
//          *
//          * <pre>array(4) {
//         [0] =&gt; array(14) {
//         ["id"] =&gt; int(41)
//         ["username"] =&gt; string(9) "rewqwewqq"
//         ["password"] =&gt; string(32) "e09d5d0d6ba8050294acae9f45c7cb5f"
//         ["user_tel"] =&gt; NULL
//         ["user_email"] =&gt; NULL
//         ["registered_time"] =&gt; NULL
//         ["user_ip"] =&gt; NULL
//         ["user_status"] =&gt; int(0)
//         ["user_role_id"] =&gt; int(1)
//         ["user_id"] =&gt; int(41)
//         ["role_id"] =&gt; int(1)
//         ["role_name"] =&gt; string(6) "院长"
//         ["role_desc"] =&gt; string(12) "管理所有"
//         ["role_status"] =&gt; int(0)
//         }
//         [1] =&gt; array(14) {
//         ["id"] =&gt; int(42)
//         ["username"] =&gt; string(7) "rewqrea"
//         ["password"] =&gt; string(32) "7d17ba2484b83edf906b858984d7f6cb"
//         ["user_tel"] =&gt; NULL
//         ["user_email"] =&gt; NULL
//         ["registered_time"] =&gt; NULL
//         ["user_ip"] =&gt; NULL
//         ["user_status"] =&gt; int(0)
//         ["user_role_id"] =&gt; int(2)
//         ["u  ser_id"] =&gt; int(42)
//         ["role_id"] =&gt; int(1)
//         ["role_name"] =&gt; string(6) "院长"
//         ["role_desc"] =&gt; string(12) "管理所有"
//         ["role_status"] =&gt; int(0)
//         }
//         [2] =&gt; array(14) {
//         ["id"] =&gt; int(43)
//         ["username"] =&gt; string(14) "zhangsanaaaaaa"
//         ["password"] =&gt; string(32) "dc483e80a7a0bd9ef71d8cf973673924"
//         ["user_tel"] =&gt; NULL
//         ["user_email"] =&gt; NULL
//         ["registered_time"] =&gt; NULL
//         ["user_ip"] =&gt; NULL
//         ["user_status"] =&gt; int(0)
//         ["user_role_id"] =&gt; int(3)
//         ["user_id"] =&gt; int(43)
//         ["role_id"] =&gt; int(3)
//         ["role_name"] =&gt; string(6) "科长"
//         ["role_desc"] =&gt; string(24) "管理副科长及以下"
//         ["role_status"] =&gt; int(0)
//         }
//         [3] =&gt; array(14) {
//         ["id"] =&gt; int(43)
//         ["username"] =&gt; string(14) "zhangsanaaaaaa"
//         ["password"] =&gt; string(32) "dc483e80a7a0bd9ef71d8cf973673924"
//         ["user_tel"] =&gt; NULL
//         ["user_email"] =&gt; NULL
//         ["registered_time"] =&gt; NULL
//         ["user_ip"] =&gt; NULL
//         ["user_status"] =&gt; int(0)
//         ["user_role_id"] =&gt; int(4)
//         ["user_id"] =&gt; int(43)
//         ["role_id"] =&gt; int(4)
//         ["role_name"] =&gt; string(9) "副科长"
//         ["role_desc"] =&gt; string(18) "管理专家主任"
//         ["role_status"] =&gt; int(0)
//         }
//         }
//         </pre>
//          */
////         $role_id = [];
////         foreach($arr as $k=>$v){
////             $role_id[] = $v['role_id'];
////
////         }
//        $role =  Db::table('health_user')->alias('h')
//         ->join('user_role ur','h.id=ur.user_id')
//         ->join('health_role r','r.role_id=ur.role_id')
//        ->select();
//         dump($role);
//     }else{
//         echo 0;
//     }
    }
    public function admin_update()
 {
  //接受数据
     $date = input();
//     dump($date);die;
     $result_status = [];
     $role_id = isset($_POST['str'])?$_POST['str']:[];
     $user_id = $date['admin_id'];
     $result_status = array();
     $NewUsername = Db::table('health_user')->where('username',$date['username'])->where('id','NEQ',$user_id)->find();
     if(!$NewUsername){
         $status = Db::table('health_user')->where('id',$user_id)->update(['username'=>$date['username']]);
         $role_list = Db::table('user_role')->where('user_id',$user_id)->select();
         $role_ids = [];
         foreach($role_list as $v)
         {

             $role_ids[]=$v['role_id'];
             if(!in_array($v['role_id'],$role_id))
             {
                 $tue = db('user_role')->where('role_id',$v['role_id'])->where('user_id',$user_id)->delete();
             }
         }
         foreach($role_id as $_item)
         {
             if(!in_array($_item,$role_ids))
             {
                 $sace = db('user_role')->insert(['role_id'=>$_item,'user_id'=>$user_id]);
             }
         }
         $result_status['status'] = 1;

     }else{
         $result_status['status']=3;
     }
     return $result_status;die;

     }




}