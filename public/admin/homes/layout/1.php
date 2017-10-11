<header>
  <div class="header-top min-width">
    <div class="container fn-clear"> <strong class="fn-left">咨询热线：400-668-6698<span class="s-time">服务时间：9:00 - 18:00</span></strong>
      <ul class="header_contact">
        <li class="c_1"> <a class="ico_head_weixin" id="wx"></a>
          <div class="ceng" id="weixin_xlgz" style="display: none;">
            <div class="cnr"> <img src="/web/Home/images/code.png"> </div>
            <b class="ar_up ar_top"></b> <b class="ar_up_in ar_top_in"></b> </div>
        </li>
        <li class="c_2"><a href="#" target="_blank" title="官方QQ" alt="官方QQ"><b class="ico_head_QQ"></b></a></li>
        <li class="c_4"><a href="#" target="_blank" title="新浪微博" alt="新浪微博"><b class="ico_head_sina"></b></a></li>
      </ul>
      <ul class="fn-right header-top-ul">
      <!--你好  欢迎来到赚的多-->
   
      <?php if(isset($_SESSION['user_id'])){
         echo '<li> <a href="/" class="app">返回首页</a> </li>' ;
         echo "欢迎"."&nbsp;&nbsp;".$_SESSION['user_id']."&nbsp;&nbsp;". "来到赚的多"."&nbsp;&nbsp;&nbsp;&nbsp;"; 
         echo '<a href="/" class="">退出</a>' ;

        
      }
      else
      {
      echo '<li> <a href="/" class="app">返回首页</a> </li>' ;
      echo ' <li>
          <div class=""><a href="register" class="c-orange" title="免费注册">免费注册</a></div>
        </li>'; 
        echo ' <li>
          <div class=""><a href="login" class="js-login" title="登录">登录</a></div>
        </li>'; 
      }
      ?>
      
        
       
       
      </ul>
    </div>
  </div>
  <div class="header min-width">
    <div class="container">
      <div class="fn-left logo"> <a class="" href="index"> <img src="/web/Home/images/logo.png"  title="" > </a> </div>
      <ul class="top-nav fn-clear">
        <li class="on"> <a href="/">首页</a> </li>
        <li> <a href="list" class="">我要投资</a> </li>
        <li> <a href="loan_index" class="">我要借款</a> </li>
        <li> <a href="Security">安全保障</a> </li>
        <li class="top-nav-safe"> <a href="personal_index">我的账户</a> </li>
        <li> <a href="Company_profile">关于我们</a> </li>
      </ul>
    </div>
  </div>
</header>