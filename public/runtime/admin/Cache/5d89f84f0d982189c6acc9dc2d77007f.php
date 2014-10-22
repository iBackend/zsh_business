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
	    
	    <div class="mainindex">

	    	<div class="formbody">
    			<form class="form-horizontal" role="form" method="post" enctype="multipart/form-data">

			    <div class="formtitle"><span>店铺资料 - <?php echo $business_account; ?></span></div>
			    
			    <ul class="forminfo">
			    <li><label>商户名称</label><input name="name" value="<?php echo $business_account; ?>" type="text" class="dfinput" /><i></i></li>
			    <li><label>商户地址</label><input name="address" value="<?php echo $business_info['address']; ?>" type="text" class="dfinput" /><i></i></li>
			    <li><label>商户图片</label>
			    	<input name="preview" type="file">
			    	<div><img style="max-width:150px;" src="<?php echo $business_info['preview']; ?>"></div>
			    </li>
			    <li><label>联系电话</label><input name="tel" value="<?php echo $business_info['tel']; ?>" type="text" class="dfinput" /><i></i></li>

			    <li><label>联系人</label><input name="contact" value="<?php echo $business_info['contact']; ?>" type="text" class="dfinput" /><i></i></li>

			    <li><label>营业时间</label><input name="open_time" value="<?php echo $business_info['open_time']; ?>" type="text" class="dfinput" /><i>手动输入营业时间</i></li>

			    <li><label>简要说明</label><input name="brief" value="<?php echo $business_info['brief']; ?>" type="text" class="dfinput" /><i></i></li>

			    <li><label>城市</label>
			    	<select class="form-control dfinput" name="city_id">
												  <?php foreach ($citys as $key => $value): ?>
												  	<option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
												  <?php endforeach; ?>
												</select>
			    	<i></i></li>

		    	<li><label>标签</label><input name="tags" value="<?php echo $business_info['tags']; ?>" type="text" class="dfinput" /><i></i></li>

		    	<li><label>SEO标题</label><input name="seo_title" value="<?php echo $business_info['seo_title']; ?>" type="text" class="dfinput" /><i></i></li>

		    	<li><label>SEO关键字</label><textarea name="seo_keyword" cols="" rows="" class="textinput"><?php echo $business_info['seo_keyword']; ?></textarea></li>

		    	<li><label>SEO描述</label><textarea name="seo_description" cols="" rows="" class="textinput"><?php echo $business_info['seo_description']; ?></textarea></li>

			    <li><label>手机显示简介</label><input name="mobile_brief" value="<?php echo $business_info['mobile_brief'];; ?>" type="text" class="dfinput" /><i></i></li>
			
			    <li><label>&nbsp;</label><input name="" type="submit" class="btn" value="确认保存"/>
			    <input type="reset" class="btn btn-danger" value="重 置"></li>
			    
			    </ul>
			    </form>
			    
			    </div>

	    </div>
</div>

</body>

</html>