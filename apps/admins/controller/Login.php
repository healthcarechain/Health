<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/4 0004
 * Time: 下午 13:50
 */
namespace app\admins\controller;
//namespace Ali;
use think\cache\driver\Redis;
use think\Controller;
use think\Db;
use think\Request;
use think\Query;
use think\View;
//use think\Exception;
//use Ali\top\TopClient;
use Vendor\Alidayu\TopClient;
use Vendor\Alidayu\AlibabaAliqinFcSmsNumSendRequest;
use Vendor\Alidayu\ResultSet;
use Vendor\Alidayu\RequestCheckUtil;
use Vendor\Alidayu\TopLogger;
use think\cache\Driver;
class Login extends Controller{

    public function index()
    {
            /***
             *七天免登录
             */
            $username = @$_COOKIE['user'];
            $password = @$_COOKIE['pwd'];
            if(!empty($username&&$password))
            {

                $Info = Db::table('health_user')->where('username',$username)->where('password',$password)->find();
                if($Info)
                {
                    session_start();
                    $_SESSION['admin_id'] = $Info['id'];
                    return view('mail/mail');
                }
                else
                {
                    setcookie('user','',0);
                    setcookie('pwd','',0);
                    echo "<script>alert('登陆已过期，请重新登陆');location.href='/';</script>";
                }
            }
            else
            {
            $view = new view();
            return $view->fetch('login');
            }

    }
    //登陆
    public function add()
    {
        $userName = $_POST['username'];
        $passWord = md5($_POST['password']);
        $remember = isset($_POST['remember'])?$_POST['remember']:'';
        $info = Db::table('health_user')->where("username",$userName)->find();
//        dump($info);
        if($info)
        {
            $PassInfo = Db::table('health_user')->where("password",$passWord)->find();
//            dump($passWord);die;
            if($PassInfo)
            {
                $info_id= Db::table('health_user')->where('username',$userName)->where("user_status",1)->find();
//                dump($res);die;
                if($info_id)
                {
                   // 开启session 将信息存入session中
                    session_start();
                    $_SESSION['admin_id'] = $info_id['id'];
                    if(!empty($remember))
                    {
                        setcookie('user',$userName,time()+7*24*3600);
                        setcookie('pwd',$passWord,time()+7*24*3600);
                    }
                    $data = array("status"=>0);
                }
                else
                {
                    $data = array('status'=>3,'error'=>'账户异常,请联系管理员');
                }

            }
            else
            {
                $data = array("status"=>1,'error'=>'密码错误');
            }
        }
        else
        {
            $data = array("status"=>2,'error'=>'用户名错误');
        }
        return $data;die;
    }
    //退出登陆
    public function logout()
    {
        session_start();
        $_SESSION = array(); 					//清除SESSION值.
        if(isset($_COOKIE[session_name()]))		//判断客户端的cookie文件是否存在,存在的话将其设置为过期.
        {
            setcookie(session_name(),'',time()-1,'/');
        }
        setcookie('user','',0);
        setcookie('pwd','',0);
        session_destroy();  					//清除服务器的sesion文件
        return redirect("/");
    }
    /**注册模块**/
    public function login(){
        //注册页面
        return view('login_add');

    }
    //注册成功
    public function success_index()
    {
        session_start();
        $user_id=$_SESSION['admin_id'];
        $user_name=$_SESSION['user_name'];
        return view("login_list",['username'=>$user_name,'user_id'=>$user_id]);
    }

    /*
        验证用户名唯一性
    */
    public function User_only()
    {
        $username = $_GET['verifyVal'];
//        dump($username);die;
        $res = DB::table("health_user")->where('username',$username)->find();
        if($res)
        {
            $error['msg']="用户名已存在";
            exit(json_encode($error));
        }
        else
        {
            $error['msg']=1;
            exit(json_encode($error));
        }
    }
    //验证邮箱唯一性
    public function Email_only(){
        $email = $_POST['email'];
        $result = Db::table('health_user')->where("user_email",$email)->find();
        if($result){
            $error['data']="邮箱已经注册，请更换邮箱";
            exit(json_encode($error));
        }
        else
        {
            $error['data']=1;
            exit(json_encode($error));
        }
    }

