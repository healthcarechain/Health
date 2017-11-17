<?php
namespace app\admins\controller;
use think\Db;
use think\cache;
use think\Controller;
use think\cache\driver\File;
use think\App;
class Clear extends Common{
    public function index()
    {
        //获取缓存路径
        return $this->fetch('clear');

    }
    public function go_clear(){
        $url = constant('RUNTIME_PATH');
        $cache_url = $url."temp";
//        stripslashes($cache_url);
        $Info = $this->Clear($cache_url);

        if(empty($Info))
        {
            return 1;
        }
    }
    public function Clear($path)
    {
        //得到完整目录
        if(is_dir($path))
        {

            if($openFile = opendir($path))
            {
                while(($file = readdir($openFile)) != false)
                {
                    if ("." == $file || ".." == $file)
                    {
                        continue;
                    }
//                 echo $path.''.$file.'<br>';
//                 echo $path.''.$file;
                    unlink($path.'\\'.$file);
                }
                closedir($openFile);
            }
        }
    }
    public function rmFile($path,$fileName){//删除执行的方法
        //去除空格
        $path = preg_replace('/(/){2,}|{}{1,}/','/',$path);
        //得到完整目录
        $path.= $fileName;
        //判断此文件是否为一个文件目录
        if(is_dir($path)){
            //打开文件
            if ($dh = opendir($path)){
                //遍历文件目录名称
                while (($file = readdir($dh)) != false){
                    //逐一进行删除
                    unlink($path.''.$file);
                }
                //关闭文件
                closedir($dh);
            }
        }
    }
}

?>