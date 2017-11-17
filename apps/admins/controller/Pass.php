<?php

namespace app\admins\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Query;
use think\View;
use think\cache\driver\Redis;
use PHPExcel;
use PHPExcel_IOFactory;

class Pass extends Common
{
    function _construct()
    {
        parent::_construct();
        $this->view->replace(['__PUBLIC__' => '/static',]);
    }

    public function index()
    {
        return $this->fetch('pass_index');
    }

    public function pass_pro()
    {
        $page = 0;
        $total = DB::name("health_chain")
            ->count();//总记录数
        $pageSize = 6; //每页显示数
        $totalPage = ceil($total / $pageSize); //总页数

        $startPage = $page * $pageSize; //开始记录
        //构造数组
        $arr['total'] = $total;
        $arr['pageSize'] = $pageSize;
        $arr['totalPage'] = $totalPage;
        $query = DB::name("health_chain")
            //->group('chain_num')
            ->limit($startPage, $pageSize)
            ->select();//查询分页数据
        //$row=mysql_fetch_array($query);
        $i = 0;
        foreach ($query as $key => $val) {
            $arr['list'][] = array(
                'chain_user' => $val['chain_user'],
            );
        }

//        echo json_encode($arr); //输出JSON数据
        return $this->fetch();
    }

    public function pass_result($doc)
    {
        $check['chain_user|chain_department|chain_content'] = array('like', '%' . $doc . '%');
        $arr = DB::name('health_chain')
            ->where($check)
            ->paginate(5, false, [
                'query' => array('doc' => $doc),
            ]);
        $page = $arr->render();
        $this->assign('lists', $arr);
        $this->assign('page', $page);
        $this->assign('param_1', $doc);
        return $this->fetch('pass_result');
    }

    public function search_pass()
    {

    }

    public function ajaxs()
    {
        $page = intval($_POST['pageNum']); //当前页
        $res = array();
        $res_1 = array();
        $arr = $_POST['arr'];
        $arr = explode('&', $arr);
        foreach ($arr as $val) {
            $get = explode('=', $val);
            if (!empty($get[1])) {
                if ($get[0] == 'chain_department' || $get[0] == 'chain_lesion' || $get[0] == 'chain_num') {
                    $res[$get[0]] = array('like', '%' . $get[1] . '%');
                } else if ($get[0] == 'date_start') {
                    $res['chain_time'] = array('>=', strtotime($get[1]));
                } else if ($get[0] == 'date_end') {
                    $res['chain_time'] = array('<', strtotime($get[1]));
                } else {
                    $res[$get[0]] = $get[1];
                }
            }
        }
        $res = array_filter($res);
        foreach ($res as $key => $value) {
            $res_1['query'][$key] = $value;
        }
        $total = DB::name('health_chain')
            ->where($res)
            ->count();
        //->group('chain_num')
        //->count('distinct chain_id');
        $pageSize = 6; //每页显示数
        $totalPage = ceil($total / $pageSize); //总页数
        $startPage = $page * $pageSize; //开始记录

        //构造数组
        $arr['total'] = $total;
        $arr['pageSize'] = $pageSize;
        $arr['totalPage'] = $totalPage;
        $data = DB::name('health_chain')
            ->where($res)
            ->limit("$startPage,$pageSize")->select();
        foreach ($data as &$v) {
            $v['chain_sex'] = ($v['chain_sex'] == 0) ? '男' : '女';
            $v['chain_modify'] = ($v['chain_modify'] == 0) ? '未修改' : '已修改';
        }
//        dump($v)
        $arr['list'] = $data;
        echo json_encode($arr); //输出JSON数据
    }

    public function show_hash()
    {
        $arr = array();
        $id = $_POST['id'];
        $Redis = new Redis();
        $original_hash = $Redis->get($id);
        $chainMsg = Db::table('health_chain')->where(['chain_id' => $id])->find();
        $chainStr = implode($chainMsg);
        $chainHash = Db::name('health_newchain')->where(['chain_id' => $id])->select();
        foreach ($chainHash as &$val) {
            $res = password_verify($chainStr, $val['chain_hash']);
            $name = Db::name('health_user')->where(['id' => $val['admin_id']])->column('username');
            $val['admin_name'] = $name;
            $time = date('Y-m-d H:i:s', $val['update_time']);
            $val['update_time'] = $time;
            $val['original_hash'] = $original_hash;
            $arr[] = $val;
        }
        return $arr;

        //$res = password_verify ( $chainStr , string $hash )
    }

