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

class Admin extends controller
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
    }

    public function index(){
        $arr = DB::name('health_user')
            ->paginate(5);
        $page = $arr->render();
        $this->assign('user',$arr);
        $this->assign('page',$page);
        return $this->fetch('admin_list');
    }
    public function del_user(){
        $id = $_POST['id'];
        //$arr = DB::execute("delete from health_user where id=$id");
        return '删除成功';
    }
}