    /*
           验证手机唯一性,以及发送验证码
            msg  1;手机唯一性
            msg  3,验证码过1分钟才能发
            msg  2.成功
    */
    public function Tel_only()
    {
//        $c = new TopClient;
        $tel = $_REQUEST['phone'];
//        echo $tel;die;
        $res = DB::table("health_user")->where('user_tel',$tel)->find();
        if($res)
        {
            $error['msg']="1";
            exit(json_encode($error));
        }
//        else
//        {
//            if(file_exists("admin/homes/code/$tel.txt") && time() - filemtime("admin/homes/code/$tel.txt")  < 60)
//            {
//                $error['msg']=3;
//                exit(json_encode($error));
//            }
//            else if(file_exists("admin/homes/code/$tel.txt"))
//            {
//
//                unlink("admin/homes/code/$tel.txt");
//            }
//
//            $rand = rand(100000,999999);
//            $file_path="admin/homes/code/$tel.txt";
//            file_put_contents($file_path,$rand,FILE_APPEND);
//            $c = new TopClient;
//            $c ->appkey = "24496818" ;
//            $c ->secretKey = "e10a78f229b0b540d787d88cd53eae2f" ;
//            $req = new AlibabaAliqinFcSmsNumSendRequest;
////            dump($req);die;
//            $req ->setExtend( "" );
//            $req ->setSmsType( "normal" );
//            $req ->setSmsFreeSignName( "" );
//            $req ->setSmsParam( "{number:'$rand'}" );
//            $req ->setRecNum( "$tel" );
//            $req ->setSmsTemplateCode( "SMS_72645024" );
//            if( $c ->execute( $req ))
//            {
//                $error['msg']=2;
//                exit(json_encode($error));
//            }
//        }
    }

    /*
          验证手机验证码
          msg 1:验证成功
          msg 2:验证码输入错误
          msg 3:验证码已失效
   */
//    public function Tel_code()
//    {
//        $verifyCode =  input('verifyCode');
//        $tel =  input('phone');
//        $TheFile = "admin/homes/code/$tel.txt";
////        dump($verifyCode);die;
//        if(file_exists("$TheFile"))
//        {
//            if ( time() - filemtime( $TheFile)  > 300) //5分钟300秒，时间可以自己调整
//            {
//                unlink("$TheFile");
//                $error['msg']=3;
//                exit(json_encode($error));
//            }
//            else
//            {
//                $code = file_get_contents("$TheFile");
//                if($verifyCode==$code)
//                {
////                    unlink("$TheFile");
//                    $error['msg']=1;
//                    exit(json_encode($error));
//                }
//                else
//                {
//                    $error['msg']=2;
//                    exit(json_encode($error));
//                }
//            }
//        }
//        else
//        {
//            $error['msg']=2;
//            exit(json_encode($error));
//        }
//    }

    /*
      * 注册入库
      */
    public function User_add()
    {
        $data = $_POST;
//        dump($data);die;
        //用户名密码
        $data['user_pwd'] = md5($data['user_pwd']);
        $data['registered_time'] = date("Y-m-d H:m:s");
        $token = md5( $data['user_pwd']."finance".$data['user_account']);
        $login_time = date("Y-m-d H:i:s",time());
        $login_ip = $_SERVER['REMOTE_ADDR'];
        $arr = DB::table("health_user")->where('username',$data['user_account'])->find();
        if($arr)
        {
            $error['msg']=2;

        }
        else
        {
            $email = Db::table('health_user')->where('user_email',$data['user_email'])->find();
            if($email){
               $error['msg']=6;

            }else{
                $iphone = Db::table('health_user')->where('user_tel',$data['user_tel'])->find();
                if($iphone)
                {
                    $error['msg']=9;

                }
                else
                    {
                    $result_id = DB::table('health_user')->insertGetId(['username'=>$data['user_account'],'password'=>$data['user_pwd'],'user_tel'=>$data['user_tel'],'user_email'=>$data['user_email'],'registered_time'=>$login_time,'user_ip'=>$login_ip]);
                    if($result_id)
                    {
                        //注册成功
                        session_start();
                        $_SESSION['admin_id']=$result_id;
                        $_SESSION['user_name'] = $data['user_account'];
                        $error['msg']=1;

                    }
                }

            }

        }
//        dump($error);die;
        return $error;

    }
    /**忘记密码模块**/
    public function pwd_list(){

        return view('after_pwd');
    }
    public function pwd_back1()
    {
        $username = $_GET['username'];

        $data = Db::table('health_user')->where('username',$username)->find();
        if($data)
        {
            $data['tel'] = str_replace(substr("$data[user_tel]",3,4),"****",$data['user_tel']);
            $data['email'] = str_replace(substr($data['user_email'],3),"***",$data['user_email']).substr($data['user_email'],strripos($data['user_email'],"@"));
            $data['success'] = 0;
            $data['msg'] = 1;
            return $data;
        }
        else
        {
            $data['success'] = 1;
            $data['msg'] = 1;
            $data['error'] = '没有该用户，请重新输入';
            return $data;
        }
    }


