<?php
namespace app\admins\controller;
//namespace Ali;
use think\Controller;
use think\Db;
use think\Request;
use think\Query;
use think\View;
class Medical extends Controller{
    //展示添加页面
    public function index(){
        return view('medical_add');
    }
    //实现添加功能
    public function add(){
        $data = input();
        if(!empty($data)){
            $flag = Db::table('health_chain')->insert([
                'chain_title'=>$data['title'],'chain_user'=>$data['name'],
                'chain_age'=>$data['age'],'chain_sex'=>$data['sex'],
                'chain_num'=>$data['num'],'chain_department'=>$data['department'],
                'chain_lesion'=>$data['lesion'],'chain_content'=>$data['content'],
            ]);
            if($flag){
                //获取当前登录的管理员的admin_id

                session_start();
                $admin_id=$_SESSION['admin_id'];
                $time = time();
                $chain_id = db('health_chain')->getLastInsID();
                //将执行时间和管理添加的id和当前病案的id存到关系表中
                db('health_addcm')->insert(['admin_id'=>$admin_id,'add_time'=>$time,'chain_id'=>$chain_id]);
                $arr = array("status"=>0);
            }else{
                $arr = array("status"=>1,'error'=>'添加病案失败');
            }
            return $arr;die;
        }

    }
    public function del_list(){
        //进行分页展示
//        page();
        $data = Db::table('health_chain')->where('chain_status',1)->paginate(5);
//        dump($data);die;
        return  view('medical_del',['data'=>$data]);
    }
    /**删除**/
    public function dele(){
        $id = input('id');
        $res = db('health_chain')->where('chain_id',$id)->delete();
        if($res){
            //将执行时间和管理删除的id和当前病案的id存到关系表中
            session_start();
            $admin_id=$_SESSION['admin_id'];
            $time = time();
            db('health_delcm')->insert(['admin_id'=>$admin_id,'del_time'=>$time,'chain_id'=>$id]);
            return 1;
        }else{
            return 0;
        }
    }
    /***批量删除**/
    public function deleall(){
        $ids = input('new_str');
//        dump($ids);die;
        $res = db::execute("delete from health_chain where chain_id in ($ids)");
        if($res){
            session_start();
            $admin_id=$_SESSION['admin_id'];
            $time = time();
            db('health_delcm')->insert(['admin_id'=>$admin_id,'del_time'=>$time,'chain_id'=>$ids]);
            return 1;
        }else{
            return 0;
        }
    }
    //分页类
    public function page($page,$page_size){
        $page = isset($_GET['page'])?$_GET['page']:1;
        $page_size = isset($_GET['page_size'])?$_GET['page_size']:10;
        $limit = ($page-1)*$page_size;
        //总条数
        $total = db('health_chain')->count();
        //总页数
        $total_page = ceil($total/$page_size);
    }
    /**进行修改**/
    public function up(){
        //分页
        $data = db('health_chain')->where('chain_status',1)->paginate(5);
        return  view('medical_update',['data'=>$data]);
    }
    public function update(){
        $id = input('id');
//        dump($id);die;
        $data = db('health_chain')->where('chain_id',$id)->find();
//        var_dump($data);die;
        if($data){
            return view('medical_up',['data'=>$data]);
        }else{
            $data = db('health_chain')->where('chain_status',1)->paginate(10);
            return  view('medical_update',['data'=>$data]);
        }
    }
    public function newup(){
        $data = input();
        $flag = db('health_chain')->where('chain_id',$data['id'])->update([
            'chain_title'=>$data['title'],'chain_user'=>$data['name'],
            'chain_age'=>$data['age'],'chain_sex'=>$data['sex'],
            'chain_num'=>$data['num'],'chain_department'=>$data['department'],
            'chain_lesion'=>$data['lesion'],
        ]);
        if($flag){
            session_start();
            $admin_id=$_SESSION['admin_id'];
            $time = time();
            $chain_id=$data['id'];
            //将执行时间和管理添加的id和当前病案的id存到关系表中
            db('health_updcm')->insert(['admin_id'=>$admin_id,'update_time'=>$time,'chain_id'=>$chain_id]);
            $arr = ['status'=>0];

        }else{
            $arr = ['status'=>1,'error'=>'修改失败,请重新修改'];
        }
        return $arr;die;
    }
}