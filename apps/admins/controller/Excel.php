<?php
namespace app\admins\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Query;
use think\View;
class Excel extends Common
{
  public function Bom()
  {
      /*检测并清除BOM*/
      if(isset($_GET['dir'])){
          $basedir=$_GET['dir'];
      }else{
          $basedir = '.';
      }
      $auto = 1;
      $this->checkdir($basedir);

      function checkdir($basedir)
      {
          if($dh = opendir($basedir)){
              while(($file = readdir($dh)) !== false){
                  if($file != '.' && $file != '..'){
                      if(!is_dir($basedir."/".$file) && pathinfo($file, PATHINFO_EXTENSION)=='php'){
                          echo "filename: $basedir/$file ".$this->checkBOM("$basedir/$file")." <br>";
                      }else if(is_dir($basedir."/".$file)){
                          $dirname = $basedir."/".$file;
                         $this->checkdir($dirname);
                      }
                  }
              }//end while
              closedir($dh);
          }//end if
      }//end checkdir
      function checkBOM($filename)
      {
          global $auto;
          $contents = file_get_contents($filename);
          $charset[1] = substr($contents, 0, 1);
          $charset[2] = substr($contents, 1, 1);
          $charset[3] = substr($contents, 2, 1);
          if(ord($charset[1]) == 239 && ord($charset[2]) == 187 && ord($charset[3]) == 191){
              if($auto == 1){
                  $rest = substr($contents, 3);
                  $this->rewrite ($filename, $rest);
                  return "<font color=red>BOM found, automatically removed.</font>";
              }else{
                  return ("<font color=red>BOM found.</font>");
              }
          }
          else return ("BOM Not Found.");
      }//end checkBOM
      function rewrite($filename, $data)
      {
          $filenum = fopen($filename, "w");
          flock($filenum, LOCK_EX);
          fwrite($filenum, $data);
          fclose($filenum);
      }//end rewrite
  }
}