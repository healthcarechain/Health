<?php
if (!session_id()) session_start();
  if(session('user_id')){
    $user_id = session('user_id');
  }else{
    $user_id = "";
  }
?>
<!--<link href="/web/Home/css/css.css" type="text/css" rel="stylesheet" />-->
<header>
<!--    {:\\think\\Url::build()}-->
    <link rel="shortcut icon" href="admin/homes/images/title.ico"/>
  <div class="header-top min-width">
    <div class="container fn-clear"> <strong class="fn-left">技术咨询：888-888-8888<span class="s-time">服务时间：9：00-17：00</span></strong>
      <ul class="header_contact">
        <li class="c_1"> <a class="ico_head_weixin" id="wx"></a>
          <div class="ceng" id="weixin_xlgz" style="display: none;">
            <div class="cnr"> <img src="admin/homes/images/code.png"> </div>
            <b class="ar_up ar_top"></b> <b class="ar_up_in ar_top_in"></b> </div>
        </li>
        <li class="c_2"><a href="#" target="_blank" title="官方QQ" alt="官方QQ"><b class="ico_head_QQ"></b></a></li>
        <li class="c_4"><a href="#" target="_blank" title="新浪微博" alt="新浪微博"><b class="ico_head_sina"></b></a></li>
<!--          <li style="margin-top: -1px;"><span class="s-time">--><?//=$weather?><!--</span></li>-->
      </ul>
      <ul class="fn-right header-top-ul">
        <ul class="fn-right header-top-ul">
          <li> <a href="/" class="app">返回首页</a> </li>
          <?php if ($user_id != ""): ?>
            <li><div class=""><a href="/personal_index" class="" title="我的信息">{:\\think\\Session::get('user_name');}</a></div></li>
            <li><div class=""><a href="logout" class="js-logout" title="退出">退出</a></div></li>
          <?php else: ?>
            <li><div class=""><a href="/" class="js-login" title="登录">登录</a></div></li>
          <?php endif ?>
        </ul>
          <script type="text/javascript" charset="utf-8" src="http://static.bshare.cn/b/buttonLite.js#uuid=&amp;style=3&amp;fs=4&amp;textcolor=#fff&amp;bgcolor=#19D&amp;text=分享到&amp;pophcol=3"></script>

      </ul>
    </div>

  </div>
  <div class="header min-width">
    <div class="container">
      <div class="fn-left logo"> </div>
      <ul class="top-nav fn-clear">
        <li> <a href="/">首页</a> </li>
        <li> <a href="#">安全保障</a> </li>
        <li> <a href="#">关于我们</a> </li>
      </ul>
    </div>
  </div>
</header>
<input type="hidden" id="user_id" value="<?=$user_id?>">

<script src="admin/homes/script/msgTips.js" type="text/javascript"></script>
<script type="text/javascript">
  window.onload = function(){

    var user_id = $("#user_id").val();
    if( user_id == "" ){
      $("body").manhua_msgTips({
        Event : "hover",			//响应的事件
        timeOut : 4000,				//提示层显示的时间
        msg : "<a href='/'>您还没有登录呢，请先登录帐号吧！</a>",			//显示的消息
        speed : 300,				//滑动速度
        type : "warning"			//提示类型（1、success 2、error 3、warning）

      });
//      var sure1 = confirm("您还没有登录呢，要去登录吗？");
//      if(sure1){
//        location.href="/login";
//      }
    }else{
      $.ajax({
        type: "GET",
        url: "finger?user_id="+user_id,
        success: function(msg){
          if (msg){

            $("body").manhua_msgTips({
              Event : "hover",			//响应的事件
              timeOut : 4000,				//提示层显示的时间
              msg : "<a href='/account_settings'>您还没有完善信息，为了方便您的操作，请先完善信息吧！</a>",			//显示的消息
              speed : 300,				//滑动速度
              type : "warning"			//提示类型（1、success 2、error 3、warning）

            });
//            var sure2 = confirm("您还没有完善您的信息，是否现在去完善？");

//            if (sure2){
//              location.href="/account_settings";
//            }
          }
        }
      });
    }
  }
</script>