<?php

namespace app\admins\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Query;
use think\View;

class Invest extends controller{
//    function _construct(){
//        parent::_construct();
//        $this -> view -> replace(['__PUBLIC__' => '/static',]);
//    }
    public function index(){
//        echo 1;die;
        return $this->fetch('invest_list');
    }
    public function ajaxs(){
        $page = intval($_POST['pageNum']); //当前页
        $res = array();
        $res_1=array();
        if(!empty($_POST['time_start'])){
            $res['update_time'] = array('>=',strtotime($_POST['time_start']));
        }
        if(!empty($_POST['time_end'])){
            $res['update_time'] = array('<',strtotime($_POST['time_end']));
        }

        $res = array_filter($res);
        foreach($res as $key=>$value){
            $res_1['query'][$key] = $value;
        }
        $total = DB::name('health_newchain')
            ->where($res)
            ->count();
        //->group('chain_num')
        //->count('distinct chain_id');
        $pageSize = 6; //每页显示数
        $totalPage = ceil($total/$pageSize); //总页数
        $startPage = $page*$pageSize; //开始记录

        //构造数组
        $arr['total'] = $total;
        $arr['pageSize'] = $pageSize;
        $arr['totalPage'] = $totalPage;
        $data= DB::name('health_newchain')
            ->where($res)
            //->group('chain_num')
            ->limit("$startPage,$pageSize")->select();
        foreach($data as &$v){
            $name = Db::name('health_user')->where(['id'=>$v['admin_id']])->column('username');
            $v['admin_name'] = $name;
            $name_chain = Db::name('health_chain')->where(['chain_id'=>$v['admin_id']])->column('chain_user');
            $v['chain_name'] = $name_chain;
            $up_time = date('Y-m-d H:i:s',$v['update_time']);
            $v['update_time'] = $up_time;
        }
        $arr['list'] = $data;
        echo json_encode($arr); //输出JSON数据
    }
}