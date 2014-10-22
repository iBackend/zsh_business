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
				<?php if ($_SERVER['REQUEST_URI'] == $value['url'].'&' || $_SERVER['REQUEST_URI'] == $value['url']): ?>
					
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
	    <link rel="stylesheet" type="text/css" href="__TMPL__Common/css/nec.css">
	    <div class="mainindex" style="width:80%;position:relative;left:-100px;">
	    	
	    	<div class="welinfo">
	    		<a href="/index.php?m=Sysconf&a=addpayment" class="u-btn u-btn-c5" style="color:white">增加支付信息</a>
	    	</div>

	    	<table class="m-table">
	    	    <thead>
	    	        <tr>
	    	        	<th>支付名称</th>
	    	        	<th>描述</th>
	    	        	<th>在线支付</th>
	    	        	<th>操作</th>
	    	        </tr>
	    	    </thead>
	    	    <tbody>
	    	        <?php if(is_array($pay)): $i = 0; $__LIST__ = $pay;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><tr>
	    	        		<td><?php echo ($vo["name"]); ?></td>
	    	        		<td><?php echo ($vo["description"]); ?></td>
	    	        		<td>
	    	        			<?php if ($vo['online_pay'] == 0): ?>
	    	        				否
	    	        			<?php else: ?>
	    	        				是
	    	        			<?php endif; ?>
	    	        		</td>
	    	        		<td>
	    	        			<a href="/index.php?m=Sysconf&a=deletepayment&id=<?php echo ($vo["id"]); ?>" class="u-btn u-btn-c1">删除</a>
	    	        			<a href="/index.php?m=Sysconf&a=editpayment&id=<?php echo ($vo["id"]); ?>" class="u-btn u-btn-c5">编辑</a>
	    	        		</td>
	    	        	</tr><?php endforeach; endif; else: echo "" ;endif; ?>
	    	    </tbody>
	    	</table>
	    </div>
</div>

</body>

</html>