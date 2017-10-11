<?php
/**
 * Created by PhpStorm.
 * User: ltstu
 * Date: 2017/9/18
 * Time: 16:26
 */
namespace app\admins\controller;
use think\Controller;
use think\Db;
use think\Query;
use think\cache\driver\Redis;
class Auto extends Controller{
    /**自动加载类**/
    public function file_index(){
        //没有归档的数据 进行归档 数据归档完成后 存入数据日志中 存入文件 修改状态 计划任务 12个小时自动去更新一次
        $time = date("Y-m-d",time());
        $nofile = Db::execute("select * from health_chain where chain_file = 1 into outfile '/phpstudy/www/shellfile/$time.txt'");
        if($nofile){
            $now = time();
            $arr = Db::execute("update health_chain set chain_file = 0,chain_time='$now' where chain_file = 1 ");
            dump($arr);
        }

    }
    /**自动入链**/
    public function file_chain(){
        $time = date("Y-m-d",time());
        $chainArr = Db::table('health_chain')->where('chain_status',1)->find();
//        dump($chainArr);die;
        $chainStr = implode($chainArr);
        $chainHash = password_hash($chainStr,PASSWORD_DEFAULT);
//        echo $chainHash;die;
        $unchain = $chainHash.uniqid();
//        $hash = '$2y$10$aIazn7Iv8oVUVE8L57uvqelB/lGwG3IRhp2hV91Bxy5LXNPt12sUe59db06cf7ab73';
//        $res = password_verify($chainStr,$chainHash);
//        var_dump($res);die;
        $chain_id = $chainArr['chain_id'];
//        echo $chain_id;die;
        $redis = new Redis();
//        dump($redis);die;
        $Conkey = $chain_id;
        $result =  $redis->get($Conkey);
        if(!$result){
            $redisUp = $redis->set("$Conkey","$chainHash");
            //BC到Mysql
            Db::table('health_chash')->insert(['chain_id'=>$Conkey,'chain_hash'=>$chainHash]);
//            dump($redisUp);die;
            if($redisUp){
                $chainBfile = "/phpstudy/www/shellchain/$time.txt";
                if(!is_file($chainBfile)){
                    //记录自动入链状态
                    Db::execute("select * from health_chain where chain_status = 1 into outfile '/phpstudy/www/shellchain/$time.txt'");
                }
                //更改入链状态
                $up = Db::table('health_chain')->where(['chain_id'=>$chain_id])->update(['chain_status'=>0]);
                dump($up);die;
            }
        }else{
            $redis->where($Conkey)->delete();
            $redisNew = $redis->set("$Conkey","$chainHash");
        }
//        $chainArr = Db::table('health_chain')->where('chain_status',0)->select();
//        foreach($chainArr as $k=>$v){
//            $chainStr = implode($v);
////            echo $chainStr.'<br>';
//            $chainHash = password_hash($chainStr,PASSWORD_DEFAULT);
////            echo $chainHash.'<br>';
//            $uniquchainHash = $chainHash.uniqid();
////            echo $uniquchainHash.'<br>';
//            $redis = new Redis();
//
//        }
//        echo $chainHash;
//        $chainStr = implode($chainArr);
//        for($i=0;$i<count($chainArr);$i++){
////            $chainHash = password_hash($chainArr[$i],PASSWORD_DEFAULT);
////          $chainStr = import($chainArr[$i]);
//        }
//        echo $chainStr;

//        dump($chainHash);

    }
}