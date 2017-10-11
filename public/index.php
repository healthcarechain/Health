<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
//echo 'Hello World';
// 定义应用目录
define('APP_PATH', __DIR__ . '/../apps/');
//define('EXTEND_PATH','../extend/');
define('EXTEND_PATH','../extend/');
//define('CONF_PATH',APP_PATH.'config/');
// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';

//$TopClient = require __DIR__.'/admin/Ali/top/TopClient.php';
//$TopClient = require __DIR__.'/admin/Ali/top/ResultSet.php';
//$TopClient = require __DIR__.'/admin/Ali/top/RequestCheckUtil.php';
//$AlibabaAliqinFcSmsNumSendRequest = require __DIR__.'/admin/Ali/top/request/AlibabaAliqinFcSmsNumSendRequest.php';