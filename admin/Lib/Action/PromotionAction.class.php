<?php
// +----------------------------------------------------------------------
// | 掌生活
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.jiepool.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Eric(1023753031@qq.com)
// +----------------------------------------------------------------------


class PromotionAction extends CommonAction
{
	
	public function index()
	{
		redirect(U('redpaper'));
	}

	public function redpaper()
	{
		$this->display();
	}
	
	public function redpaper_list()
	{
		$list = $this->data_list();
		echo json_encode($list);
	}
	
	function createpaper(){
		if($_REQUEST['id']){
			$sql = "select * from ". DB_PREFIX."redpaper where id=".$_REQUEST['id'];
			$redpaper = $GLOBALS['db']->getRow($sql);
			
			$redpaper['begin_time'] = date('Y-m-d H:i:s', $redpaper['begin_time']);
			$redpaper['end_time'] = date('Y-m-d H:i:s', $redpaper['end_time']);
			
			$this->assign('redpaper',$redpaper);
		}
		$this->display();
	}
	
	
	public function redpaperSave()
	{
		$file_upload_flag = false;
		foreach($_FILES as $f){
			if($f['size']>0){
				$file_upload_flag = true;
				break;
			}
		}

		$img = '';
		if($file_upload_flag){
			$arr_img = $this->uploadImage();
			if($arr_img['status']==0)
			{
				$this->error($arr_img['info']);
			}
			$img = 'http://'.$_SERVER['HTTP_HOST'].$arr_img['data'][0]['recpath'].$arr_img['data'][0]['savename'];
		}
		
		if(!$_REQUEST['id']){
			$sql = "insert into ". DB_PREFIX."redpaper (name, genus, use_limit,
					 begin_time, end_time, point, img, type, business_id, remark, create_time, status) 
					values ('".$_REQUEST['name']."',
					'".$_REQUEST['genus']."',
					'".$_REQUEST['use_limit']."',
					'".strtotime($_REQUEST['begin_time'])."',
					'".strtotime($_REQUEST['end_time'])."',
					'".$_REQUEST['point']."',
					'".$img."',
					'2',
					'".$this->location_id."',
					'".$_REQUEST['remark']."',
					'".time()."',
					0
					) ";
			$msg = "创建红包成功！";
		}else {
			$sql = "update ". DB_PREFIX."redpaper set name='".$_REQUEST['name']."',
				genus='".$_REQUEST['genus']."',
				use_limit='".$_REQUEST['use_limit']."',
				begin_time='".strtotime($_REQUEST['begin_time'])."',
				end_time='".strtotime($_REQUEST['end_time'])."',
				point='".$_REQUEST['point']."',";
				
			if($img){
				$sql.="img='".$img."',";
			}
			$sql.="remark='".$_REQUEST['remark']."' where id=".$_REQUEST['id'];
			
			$msg = "修改红包成功！";
		}
		
		$GLOBALS['db']->query($sql);
		$this->success($msg);
	}
	
	public function deleteRedpaper()
	{
		$sql = "delete from ". DB_PREFIX."redpaper where id=".$_REQUEST['id'];
		$GLOBALS['db']->query($sql);
		$this->success("删除成功！",true);
	}
	
	
	public function deliveryRedpaper()
	{
		$sql = "update ". DB_PREFIX."redpaper set status=1 where id=".$_REQUEST['id'];
		$GLOBALS['db']->query($sql);
		$this->success("发放成功！",true);
	}
	
	
	/**
	 * @access  public
	 * @param
	 * @return void
	 */
	function data_list()
	{
		$filter = array();
		
		$filter['search_start'] = empty($_REQUEST['search_start']) ? ''     : trim($_REQUEST['search_start']);
		$filter['search_end'] = empty($_REQUEST['search_end']) ? ''     : trim($_REQUEST['search_end']);
		$filter['search_name'] = empty($_REQUEST['search_name']) ? ''     : trim($_REQUEST['search_name']);
		
		$filter['sort']    = empty($_REQUEST['sort'])    ? 'id' : trim($_REQUEST['sort']);
		$filter['order'] = empty($_REQUEST['order']) ? 'DESC'     : trim($_REQUEST['order']);
		$filter['page'] = empty($_REQUEST['page']) ? '1'     : trim($_REQUEST['page']);
		$filter['page_size']	= empty($_REQUEST['rows']) ? '25'     : trim($_REQUEST['rows']);
		
		
		$where = " WHERE type=2 and business_id='".$this->location_id."' ";//type: 1, 平台发放的红包；2，商家发放的红包；3，物业发放的红包；
		
		if($filter['search_start']){
			$where.=" AND begin_time>=".strtotime($filter['search_start'])." ";
		}
		if($filter['search_end']){
			$where.=" AND end_time<=".strtotime($filter['search_end'])." ";
		}
		if($filter['search_name']){
			$where.=" AND name LIKE '%".$filter['search_name']."%' ";
		}
		
		
		$sql = "SELECT COUNT(*) FROM " . DB_PREFIX."redpaper".$where;
		$filter['record_count'] = $GLOBALS['db']->getOne($sql);

		/* 分页大小 */
		$filter = $this->page_and_size($filter);
		
		$sql = "SELECT * ".
                " FROM " . DB_PREFIX."redpaper".$where.
                " ORDER by " . $filter['sort'] . ' ' . $filter['order'] .
                " LIMIT " . $filter['start'] . ',' . $filter['page_size'];
	
 		$list = $GLOBALS['db']->getAll($sql);

		// 带上附加字段
		foreach ($list as $key => $value) {
			$list[$key]['create_time'] = date('Y-m-d H:i:s', $value['create_time']);
		}
	
		$arr = array('rows' => $list, 'filter' => $filter,
	        'page' => $filter['page_count'], 'total' => $filter['record_count']);
	
		return $arr;
	}
	

}
?>