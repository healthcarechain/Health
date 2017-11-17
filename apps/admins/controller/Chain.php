<?php
namespace app\admins\controller;
//namespace Ali;
use think\cache\driver\Redis;
use think\Controller;
use think\Db;
use think\Request;
use think\Query;
use think\View;
use test\Page;
use think\paginator;
class Chain extends Common {
    public function index(){

        $date = Db::table('health_chain')->where('chain_file',0)->select();
        foreach($date as $k=>$v){
            $time['chain_time'] = date("Y-m-d H:i",$v['chain_time']);
            $data[] = array_merge($v,$time);
        }
//        dump($time);die;
//        datetime_format=false;
        $auto_load = Db::table('health_autoload')->find();
        return view('chain',['auto_load'=>$auto_load]);


    }
//    /**入链操作**/
    public function block(){
        $data = input();
        $admin_id = $_SESSION['admin_id'];
        $chainMsg = Db::table('health_chain')->where(['chain_id'=>$data['chain_id']])->find();
        $chainStr = implode($chainMsg);
        //获取当前数据的hash 验证唯一用户标识
        $chainHash = password_hash($chainStr,PASSWORD_DEFAULT);
//        $strNew = sprintf('%u',(float) microtime() * 1000);
//        echo $strNew;
        $uniqidChain = $chainHash.uniqid();
//        echo $uniqidChain;die;
        $chain_id = $data['chain_id'];
        /*存入到Redis中*/
        //实例化Redis对象
        $redis = new Redis();
//        dump($redis);die;
        //将当前操作人的id与当前数据的id成Key 存到redis中$admin_id.'&'.
        $Conkey = $chain_id;
        $result =  $redis->get($Conkey);
//        dump($result);die;
        $res = array();
//        $admin = Db::table('health_user')->where('id',$admin_id)->find();
//        dump($result);die;
        if(!$result){
            //进行入链
            $redisUp = $redis->set("$Conkey","$chainHash");
            //同时备份到数据库中
            Db::table('health_chash')->insert(['chain_id'=>$Conkey,'chain_hash'=>$chainHash]);
//            dump($redisUp);die;
           if($redisUp){
               //更改入链状态
               $up = Db::table('health_chain')->where(['chain_id'=>$data['chain_id']])->update(['chain_status'=>0]);
//               dump($up);die;
               if($up){
                   //入链成功
                   $res['status']=0;
               }else{
                   //入链失败
                  $res['status']=2;
               }
           }else{
               //1是入链失败，重试
                $res['status']=1;
           }

        }
        return $res;
    }
    public function search(){

        $data = input();
        $page = isset($data['page']) ? $data['page'] : 1;
        $page_size = 10;
        $total = db('health_chain')->count();
        $total_page = ceil($total/$page_size);
//        return $total_page;
        $offset = ($page-1)*$page_size+1;
//        echo $limit;die;
        if(array_key_exists('page',$data)){
            unset($data['page']);
        }
            $where=$this->getWhere($data);
            if($where){
                $adc['chain_time'] = [$where['chain_time'][0],$where['chain_time'][1]];
//     dump($adc);die;
                $mvp['chain_status'] = [$where['chain_status'][0],$where['chain_status'][1]];
                $result = Db::table('health_chain')->where($adc)->where($mvp)->page($page)->limit($page_size)->select();
                foreach($result as $k=>$v){
                    $time['chain_time'] = date("Y-m-d H:i",$v['chain_time']);
                    $result['data'][] = array_merge($v,$time);
                }
//                dump($result);die;
                if(!empty($result)){
                    $params = array(
                        'total_rows'=>$total,
                        'method'=>'ajax',
                        'now_page'=>$page,
                        'ajax_func_name'=>'page',
                        'list_rows'=>$page_size,
                        'parameter'=>http_build_query($data),
                    );
                    $pags = new \test\Page($params);
//            var_dump($pags);die;
                    $result['page']=$pags->show(3);
//            dump($result);die;
                    $result['status']=1;
                }else{
                    $result['status']=0;
                }
              return $result;
            }

    }
    /**进行搜索封装**/
    public function getWhere($where){
        $arr = array();
        switch (intval($where['chain_status'])){
            case 0:
            $arr['chain_status'] = array('eq',0);
            break;
            case 1:
            $arr['chain_status'] = array('eq',1);
            break;
            case 2:
            $arr['chain_status'] = array('neq',3);
            break;
            default:
        }
        $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
        $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
        $beginLastweek=mktime(0,0,0,date('m'),date('d')-date('w')+1-7,date('Y'));
        $endLastweek=mktime(23,59,59,date('m'),date('d')-date('w')+7-7,date('Y'));
        $beginThismonth = mktime(0,0,0,date('m')-2+1,1,date('Y'));
        $endThismonth = mktime(23,59,59,date('m'),date('t'),date('Y'));
        $Threemonth = mktime(0,0,0,date('m')-3+1,1,date('Y'));
        $Tendmonth = mktime(23,59,59,date('m'),date('t'),date('Y'));
        $Alltime =  mktime(0,0,0,date('m')-3+1,1,date('Y')-10);
        switch(intval($where['chain_time'])){
             case 0:
            //当天的时间
             $arr['chain_time'] = ['between',[$beginToday,$endToday]];
             break;
             case 1:
            //当前一周的时间
             $arr['chain_time'] = ['between',[$beginLastweek,$endLastweek]];
             break;
             case 2:
             //当前一个月的时间
              $arr['chain_time'] = ['between',[$beginThismonth,$endThismonth]];
              break;
              case 3:
              //当前三个月的的时间 （算上当前的月份）
              $arr['chain_time'] = ['between',[$Threemonth,$Tendmonth]];
              break;
              case 4:
               $arr['chain_time'] = ['between',[$Alltime,$endToday]];
              default:
        }
        return $arr;
    }
    protected function time(){
        $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
        $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
        $beginLastweek=mktime(0,0,0,date('m'),date('d')-date('w')+1-7,date('Y'));
        $endLastweek=mktime(23,59,59,date('m'),date('d')-date('w')+7-7,date('Y'));
        $beginThismonth = mktime(0,0,0,date('m')-2+1,1,date('Y'));
        $endThismonth = mktime(23,59,59,date('m'),date('t'),date('Y'));
        $Threemonth = mktime(0,0,0,date('m')-3+1,1,date('Y'));
        $Tendmonth = mktime(23,59,59,date('m'),date('t'),date('Y'));
        $Alltime =  mktime(0,0,0,date('m')-3+1,1,date('Y')-10);
        $map['chain_status']=1;
//        $map['chain_status']=2;
//
//// and 关系
////        $map['chain_time']=array('egt',1505192742);
////        $map['chain_time']=array('elt',1502514441);->where('chain_status',['eq',0],['eq',1],'or')
//        $map1['chain_status']=array('eq',1);
//        $map2['chain_time']=array('between','1183219200,1905145600');
//        $list = Db::name('health_chain')->where($map2)->where($map1)->select();
////dump($list);die;
    }
    //病案归档
    public function file(){
        $data = Db::table('health_chain')->where('chain_file',1)->paginate(10);
        $auto_load = Db::table('health_autoload')->find();
        $arr = Db::table('health_chain')->where('chain_file',1)->select();
        if(!empty($arr)){
            $res='';
            return view('file',['res'=>$res,'data'=>$data,'auto_load'=>$auto_load]);
        }else{
          $res = '暂时没有数据：)';
          return view('file',['res'=>$res,'data'=>$data,'auto_load'=>$auto_load]);
        }
    }
    public function file_add(){
        $id = input('id');
//        dump($id);
        //记录下当前归档管理员id  只为记录使用
        $admin_id = $_SESSION['admin_id'];
        $time = time();
        $result = Db::table('health_chain')->where('chain_id',$id)->update(['chain_file'=>0,'chain_time'=>$time]);
//        dump($result);die;
        if($result){

            $up = Db::table('health_fileup')->insert(['admin_id'=>$admin_id,'chain_id'=>$id,'file_time'=>$time]);
            if($up){

                return 1;
            }
        }else{
            return 0;
        }
    }
    /**自动归档**/
    public function aotomatic(){
    //状态为1的时候为OFF 状态为0的时候ON
        $data = Db::table('health_autoload')->find();
        $arr = array();
        if($data['auto_file']==0){
           $res = Db::table('health_autoload')->where('id',1)->update(['auto_file'=>1]);
           if($res){
               $arr['msg']=1;
           }
        }else if($data['auto_file']==1){
           $res = Db::table('health_autoload')->where('id',1)->update(['auto_file'=>0]);
           if($res){
               $arr['msg']=0;
           }
        }
        return $arr;
    }
    /**自动入链**/
    public function checkchain(){
        //状态为1的时候为OFF 状态为0的时候ON
        $data = Db::table('health_autoload')->find();
        $arr = array();
        if($data['auto_chain']==0){
            $res = Db::table('health_autoload')->where('id',1)->update(['auto_chain'=>1]);
            if($res){
                $arr['msg']=1;
            }
        }else if($data['auto_chain']==1){
            $res = Db::table('health_autoload')->where('id',1)->update(['auto_chain'=>0]);
            if($res){
                $arr['msg']=0;
            }
        }
        return $arr;
    }
    /**入链修改**/
    public function uplist(){
        $data = Db::table('health_chain')->where('chain_status',0)->paginate(5);
        return view('uplist',['data'=>$data]);
    }
    public function chain_update(){
        $id = input('id');
        $data = db('health_chain')->where('chain_id',$id)->find();
        if($data){
            return view('chain_up',['data'=>$data]);
        }else{
            $data = db('health_chain')->where('chain_status',0)->paginate(5);
            return  view('chain_up',['data'=>$data]);
        }
    }
    public function newdate(){
//        echo 1;die;
        $data = input();
        $flag = Db::table('health_chain')->where('chain_id',$data['id'])->update([
            'chain_title'=>$data['title'],'chain_user'=>$data['name'],
            'chain_age'=>$data['age'],'chain_sex'=>$data['sex'],
            'chain_num'=>$data['num'],'chain_department'=>$data['department'],
            'chain_lesion'=>$data['lesion'],
        ]);
        if($flag){
            $chainMsg = Db::table('health_chain')->where('chain_id',$data['id'])->find();
            $chainStr = implode($chainMsg);
            //生成新的hash 验证唯一用户标识
            $NewHash = password_hash($chainStr,PASSWORD_DEFAULT);
            $admin_id=$_SESSION['admin_id'];
            $update_time = time();
            $chain_id = $data['id'];
            Db::table('health_chain')->where('chain_id',$chain_id)->update(['chain_modify'=>1]);
            $result = Db::table('health_newchain')->insert(['chain_id'=>$chain_id,'chain_hash'=>$NewHash,'admin_id'=>$admin_id,'update_time'=>$update_time]);
            if($result){
                $arr = ['status'=>0];
            }
        }else{
            $arr = ['status'=>1,'error'=>'修改失败,请重新修改'];
        }
        return $arr;die;
    }
}