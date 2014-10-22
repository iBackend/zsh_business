<?php if (!defined('THINK_PATH')) exit();?><!-- 整个公共页头 -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo C('SYSTEM_TITLE');;?></title>
<link href="__TMPL__Common/css/style.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="__TMPL__Common/js/jquery.js"></script>
<script type="text/javascript">
$(function(){	
	//顶部导航切换
	$(".nav li a").click(function(){
		$(".nav li a.selected").removeClass("selected")
		$(this).addClass("selected");
	})	
})	
</script>


</head>

<body style="background:url(__TMPL__Common/images/topbg.gif) repeat-x;overflow-x:hidden">

    <div class="topleft">
    <a href="/"><img src="__TMPL__Common/images/logo.png" title="系统首页" /></a>
    </div>
<!-- 整个公共页头结束 -->
				
<!-- 主导航 -->
<div class="navbar-header pull-left" >
<!-- 导航 -->
<ul class="nav">
	<?php if(is_array($estatemenu1)): $i = 0; $__LIST__ = $estatemenu1;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><li><a href="<?php echo ($vo["url"]); ?>" class=""><img src="__TMPL__Common/images/icon0<?php echo ($key+1);?>.png"  /><h2><?php echo ($vo["title"]); ?></h2></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
	
</ul>
</div>
				
<!-- 主导航右边 -->
<div class="navbar-header pull-right" role="navigation">
	<div class="topright">    
<ul>
<li><span><img src="__TMPL__Common/images/help.png" title="帮助"  class="helpimg"/></span><a href="#">帮助</a></li>
<li><a href="#">关于</a></li>
<li><a href="/index.php?m=Public&a=do_loginout&">退出</a></li>
</ul>

<div class="user">
<span>欢迎Admin</span>
<i>消息</i>
<b>5</b>
</div>    

</div>
</div><!-- /.navbar-header -->

<!-- 左边列表 -->
<script type="text/javascript">
$(function(){	
	//导航切换
	$(".menuson li").click(function(){
		$(".menuson li.active").removeClass("active")
		$(this).addClass("active");
	});
	
	$('.title').click(function(){
		var $ul = $(this).next('ul');
		$('dd').find('ul').slideUp();
		if($ul.is(':visible')){
			$(this).next('ul').slideUp();
		}else{
			$(this).next('ul').slideDown();
		}
	});
})	
</script>
<dl class="leftmenu" style="position:absolute;top:88px;">
    
<dd>
<div class="title">
<span><img src="__TMPL__Common/images/leftico01.png" /></span>控制台
</div>
	<ul class="menuson">
    <?php foreach ($estatemenu1 as $key => $vo): ?>
		<?php if (strstr($vo['url'], MODULE_NAME)): ?>
			<?php foreach ($vo['below_menu'] as $key => $value): ?>
				<?php if ($_SERVER['REQUEST_URI'] == $value['url']): ?>
					
					<li class="active"><cite></cite><a href="<?php echo $value['url']; ?>"><?php echo $value['title']; ?></a><i></i></li>
				<?php else: ?>
					
					<li><cite></cite><a href="<?php echo $value['url']; ?>"><?php echo $value['title']; ?></a><i></i></li>
				<?php endif; ?>
			<?php endforeach; ?>
		<?php endif; ?>
	<?php endforeach; ?>
    </ul>    
</dd>

</dl>



<div style="position:absolute;top:88px;left:188px;width:100%;">
	<div class="place" style="height:36px;">
	    <span>位置：</span>
	    <ul class="placeul">
	    <li><a href="#">控制台</a></li>
	    </ul>
	    </div>
	    
	    <div class="mainindex">
	    

	    <div class="xline"></div>
	    
	    <ul class="iconlist">
	    
	    <li><img src="__TMPL__Common/images/ico01.png" /><p><a href="#">管理设置</a></p></li>
	    <li><img src="__TMPL__Common/images/ico02.png" /><p><a href="#">发布文章</a></p></li>
	    <li><img src="__TMPL__Common/images/ico03.png" /><p><a href="#">数据统计</a></p></li>
	    <li><img src="__TMPL__Common/images/ico04.png" /><p><a href="#">文件上传</a></p></li>
	    <li><img src="__TMPL__Common/images/ico05.png" /><p><a href="#">目录管理</a></p></li>
	    <li><img src="__TMPL__Common/images/ico06.png" /><p><a href="#">查询</a></p></li> 
	            
	    </ul>

	    </div>
</div>

</body>

</html>