    //导出Execl数据
    public function Export_excel()
    {
        $date = input();
        $chain_id = @$date['str'];
        if ($chain_id)
        {
            $data['arr'] = Db::table('health_chain')
                ->where('chain_id','in',$chain_id)
                ->select();

            if($data)
            {
                $data['success']=1;
            }
            else
            {
                $data['success']=2;
            }
          echo json_encode($data);die;

            //设置导出文件名
//            $time = date("Y-m-d", time());
//            $file_name = $time . '电子监控病案' . '.' . 'xls';
//            $res = service('ExcelToArrary')->push($arr, $file_name);
            //Vendor("PHPExcel");
            //Vendor("Excel2007");
//            /Api/excel/PHPExcel/Writer/Excel2007.php';
           // $excel = new \PHPExcel();
//            var_dump($excel);die;
            //Excel表格式
            //构造数组
          //  $letter = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
            //构造表头数据
          //  $tableheader = array('编号','病案标题','姓名','年龄','性别','病案号','病诊科室','住院院区','病案归档','归档时间','病案入链','病案备注','是否修改');
//            for($j=0;$j<count($tableheader);$j++)
//            {
//             $excel->getActiveSheet()->setCellValue("$letter[$j]1","$tableheader[$j]");
//            }
//            dump($arr);
//            for ($k = 0; $k<count($chain_id);$k++)
//            {


                //$data = iconv('utf-8','gb2312',$data);
              //  for($i=2;$i <= count($data) + 1;$i++)
                //{
                 //   $j = 0;
                  //  foreach ($data[$i-2] as $key=>$value)
                   // {
                   //     $excel->getActiveSheet()->setCellValue("$letter[$j]$i","$value");
                  //      $j++;
                   // }

//             }
//                dump(APP_PATH);die;
//            if($data)
//            {
//                $data['success']=1;
//            }
//            else
//            {
//                $data['success']=2;
//            }
//            return $data;die;
//            dump($data['success']);die;

          //  }
            //$write = new \PHPExcel_Writer_Excel2007($excel);
//            dump($write);die;
//            ob_end_clean();
//            ob_clean();

//            header("Pragma: public");
//            header("Expires: 0");
//            header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
//            header("Content-Type:application/force-download");
//            header("Content-Type:application/vnd.ms-execl");
//            header("Content-Type:application/octet-stream");
//            header("Content-Type:application/download");;
//            header('Content-Disposition:attachment;filename="导出测试.xls"');
//            header("Content-Transfer-Encoding:binary");
            //==============================================
//            header("Cache-Control: public");
//            header("Pragma: public");
//            header("Content-Type: application/force-download");
//            header("Content-Type: application/octet-stream");
//            header("Content-Type:application/download");
//            header("Content-type:application/vnd.ms-excel");
//            header("Content-Disposition:attachment;filename=文件名.xls");
//            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            //也不知道为什么下面2个要一起用才OK,折腾4天,关键就在这吧
//            $filename = urlencode("个税表.xls");
//            ob_end_clean();
//            header('Content-Type: application/vnd.ms-excel');
//            header('Content-Disposition: attachment;filename='.$filename);
//            header('Cache-Control: max-age=0');
//            $objWriter = PHPExcel_Writer_Excel2007::createWriter($excel, 'Excel5');
//            $write->save('php://output');
//            $write->save('php://output');die;
//===================================================

        }
        else
        {
            //错误
            echo '暂时没有数据';die;
        }


    }
    public function export_data($data = array())
    {
        # code...
        include_once(APP_PATH.'Tools/PHPExcel/Classes/PHPExcel/Writer/IWriter.php') ;
        include_once(APP_PATH.'Tools/PHPExcel/Classes/PHPExcel/Writer/Excel5.php') ;
        include_once(APP_PATH.'Tools/PHPExcel/Classes/PHPExcel.php') ;
        include_once(APP_PATH.'Tools/PHPExcel/Classes/PHPExcel/IOFactory.php') ;
        $obj_phpexcel = new PHPExcel();
        $obj_phpexcel->getActiveSheet()->setCellValue('a1','Key');
        $obj_phpexcel->getActiveSheet()->setCellValue('b1','Value');
        if($data){
            $i =2;
            foreach ($data as $key => $value) {
                # code...
                $obj_phpexcel->getActiveSheet()->setCellValue('a'.$i,$value);
                $i++;
            }
        }
        $obj_Writer = PHPExcel_IOFactory::createWriter($obj_phpexcel,'Excel5');
        $filename = "outexcel.xls";
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="'.$filename.'"');
        header("Content-Transfer-Encoding: binary");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        $obj_Writer->save('php://output');
    }
    private function _export_data($data = array())
    {
        error_reporting(E_ALL); //开启错误
        set_time_limit(0); //脚本不超时
        date_default_timezone_set('Europe/London'); //设置时间
        /** Include path **/
        // Create new PHPExcel object
        Vendor('PHPExcel');
        Vendor('phpexcel.IOFactory');
        $objPHPExcel = new \PHPExcel();
        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
            ->setLastModifiedBy("Maarten Balliauw")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Test result file");
        // Add some data
        $letter = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        if($data){
            $i = 1;
            foreach ($data as $key => $value) {
                $newobj = $objPHPExcel->setActiveSheetIndex(0);
                $j = 0;
                foreach ($value as $k => $val) {
                    $index = $letter[$j]."$i";
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($index, $val);
                    $j++;
                }
                $i++;
            }
        }
        $date = date('Y-m-d',time());
        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle($date);
        $objPHPExcel->setActiveSheetIndex(0);
        // Redirect output to a client's web browser (Excel2007)
        ob_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$date.'.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
//        dump($objWriter);die;
        $objWriter->save('php://output');
        exit;
    }
    public function _export_date($data=array(),$name='Excel'){
        header("content-type:text/html;charset=utf-8");
        $path = dirname(__FILE__);
//        Vendor("PHPExcel");
//        Vendor("phpexcel.IOFactory");
//        error_reporting(E_ALL);
//        date_default_timezone_set('Europe/London');
        $objPHPExcel = new PHPExcel();
        //获取当前sheet操作对象
        $SheetPHP = $objPHPExcel->getActiveSheet();
        $SheetPHP->setTitle('病案信息');
//        dump($objPHPExcel);die;
        /*以下是一些设置 ，什么作者  标题啊之类的*/
//        $objPHPExcel->getProperties()->setCreator("病案管理数据")
//            ->setLastModifiedBy("病案管理数据")
//            ->setTitle("数据EXCEL导出")
//            ->setSubject("数据EXCEL导出")
//            ->setDescription("备份数据")
//            ->setKeywords("excel")
//            ->setCategory("result file");
//        $objPHPExcel->setActiveSheetIndex(0)
        $SheetPHP->setCellValue('A1', '编号')
            ->setCellValue('B1', '病案标题')
            ->setCellValue('C1', '姓名')
            ->setCellValue('D1', '年龄')
            ->setCellValue('E1', '性别')
            ->setCellValue('F1', '病案号')
            ->setCellValue('G1', '出院科室')
            ->setCellValue('H1', '出院病区')
            ->setCellValue('I1', '归档标志')
            ->setCellValue('J1', '归档时间')
            ->setCellValue('K1', '入链操作')
            ->setCellValue('L1', '病案备注')
            ->setCellValue('M1', '入链修改')
            ->setCellValue('N1', '入链时间')
            ->setCellValue('O1', '病案类型')
            ->setCellValue('P1', '修改标识');
//        foreach($data as $k => $v)
//        {
//            $num=$k+1;
//            $objPHPExcel->setActiveSheetIndex(0)
//                //Excel的第A列，uid是你查出数组的键值，下面以此类推
//                ->setCellValue('A'.$num, $v['chain_id'])
//                ->setCellValue('B'.$num, $v['chain_title'])
//                ->setCellValue('C'.$num, $v['chain_user'])
//                ->setCellValue('D'.$num, $v['chain_age'])
//                ->setCellValue('E'.$num, $v['chain_sex'])
//                ->setCellValue('F'.$num, $v['chain_num'])
//                ->setCellValue('G'.$num, $v['chain_department'])
//                ->setCellValue('H'.$num, $v['chain_lesion'])
//                ->setCellValue('J'.$num, $v['chain_file'])
//                ->setCellValue('K'.$num, $v['chain_time'])
//                ->setCellValue('L'.$num, $v['chain_status'])
//                ->setCellValue('M'.$num, $v['chain_content'])
//                ->setCellValue('N'.$num, $v['chain_modify'])
//                ->setCellValue('O'.$num, $v['chain_chtime'])
//                ->setCellValue('P'.$num, $v['health_insurance'])
//                ->setCellValue('Q'.$num, $v['archives_modify']);
//            }

//        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
        ob_end_clean();
//        header('Content-Type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$name.'.xls"');
        header('Cache-Control: max-age=0');

//        $PHPWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
        $objWriter->save('php://output');
    }
    public function export_demo($dataResult=array())
    {
          $date = input();
          $chain_id = @$date['data'];
         $dataResult=Db::table('health_chain')
             ->where('chain_id','in',$chain_id)
             ->select();
//        $dataResult = input('data');
//        $dataResult = json_decode($dataResult,true);
//        dump($dataResult);die;
        $headTitle = "医疗链病案数据";
        $title = date("Y-m-d")."-"."病案质检导出数据";
//        $headtitle = "<tr style='height:50px;border-style:none;><td border=\"0\" style='height:60px;width:270px;font-size:22px;' colspan='11'>{$headTitle}</th></tr>";
        $headtitle = "<tr style='height:50px;border-style: none;><td border=\"0\" style='height: 60px;width:270px;font-size=22px;' colspan='11'>{$headTitle}</th></tr>";
        $titlename="<tr>
           <td>编号</td>
           <td>病案标题</td>
           <td>姓名</td>
           <td>年龄</td>
           <td>性别</td>
           <td>病案号</td>
           <td>出院科室</td>
           <td>出院病区</td>
           <td>归档标志</td>
           <td>归档时间</td>
           <td>入链操作</td>
           <td>病案备注</td>
           <td>入链修改</td>
           <td>入链时间</td>
           <td>病案类型</td>
           <td>修改标识</td>
       </tr>";
        $filename = $title.".xls";
//        dump($title);
        $this->excelData($dataResult,$titlename,$headtitle,$filename);

    }
    /*
     *处理Excel导出
     *@param $datas array 设置表格数据
     *@param $titlename string 设置head
     *@param $title string 设置表头
     */
    public function excelData($datas,$titlename,$title,$filename)
    {
        $str = "<html xmlns:o=\"urn:schemas-microsoft-com:office:office\"\r\nxmlns:x=\"urn:schemas-microsoft-com:office:excel\"\r\nxmlns=\"http://www.w3.org/TR/REC-html40 \">\r\n<head>\r\n<meta http-equiv=Content-Type content=\"text/html;charset=utf-8\">\r\n</head>\r\n<body>";
        $str .="<table border=1><head>".$titlename."</head>";
        $str .= $title;
        foreach ($datas  as $key=> $rt )
        {
            $str .= "<tr>";
            $str .= "<td>{$rt['chain_id']}</td>";
            $str .= "<td>{$rt['chain_title']}</td>";
            $str .= "<td>{$rt['chain_user']}</td>";
            $str .= "<td>{$rt['chain_age']}</td>";
            $str .= "<td>{$rt['chain_sex']}</td>";
            $str .= "<td>{$rt['chain_num']}</td>";
            $str .= "<td>{$rt['chain_department']}</td>";
            $str .= "<td>{$rt['chain_lesion']}</td>";
            $str .= "<td>{$rt['chain_file']}</td>";
            $str .= "<td>{$rt['chain_time']}</td>";
            $str .= "<td>{$rt['chain_status']}</td>";
            $str .= "<td>{$rt['chain_content']}</td>";
            $str .= "<td>{$rt['chain_modify']}</td>";
            $str .= "<td>{$rt['chain_chtime']}</td>";
            $str .= "<td>{$rt['health_insurance']}</td>";
            $str .= "<td>{$rt['archives_modify']}</td>";
            $str .= "</tr>\n";
        }
        $str .= "</table></body></html>";
        header( "Content-type:application/vnd.ms-excel;name='excel'");
        header( "Content-type: application/octet-stream" );
        header( "Content-Disposition: attachment; filename=".$filename );
        header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
        header( "Pragma: no-cache" );
        header( "Expires: 0" );
        exit( $str );

    }

}