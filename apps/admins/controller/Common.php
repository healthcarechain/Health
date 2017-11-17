<?php
namespace app\admins\Controller;
use think\Controller;
use think\Db;
use think\session;
class Common extends Controller
{
    //定义不需要权限的路由
    protected $allowAllAction = [
        'index_admin',
        'personal',
    ];
    //防止非法登录
    public function __construct()
    {
        parent::__Construct();
        session_start();
        $admin_id = @$_SESSION['admin_id'];
//        echo $admin_id;die;
        if(empty($_SESSION['admin_id']))
        {
            if(empty(@$_COOKIE['name']) || empty(@$_COOKIE['pwd']))
            {//如果session为空，并且用户没有选择记录登录状
                echo "<script>alert('未登录,请返回用户中心');location.href='/';</script>";
            }
            else
             {
                //用户选择了记住登录状态
                $user = $_COOKIE['name'];
                $pwd = $_COOKIE['password'];
                $UserInfo = Db::table('health_user')->where('username',$user)->where('password',$pwd)->find();
                if (empty($UserInfo)){
                    echo "<script>alert('用户名密码错误，请重新登录');location.href='/';</script>";
                }
                else
                {
                    $_SESSION['admin_id'] = $UserInfo['id'];//用户名和密码对了，把用户的个人资料放到session里面
                }
                echo "<script>alert('未登录,请返回用户中心');location.href='/';</script>";
            }
        }

        //获取当前的URL路由
    $url = array_keys($_REQUEST)[0];
    $NowUrl =  substr($url,'1');
    $response = $this->autoPrivilege($admin_id);

//    dump($NowUrl);
//    dump($this->allowAllAction);die;

        //超级管理员
        if ($admin_id == 1)
        {
            return true;
        }
//        dump($NowUrl);
//     dump($response);
     if(in_array($NowUrl,$response))
     {
         return true;
     }
     else
     {
         if(in_array($NowUrl,$this->allowAllAction))
         {
            return true;
         }else{
          echo "您还没有权限，请联系管理员开通";die;
         }

     }
    }

    //当前登录的用户
    protected $current_user = null;
    protected $auth_cookie_name = null;
    protected $privilege_url = [];

   /*
    * 判断权限逻辑
    * 取出当前登录用户的所属角色
    * 再通过角色去获取其的权限关系
    * 在权限列表中取出所有权限链接
    * 判断当前访问的权限链接 是否在权限列表中
    * ***/
   //1.获取某用户的所有权限
   //2.获取指定用户的所属角色

   public function getRolePrivilege($user_id)
   {
       if(!$user_id && $this->current_user)
       {
           $user_id = $this->current_user->id;
       }
       $privilege_url = [];
       //取出当前登录用户所有角色
       $role_id = Db::name('user_role')->where('user_id',$user_id)->select('role_id')->column();
       if($role_id)
       {
           $node_ids = Db::table('role_node')->where('role_id',$role_id)->select('node_id')->column();
           $_list = Db::table('health_node')->where('node_id',$node_ids)->select();
           if($_list)
           {
               foreach($_list as $key=>$value)
               {
                   $tmp_urls = @json_decode($value['node_url'],true);
                   $this->privilege_url = array_merge($privilege_url,$tmp_urls);
               }
           }
       }
//       return $privilege_url;
   }
   public function autoPrivilege($admin_id)
   {
       $response = DB::table('user_role')
           ->alias(['user_role'=>'ur','health_role'=>'hr','role_node'=>'rn','health_node'=>'hn'])
           ->join("health_role",'ur.role_id = hr.role_id')
           ->join("role_node",'hr.role_id = rn.role_id')
           ->join("health_node",'rn.node_id = hn.node_id')
           ->where("ur.user_id",'EQ',$admin_id)
           ->select();
//       dump($response);die;
       foreach($response as $_item)
       {
           $result = @json_decode($_item['node_url'],true);
           $this->privilege_url = array_merge($this->privilege_url,$result);
//           dump($this->privilege_url);die;
//dump($Is_url);die;
//           if(in_array($Is_url,$this->privilege_url,TRUE))
//           {
//               return true;
//           }
       }
       return $this->privilege_url;

   }

}