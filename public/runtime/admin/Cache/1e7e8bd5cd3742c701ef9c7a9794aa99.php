<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>欢迎登录后台管理系统</title>
<link href="__TMPL__Common/css/style.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="__TMPL__Common/js/jquery.js"></script>
<script src="__TMPL__Common/js/cloud.js" type="text/javascript"></script>

<script type="text/javascript" src="__TMPL__Common/js/jquery.timer.js"></script>
<script type="text/javascript" src="__TMPL__Common/js/login.js"></script>

<script language="javascript">
	$(function(){
    $('.loginbox').css({'position':'absolute','left':($(window).width()-692)/2});
	$(window).resize(function(){  
    $('.loginbox').css({'position':'absolute','left':($(window).width()-692)/2});
    })  
});  
</script>

<script type="text/javascript">
    //定义JS语言
    var ADM_NAME_EMPTY = '<?php echo l("ADM_NAME_EMPTY");?>';
    var ADM_PASSWORD_EMPTY = '<?php echo l("ADM_PASSWORD_EMPTY");?>';
    var ADM_VERIFY_EMPTY = '<?php echo l("ADM_VERIFY_EMPTY");?>';
    function resetwindow()
    {
        if(top.location != self.location)
        {
            top.location.href = self.location.href;
            return 
        }
    }
    resetwindow();
</script>



</head>

<body style="background-color:#1c77ac; background-image:url(__TMPL__Common/images/light.png); background-repeat:no-repeat; background-position:center top; overflow:hidden;">



    <div id="mainBody">
      <div id="cloud1" class="cloud"></div>
      <div id="cloud2" class="cloud"></div>
    </div>  


<div class="logintop">    
    <span>欢迎登录商家后台管理界面平台</span>    
    <ul>
    <li><a href="#">回首页</a></li>
    <li><a href="#">帮助</a></li>
    <li><a href="#">关于</a></li>
    </ul>    
    </div>
    
    <form action="<?php echo u("Public/do_login");?>" method="post">
    <div class="loginbody">
    
    <span class="systemlogo"></span> 
       
    <div class="loginbox">
    
    <ul>
    <li><input name="adm_name" type="text" class="loginuser adm_name" placeholder="用户名" value="" onclick="JavaScript:this.value=''"/></li>
    <li>
    <input name="adm_password" type="password" placeholder="密码" class="loginpwd adm_password" style="width:120px" value="" onclick="JavaScript:this.value=''"/>
    <input name="adm_verify" placeholder="验证码" type="text" class="loginpwd adm_verify" style="width:80px" value="" onclick="JavaScript:this.value=''"/><img src="__ROOT__/verify.php"  id="verify" align="absmiddle" />
    </li>
    
    <li>
    <input name="" id="login_btn" type="button" class="loginbtn submit"   value="登录"    /><label id="login_msg"></label>
    </li>
    </ul>
    
    
    </div>
    
    </div>
    </form>
    
    
    
    <div class="loginbm">版权所有  2013  <a href="http://www.zsh580.com">www.zsh580.com</a>  </div>
	
    
</body>

</html>