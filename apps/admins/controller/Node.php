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
class Node extends Common{
    public function node_index()
    {
        return view('node_add');
    }
    public function node_add()
    {
        $node_desc = trim(input('node_desc'),"");
        $node_name = trim(input('node_name'),"");
        $node_url =  trim(input('node_url'),"");
        $urls = explode("\n",$node_url);
//        $result = Db::table('health_node')->insert(['node_name'=>$node_name,'node_desc'=>$node_desc,'node_url'=>json_encode($node_url)]);
//        dump($result);die;
        $res = array();
        $OnlyName = Db::table('health_node')->where('node_name',$node_name)->find();
        if(!empty($OnlyName))
        {
            //权限已经存在
            $res['status']=1;
        }
        else
        {
         $result = Db::table('health_node')->insert(['node_name'=>$node_name,'node_desc'=>$node_desc,'node_url'=>json_encode($urls)]);
         if($result)
         {
             //成功
             $res['status']=0;
         }
         else
         {
             //失败
             $res['status']=3;
         }
        }
        return json_encode($res);exit;
    }
    public function node_list()
    {
        $data = Db::table('health_node')->where('node_status',0)->paginate(5);
//        dump($data);die;
        return view('node_list',['data'=>$data]);
    }
    public function node_page()
    {
        $result = input();
        $page = isset($result['page']) ? $result['page'] : 1;
        $page_size = 10;
        $total = db('health_node')->count();
        $total_page = ceil($total/$page_size);
        $offset = ($page-1)*$page_size+1;
        if(array_key_exists('page',$result)){
            unset($result['page']);
        }
        $res = [];
        $res['data'] = Db::table('health_node')->limit($page_size)->where('node_status',0)->page($page)->select();
        foreach($res['data'] as $_key => $value)
        {

            $tmp_urls[] = json_decode($value['node_url'],true );
            $tmp_urls = $tmp_urls?$tmp_urls:[];
            $value['node_url']=implode("<br/>",$tmp_urls);
        }
//        dump($res['data']);die;
//     echo implode("<br/>",$tmp_urls);die;
//        dump($tmp_urls);die;

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
    public function node_del()
    {
        $node_id=input('id');
        if($node_id)
        {
            $res = Db::table('health_node')->where('node_id',$node_id)->delete();
            if($res)
            {
                echo 1;
            }
            else
            {
                echo 0;
            }
        }
    }
    public function node_uplist()
    {
        $id = input('id');
        $data = Db::table('health_node')->where('node_id',$id)->where('node_status',0)->find();
//        dump($data);die;
        return view('node_update',['data'=>$data]);
    }
    public function node_update()
    {
     $arr = input();
     $node_desc = trim(input('node_desc'),"");
     $node_name = trim(input('node_name'),"");
     $node_url  = trim(input('node_url'),"");
     $node_url = explode("\n",$node_url);
     $res = array();
     $result = Db::table('health_node')->where('node_name',$node_name)->where('node_id','NEQ',$arr['node_id'])->find();
     if(!$result)
     {
         $msgInfo = Db::table('health_node')->where('node_id',$arr['node_id'])->update(['node_name'=>$node_name,'node_desc'=>$node_desc,'node_url'=>json_encode($node_url)]);
         if($msgInfo)
         {
             $res['status']=1;
         }
     }
     else
     {
         //权限名已存在
         $res['status']=0;
     }
     return $res;die;
    }
}
?>