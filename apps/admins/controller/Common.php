<?php
namespace app\admins\Controller;
use think\Controller;
class Common extends Controller
{
    //防止非法登录
    public function __construct()
    {
        parent::__Construct();
        if(empty($_SESSION['id']))
        { //session('name',null);
            echo "<script>alert('您未登录,请先登录！');location.href='/';</script>";
        }
    }
//    public function _initialize()
//    {
//        if (!$this->checkLogin())
//        {
//            redirect('/');
//        }
//    }
//
//    /*
//     * 检查用户是否登录成功
//     */
//    private function checkLogin()
//    {
//        $id = session("id");
//        if (isset($id) && ($id > 0))
//        {
//            return true;
//        }
//        else
//        {
//            return false;
//        }
//    }
}