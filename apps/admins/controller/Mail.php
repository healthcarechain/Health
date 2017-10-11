<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/1 0001
 * Time: 下午 17:33
 */
namespace app\admins\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Query;
use think\View;
class Mail extends controller{
    public function index(){

        return view('mail');
    }
    public function personal(){
        return view('personal');
    }
}