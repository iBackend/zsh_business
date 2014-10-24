<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class BaseAction extends Action{
	//后台基础类构造
	protected $lang_pack;
	public function __construct()
	{
		parent::__construct();
		check_install();
		//重新处理后台的语言加载机制，后台语言环境配置于后台config.php文件
		$langSet = conf('DEFAULT_LANG');			       	
		// 定义当前语言
		define('LANG_SET',strtolower($langSet));
		 // 读取项目公共语言包
		if (is_file(LANG_PATH.$langSet.'/common.php'))
		{
			L(include LANG_PATH.$langSet.'/common.php');
			$this->lang_pack = require LANG_PATH.$langSet.'/common.php';
			
			if(!file_exists(APP_ROOT_PATH."public/runtime/admin/lang.js"))
			{
				$str = "var LANG = {";
				foreach($this->lang_pack as $k=>$lang)
				{
					$str .= "\"".$k."\":\"".$lang."\",";
				}
				$str = substr($str,0,-1);
				$str .="};";
				file_put_contents(APP_ROOT_PATH."public/runtime/admin/lang.js",$str);
			}
		}

		// 主菜单列表
		$main_menu = M('Menu')->where("`pid` = 0 and `tip` = 'business'")->select();
		//当前显示的菜单
		$current_menu = array();
		// 从菜单列表
		foreach ($main_menu as $key => $value) {
			$main_menu[$key]['below_menu'] = $this->getBelowMenu($value['id']);
			if (strstr($value['url'], MODULE_NAME)){
				$current_menu = $main_menu[$key];
			}
		}
		
		$urls = array();
		if($current_menu){
			$urls[] = $current_menu;
			foreach ($current_menu['below_menu'] as $key=>$value)
			{
				if ($_SERVER['REQUEST_URI'] == $value['url'].'&' || $_SERVER['REQUEST_URI'] == $value['url']){
					$urls[] = $value;
				}
			}
		}else {
			//显示首页
			$current_menu = array("title"=>'首页',"url"=>'/');
			$urls[] = $current_menu;
			$current_menu['below_menu'] = $main_menu;
		}
		$this->generate_url($urls);
		
		$this->assign('estatemenu1',$main_menu);
		$this->assign('currentmenu',$current_menu);
	}
	
	private function generate_url($url)
	{
		$html = '';
		for($i=0;$i<count($url);$i++){
			$html .= '<a href="'.$url[$i]['url'].'">'.$url[$i]['title'].'</a>';
			if($i<count($url)-1){
				$html .= '&gt;';
			}
		}
		$this->assign('url',$html);
	}

	// 获取从菜单
	private function getBelowMenu($value='')
	{
		$model = M('Menu');
		$list = $model->where("`pid` = {$value}")->select();
		return $list;
	}
	

	protected function error($message,$ajax = 0)
	{

		if(!$this->get("jumpUrl"))
		{
			if($_SERVER["HTTP_REFERER"]) $default_jump = $_SERVER["HTTP_REFERER"]; else $default_jump = u("Index/main");
			$this->assign("jumpUrl",$default_jump);
		}
		parent::error($message,$ajax);
	}
	protected function success($message,$ajax = 0)
	{

		if(!$this->get("jumpUrl"))
		{
			if($_SERVER["HTTP_REFERER"]) $default_jump = $_SERVER["HTTP_REFERER"]; else $default_jump = u("Index/main");
			$this->assign("jumpUrl",$default_jump);
		}
		parent::success($message,$ajax);
	}

	public function img(){
		$pic = strip_tags(stripslashes(trim($_GET['pic'])));
		$width = strip_tags(stripslashes(trim($_GET['w'])));
		$height = strip_tags(stripslashes(trim($_GET['h'])));

        $file_info  = pathinfo($pic);

        if((!empty($width)) and (!empty($height))){
            $thumb_img = $file_info["dirname"]."/".$file_info["filename"]."_{$width}_{$height}.".$file_info["extension"];
            if(!file_exists($thumb_img)){
                $Image = new Image();
                Image::thumb($pic, $thumb_img, "", $width, $height);    
                // $image = new Image(); 
                // $image->open($pic);
                // //将图片裁剪为400x400并保存为corp.jpg
                // $image->crop($width, $height)->save($thumb_img);
            }
            $fileres = file_get_contents($thumb_img); //FALSE if 404     
        }else{
            $fileres = file_get_contents($pic); //FALSE if 404            
        }
        header('Content-type: image/jpeg');
        echo $fileres;
    }
    
    
    
    
    /**
     * 分页的信息加入条件的数组
     *
     * @access  public
     * @return  array
     */
    public function page_and_size($filter)
    {
    	if(empty($filter['page_size'])){
    		if (isset($_REQUEST['page_size']) && intval($_REQUEST['page_size']) > 0)
    		{
    			$filter['page_size'] = intval($_REQUEST['page_size']);
    		}
    		elseif (isset($_COOKIE['ECSCP']['page_size']) && intval($_COOKIE['ECSCP']['page_size']) > 0)
    		{
    			$filter['page_size'] = intval($_COOKIE['ECSCP']['page_size']);
    		}
    		else
    		{
    			$filter['page_size'] = 25;
    		}
    	}
    
    	/* 每页显示 */
    	$filter['page'] = (empty($_REQUEST['page']) || intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);
    
    	/* page 总数 */
    	$filter['page_count'] = (!empty($filter['record_count']) && $filter['record_count'] > 0) ? ceil($filter['record_count'] / $filter['page_size']) : 1;
    
    	/* 边界处理 */
    	if ($filter['page'] > $filter['page_count'])
    	{
    		$filter['page'] = $filter['page_count'];
    	}
    
    	$filter['start'] = ($filter['page'] - 1) * $filter['page_size'];
    	return $filter;
    }
}
?>