    /*
    * 手机找回密码（验证码）
    * msg 1:1分钟后在发送验证码
    * msg 2:发送成功
    */
//    public function Pwd_back_tel()
//    {
//        $tel =  Input::query('phone');
//
//        if(file_exists("code/$tel.txt") && time() - filemtime("code/$tel.txt")  < 60)
//        {
//            $error['msg']=1;
//            exit(json_encode($error));
//        }
//        else if(file_exists("code/$tel.txt"))
//        {
//            unlink("code/$tel.txt");
//        }
//
//        $rand = rand(100000,999999);
//        $file_path="code/$tel.txt";
//        file_put_contents($file_path,$rand,FILE_APPEND);
//
//        $c = new \TopClient;
//        $c ->appkey = "24496818" ;
//        $c ->secretKey = "e10a78f229b0b540d787d88cd53eae2f" ;
//        $req = new \AlibabaAliqinFcSmsNumSendRequest;
//        $req ->setExtend( "" );
//        $req ->setSmsType( "normal" );
//        $req ->setSmsFreeSignName( "" );
//        $req ->setSmsParam( "{number:'$rand'}" );
//        $req ->setRecNum( "$tel" );
//        $req ->setSmsTemplateCode( "SMS_72645024" );
//        if( $c ->execute( $req ))
//        {
//            $error['msg']=2;
//            exit(json_encode($error));
//        }
//    }

    /*
     * 修改密码
     * success  0,成功/1,失败
     */
    public function update_pwd()
    {
        $data = input();
        $data = (array)$data;
        $email = trim($data['email'],'{');
        $emails = trim($email,'}');
        $data['new_pwd'] = md5($data['new_pwd']);
//        echo $data['new_pwd'];die;
        if(isset($data['phone']))
        {
            DB::table('health_user')->where('user_tel',$data['phone'])->update(['password'=>$data['new_pwd']]);
        }
        else
        {
            $user = Db::table('health_user')->where('user_email',$emails)->find();
//            dump($user);die;
            DB::table('health_user')->where('id',$user['id'])->update(['password'=>$data['new_pwd']]);
        }

//        if($res)
//        {
        $date['success'] = 0;
        return json_encode($date);
//        }
//        else
//        {
//            $date['success'] = 1;
//            return json_encode($date);
//        }
    }


    /*
     * 发送邮件
     * success 0:成功；1:失败
     */

    public function pwd_email()
    {
//        $arr = input();
//        dump($arr);die;
        if(input('email'))
        {
            //接受邮件
            $email = input('email');
            $code = md5('finance'.$email);
            $time = time();
            $url = "health.lwebshop.com/pwd_back_email?code=$code&email=$email&time=$time";
            $title = '医保中心密码找回邮件';
            $data['email'] = $email;
            $message = "尊敬的医疗后台用户您好:您的密码已经重置请复制后面连接进行重新设置密码,邮件有效时间为5分钟。".$url;
            $flag=\phpmailer\Email::send($data['email'],$title,$message);
//            $flag = true;
            if($flag)
            {
                $data['success'] = 0;
                return $data;
            }
            else
            {
                $data['success'] = 1;
                $data['error'] = "发送邮件失败，请重试";
                return $data;
            }
        }
        else
        {
            return view("pwd_email");
        }

    }
    //邮件测试
    public function test(){
        $test = \phpmailer\Email::send('1364096285@qq.com','demo','线上9.22测试');
        var_dump($test);

//        $Test = new \test\Test();
//        $Test->test();
//        $config = [
//            'host'       => '47.93.61.134',
//            'port'       => 6379,
//            'password'   => '',
//            'select'     => 0,
//            'timeout'    => 0,
//            'expire'     => 0,
//            'persistent' => false,
//            'prefix'     => '',
//        ];
//        $redis = new Redis($config);
//        dump($redis);die;
////        $redis->set('test','zhangsan');
//        echo $redis->get("1");

    }
    public function email_success(){
        return view('pwd_email');
    }
    /*
     * 邮箱找回密码
     */
    public function pwd_back_email($code,$email,$time)
    {
        //邮件5分钟内有效
        $code_token = md5('finance'.$email);
        if(time()-$time<5*60){
            if($code == $code_token)
            {
                return view('pwd_back_email',['email'=>$email]);
            }
            else
            {
                echo "<script>alert('网络异常，请再试一次');location.href='/'</script>";
            }
        }else{
            echo "<script>alert('链接已失效,请重新再试');location.href='/'</script>";
        }

    }
}