<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
//    '__pattern__' => [
//        'name' => '\w+',
//    ],
//    '[hello]'     => [
//        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
//        ':name' => ['index/hello', ['method' => 'post']],
//    ],
    //登录首页
    "/"=>"admins/Login/index",
    //登录模块
    "index_index"=>"admins/Login/add",
    //注册模块
    'login_index'=>"admins/Login/login",
    'login_success'=>"admins/Login/success_index",
    'username_only'=>"admins/Login/user_only",
    'tel_only'=>"admins/Login/tel_only",
    'tel_code'=>"admins/Login/tel_code",
    "user_add"=>"admins/Login/user_add",
    "email_only"=>"admins/Login/email_only",
    //后台首页
    "index_admin"=>"admins/Mail/index",
    "personal"=>"admins/Mail/personal",
    //后台退出
    "logout"=>"admins/Login/logout",
    //密码找回
    "pwd_list"=>"admins/Login/pwd_list",
    "pwd_back"=>"admins/Login/pwd_back1",
    "pwd_email"=>"admins/Login/pwd_email",
    "update_pwd"=>"admins/Login/update_pwd",
    "pwd_back_email"=>"admins/Login/pwd_back_email",
    "test"=>"admins/Login/test",
    //病案入链
    "chain_list"=>"admins/Chain/index",
    "chain_search"=>"admins/Chain/search",
    "chain_block"=>"admins/Chain/block",
    "chain_uplist"=>"admins/Chain/uplist",
    "chain_update"=>"admins/Chain/chain_update",
    "chain_newdate"=>"admins/Chain/newdate",
    //选择归档
    "chain_file"=>"admins/Chain/file",
    "chain_fadd"=>"admins/Chain/file_add",
    "chain_aotomatic"=>"admins/Chain/aotomatic",
    "chain_checkchain"=>"admins/Chain/checkchain",
    //病案管理
    'medical_add'=>'admins/Medical/index',
    'department_add'=>"admins/Medical/add",
    'medical_delete'=>'admins/Medical/del_list',
    'medical_del'=>'admins/Medical/dele',
    'medical_delall'=>'admins/Medical/deleall',
    'medical_up'=>'admins/Medical/up',
    'medical_update'=>'admins/Medical/update',
    'medical_newup'=>"admins/Medical/newup",
    //9.21 LF 监控查询
    'pass_index'=>'admins/Pass/index',
    'pass_result'=>'admins/Pass/go_result',
    'pass_pro'=>'admins/Pass/pass_pro',
    'admin_list'=>'admins/Admin/index',
    //自动加载类
    'chain_load'=>"admins/Auto/file_index",
    'chain_loca'=>"admins/Auto/file_chain",
    //10.09 消息通知
    'invest_list'=>'admins/Invest/index',
];
