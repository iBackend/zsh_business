<?php
// +----------------------------------------------------------------------
// | 掌生活
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.jiepool.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Eric(1023753031@qq.com)
// +----------------------------------------------------------------------


class CommentAction extends CommonAction
{
	
	public function index()
	{
		redirect(U('commentlist'));
	}

	public function commentlist()
	{
		$this->display();
	}
	
	public function comments_list()
	{
		$list = $this->comment_list();
		echo json_encode($list);
	}

	public function answer()
	{
		$comment = D('message')->where("`id` = {$_REQUEST['id']}")->select();
		if(!$comment){
			$this->error('您所选择的评论不存在！请在评论列表中选中评论进行回复！');
			return;
		}
// 		$model = M('user');
		$comment[0]['user_name'] = M('user')->where("`id` = {$comment[0]['user_id']}")->getField('user_name');
// 		echo $model->getLastSql();

		if($comment[0]['admin_reply']){
			$this->assign('had_reply',1);
		}
		
		$this->assign('comment',$comment[0]);
		$this->display();
	}
	
	function reply(){
		$sql = "update ".DB_PREFIX."message set admin_reply='".$_REQUEST['admin_reply']."'  where id=".$_REQUEST['id'];
		$GLOBALS['db']->query($sql);
		$this->success("回复成功！");
	}
	
	
	/**
	* @access  public
	* @param
	* @return void
	*/
	function comment_list(){
		$filter = array();
	
		$filter['search_start'] = empty($_REQUEST['search_start'])    ? '' : trim($_REQUEST['search_start']);
		$filter['search_end'] = empty($_REQUEST['search_end']) ? ''     : trim($_REQUEST['search_end']);
		$filter['search_reply'] = isset($_REQUEST['search_reply'])? trim($_REQUEST['search_reply']):'';
		$filter['search_user_name'] = empty($_REQUEST['search_user_name']) ? ''     : trim($_REQUEST['search_user_name']);
	
		$filter['sort']    = empty($_REQUEST['sort'])    ? 'id' : trim($_REQUEST['sort']);
		$filter['order'] = empty($_REQUEST['order']) ? 'DESC'     : trim($_REQUEST['order']);
		$filter['page'] = empty($_REQUEST['page']) ? '1'     : trim($_REQUEST['page']);
		$filter['page_size']	= empty($_REQUEST['rows']) ? '25'     : trim($_REQUEST['rows']);
	
		$bid = $this->bid;
		$location = M('supplier_account_location_link')->where("`account_id` = {$bid}")->find();
		$location_id = $location['location_id'];
		$where = " WHERE pid IN ($location_id) ";
		if($filter['search_start']){
			$where.=" AND create_time>=".strtotime($filter['search_start'])." ";
		}
		if($filter['search_end']){
			$where.=" AND create_time<=".strtotime($filter['search_end'])." ";
		}
		if($filter['search_user_name']){
			$where.=" AND user_id IN (SELECT id FROM " . DB_PREFIX."user WHERE user_name LIKE '%".$filter['search_user_name']."%' )  ";
		}
		if(isset($_REQUEST['search_reply']) && $filter['search_reply']!=''){
			if($filter['search_reply']==1){
				$where.=" AND admin_reply!='' ";
			}else {
				$where.=" AND admin_reply='' ";
			}
		}
	
		$sql = "SELECT COUNT(*) FROM " . DB_PREFIX."message".$where;
		$filter['record_count'] = $GLOBALS['db']->getOne($sql);
	
		/* 分页大小 */
		$filter = $this->page_and_size($filter);
	
		$sql = "SELECT * ".
					" FROM " . DB_PREFIX."message".$where.
					" ORDER by " . $filter['sort'] . ' ' . $filter['order'] .
					" LIMIT " . $filter['start'] . ',' . $filter['page_size'];
	
		$list = $GLOBALS['db']->getAll($sql);
		foreach ($list as $key => $value) {
			$list[$key]['user_name'] = M('user')->where("`id` = {$value['user_id']}")->getField('user_name');
			$list[$key]['create_time'] = date('Y-m-d H:i:s', $value['create_time']);
			$list[$key]['reply_flag'] = $value['admin_reply']?1:0;
		}
	
		$arr = array('rows' => $list, 'filter' => $filter, 'page' => $filter['page_count'], 'total' => $filter['record_count']);
		return $arr;
	}

}
?>