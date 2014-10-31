<?php
// +----------------------------------------------------------------------
// | 掌生活
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.jiepool.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Eric(1023753031@qq.com)
// +----------------------------------------------------------------------


class IntegritygoldAction extends CommonAction
{
	
	public function index()
	{
		redirect(U('compensate'));
	}

	public function compensate()
	{
		$bid = $this->bid;
		$rel_model = 'admin';
		$model = D('Compensate');
		$map['rel_model'] = $rel_model;
		$map['rel_id'] = $bid;
		$list = $model->where($map)->select();
		foreach ($list as $key => $value) {
			$list[$key]['user_name'] = M('User')->where("`id` = {$value['user_id']}")->getField('user_name');
			$list[$key]['user_phone'] = M('User')->where("`id` = {$value['user_id']}")->getField('mobile');
			$list[$key]['order_sn'] = M('DealOrder')->where("`id` = {$value['order_id']}")->getField('order_sn');
			switch ($value['status']) {
				case '1':
					$list[$key]['status'] = '用户已申请';
					break;

				case '2':
					$list[$key]['status'] = '商家已受理';
					break;

				case '3':
					$list[$key]['status'] = '商家已拒绝';
					break;

				case '0':
					$list[$key]['status'] = '处理成功';
					break;
				
				default:
					$list[$key]['status'] = '非正常状态';
					break;
			}
		}
		$this->assign('list',$list);
		$this->display();
	}

	public function record()
	{
		$this->display();
	}
	
	function record_list(){
		$list = $this->data_list();
		echo json_encode($list);
	}

	public function addgold()
	{
		$this->display();
	}

	// 拒绝赔偿
	public function refused_compen()
	{
		$model = D('Compensate');
		$id = $_GET['id'];
		$result = $model->where("`id` = {$id}")->setField('status',3);
		$this->success('已成功拒绝');
	}

	// 先赔
	public function do_compen()
	{
		$model = D('Compensate');
		$id = $_GET['id'];
		$result = $model->where("`id` = {$id}")->setField('status',4);
		$this->success('已成功处理');
	}

	function data_list(){
		$filter = array();
		
		$filter['search_start'] = empty($_REQUEST['search_start']) ? ''     : trim($_REQUEST['search_start']);
		$filter['search_end'] = empty($_REQUEST['search_end']) ? ''     : trim($_REQUEST['search_end']);
		$filter['search_genus'] = isset($_REQUEST['search_genus']) ? $_REQUEST['search_genus']     : '';
		$filter['search_keyword'] = empty($_REQUEST['search_keyword']) ? ''     : trim($_REQUEST['search_keyword']);
		
		$filter['sort']    = empty($_REQUEST['sort'])    ? 'id' : trim($_REQUEST['sort']);
		$filter['order'] = empty($_REQUEST['order']) ? 'DESC'     : trim($_REQUEST['order']);
		$filter['page'] = empty($_REQUEST['page']) ? '1'     : trim($_REQUEST['page']);
		$filter['page_size']	= empty($_REQUEST['rows']) ? '25'     : trim($_REQUEST['rows']);
		
		$where = " WHERE location_id='".$this->location_id."' ";
		
		if($filter['search_start']){
			$where.=" AND create_time>=".strtotime($filter['search_start'])." ";
		}
		if($filter['search_end']){
			$where.=" AND create_time<=".strtotime($filter['search_end'])." ";
		}
		if($filter['search_keyword']){
			$where.=" AND remarks LIKE '%".$filter['search_keyword']."%' ";
		}
		if(isset($_REQUEST['search_genus']) && $filter['search_genus']!=''){
			$where.=" AND genus=".($filter['search_genus'])." ";
		}
		
		$sql = "SELECT COUNT(*) FROM " . DB_PREFIX."compenprocess".$where;
		$filter['record_count'] = $GLOBALS['db']->getOne($sql);
		
		/* 分页大小 */
		$filter = $this->page_and_size($filter);
		
		$sql = "SELECT * ".
		                " FROM " . DB_PREFIX."compenprocess".$where.
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