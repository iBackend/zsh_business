<?php
// +----------------------------------------------------------------------
// | 掌生活
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.jiepool.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Eric(1023753031@qq.com)
// +----------------------------------------------------------------------


class OrdermanageAction extends CommonAction
{
	
	public function index()
	{
		redirect(U('orderlist'));
	}

	// 订单列表
	public function orderlist()
	{
		$this->display();
	}
	
	// 首页显示的订单列表
	public function orders_list()
	{
		$order_list = $this->order_list();
		echo json_encode($order_list);
	}
	
	//发货
	public function deliveryGoods()
	{
		$sql = "update ". DB_PREFIX."deal_order set order_status=2 where id=".$_REQUEST['order_id'];
		if($GLOBALS['db']->query($sql)){
			$this->orderLog("商家已发货", $_REQUEST['order_id']);
			$this->success("发货成功！",true);
		}else {
			$this->error("发货失败！",true);
		}
	}
	
	//拒绝
	public function rejectOrder()
	{
		$sql = "update ". DB_PREFIX."deal_order set order_status=0 where id=".$_REQUEST['order_id'];
		$GLOBALS['db']->query($sql);
		$this->orderLog("商家已拒绝", $_REQUEST['order_id']);
		$this->success("拒绝成功！",true);
	}

	//修改
	public function editOrder()
	{
		$sql = "update ". DB_PREFIX."deal_order set total_price='".$_REQUEST['total_price']."' where id=".$_REQUEST['order_id'];
		$GLOBALS['db']->query($sql);	
		$this->orderLog("商家修改订单总价为".$_REQUEST['total_price'], $_REQUEST['order_id']);
		$this->success("修改成功！",true);
	}
	
	//关闭订单
	public function closeOrder()
	{
		$sql = "update ". DB_PREFIX."deal_order set order_status=5 where id=".$_REQUEST['order_id'];
		$GLOBALS['db']->query($sql);
		$this->orderLog("商家已关闭", $_REQUEST['order_id']);
		$this->success("关闭订单成功！",true);
	}
	
	
	/**
	* 商品详情
	*/
	public function detailOrder()
	{
		$order = $this->getOrder($_REQUEST['id']);
		$this->assign('order',$order[0]);
		$this->assign('order_item',$order[0]['item']);
		$this->assign('next_order',$order[1]);
		$this->display();
	}
		
	/**
	 * 订单统计
	 */
	public function ordercensus()
	{
		if(!empty($_REQUEST['search_end']) && !empty($_REQUEST['search_start'])){
			
			$filter = array();
			$filter['search_start'] = trim($_REQUEST['search_start']);
			$filter['search_end'] = trim($_REQUEST['search_end']);
			
			$this->assign('search_start',$filter['search_start']);
			$this->assign('search_end',$filter['search_end']);
			
			// 商家账号
			$bid = $this->bid;
			// 店铺ID
			$location_id = M('supplier_account_location_link')->where("`account_id` = {$bid}")->getField('location_id');
					// 商品组
			$arr_deal_ids = D('deal_location_link')->where("`location_id` = {$location_id}")->select();
			$arr_deal_ids_end = array();
			foreach ($arr_deal_ids as $key => $value) {
				$arr_deal_ids_end[] = $value['deal_id'];
			}
			// 订单筛选
			$where = " WHERE deal_ids IN (".implode(",", $arr_deal_ids_end).")";
			
			if($filter['search_start']){
				$where.=" AND create_time>=".strtotime($filter['search_start'])." ";
			}
			if($filter['search_end']){
				$where.=" AND create_time<=".strtotime($filter['search_end'])." ";
			}
			
			$sql = "SELECT *  FROM " . DB_PREFIX."deal_order".$where;
			$list = $GLOBALS['db']->getAll($sql);
			
			/**
			 * 	已完成的订单：order_status=3
				未完成的订单：order_status!=3 && order_status!=0 
				取消的订单：order_status=0
			 */
			//已完成的订单
			$complement_number = 0;
			$complement_total_price = 0;
			
			//未完成的订单
			$uncomplement_number = 0;
			$uncomplement_total_price = 0;
			
			//关闭的订单
			$close_number = 0;
			$close_total_price = 0;
			
			foreach ($list as $key => $value) {
				if($value['order_status']==3){
					$complement_number++;
					$complement_total_price += $value['total_price'];
					
				}else if($value['order_status']==0){
					$close_number++;
					$close_total_price += $value['total_price'];
					
				}else{
					$uncomplement_number++;
					$uncomplement_total_price += $value['total_price'];
				}
			}
			
			$this->assign('complement_number',$complement_number);
			$this->assign('complement_total_price',$complement_total_price);
			$this->assign('uncomplement_number',$uncomplement_number);
			$this->assign('uncomplement_total_price',$uncomplement_total_price);
			$this->assign('close_number',$close_number);
			$this->assign('close_total_price',$close_total_price);
		}
		
		$this->display();
	}

	public function check()
	{
		$this->display();
	}
	
	function checkOrder()
	{
		$sql = "update " . DB_PREFIX."deal_order set type=1 where id=".$_REQUEST['id'];
		$GLOBALS['db']->query($sql);
		$this->success("账目核对成功！");
	}
	
	
	
	/**
	 *  返回订单列表数据
	 * @access  public
	 * @param
	 * @return void
	 */
	function order_list()
	{
		$filter = array();
		
		$filter['search_start'] = empty($_REQUEST['search_start']) ? ''     : trim($_REQUEST['search_start']);
		$filter['search_end'] = empty($_REQUEST['search_end']) ? ''     : trim($_REQUEST['search_end']);
		$filter['search_order_sn'] = empty($_REQUEST['search_order_sn']) ? ''     : trim($_REQUEST['search_order_sn']);
		$filter['search_user_name'] = empty($_REQUEST['search_user_name']) ? ''     : trim($_REQUEST['search_user_name']);
		$filter['search_checked'] = isset($_REQUEST['search_checked']) ? trim($_REQUEST['search_checked']):'';
		
		
		$filter['sort']    = empty($_REQUEST['sort'])    ? 'id' : trim($_REQUEST['sort']);
		$filter['order'] = empty($_REQUEST['order']) ? 'DESC'     : trim($_REQUEST['order']);
		$filter['page'] = empty($_REQUEST['page']) ? '1'     : trim($_REQUEST['page']);
		$filter['page_size']	= empty($_REQUEST['rows']) ? '25'     : trim($_REQUEST['rows']);
		
		// 商家账号
		$bid = $this->bid;
		// 店铺ID
		$location_id = M('supplier_account_location_link')->where("`account_id` = {$bid}")->getField('location_id');
		// 商品组
		$arr_deal_ids = D('deal_location_link')->where("`location_id` = {$location_id}")->select();
		$arr_deal_ids_end = array();
		foreach ($arr_deal_ids as $key => $value) {
			$arr_deal_ids_end[] = $value['deal_id'];
		}
		// 订单筛选
		$where = " WHERE deal_ids IN (".implode(",", $arr_deal_ids_end).")";
		
		if($filter['search_start']){
			$where.=" AND create_time>=".strtotime($filter['search_start'])." ";
		}
		if($filter['search_end']){
			$where.=" AND create_time<=".strtotime($filter['search_end'])." ";
		}
		if($filter['search_order_sn']){
			$where.=" AND order_sn LIKE '%".$filter['search_order_sn']."%' ";
		}
		if($filter['search_user_name']){
			$where.=" AND user_name LIKE '%".$filter['search_user_name']."%' ";
		}
		if(isset($_REQUEST['search_checked']) && $filter['search_checked']!=''){
			$where.=" AND type = '".$filter['search_checked']."' ";
		}
		
		$sql = "SELECT COUNT(*) FROM " . DB_PREFIX."deal_order".$where;
		$filter['record_count'] = $GLOBALS['db']->getOne($sql);

		/* 分页大小 */
		$filter = $this->page_and_size($filter);
		
		
		$sql = "SELECT * ".
                " FROM " . DB_PREFIX."deal_order".$where.
                " ORDER by " . $filter['sort'] . ' ' . $filter['order'] .
                " LIMIT " . $filter['start'] . ',' . $filter['page_size'];
	
 		$list = $GLOBALS['db']->getAll($sql);

		// 带上附加字段
		foreach ($list as $key => $value) {
			$list[$key]['good_name'] = M('deal')->where("`id` = {$value['deal_ids']}")->getField('name');
			$list[$key]['pay_status_val'] = L('PAY_STATUS_'.$value['pay_status']).'';
			$list[$key]['order_status_val'] = L('ORDER_STATUS_'.$value['order_status']).'';
			$list[$key]['create_time'] = date('Y-m-d H:i:s', $value['create_time']);//￥
			$list[$key]['total_price'] = sprintf("%.2f", $list[$key]['total_price']);
		}
	
		$arr = array('rows' => $list, 'filter' => $filter,
	        'page' => $filter['page_count'], 'total' => $filter['record_count']);
	
		return $arr;
	}
	
	
	function getOrder($id=''){
		// 商家账号
		$bid = $this->bid;
		// 店铺ID
		$location_id = M('supplier_account_location_link')->where("`account_id` = {$bid}")->getField('location_id');
		// 商品组
		$arr_deal_ids = D('deal_location_link')->where("`location_id` = {$location_id}")->select();
		$arr_deal_ids_end = array();
		foreach ($arr_deal_ids as $key => $value) {
			$arr_deal_ids_end[] = $value['deal_id'];
		}
		// 订单筛选
		$where = " WHERE deal_ids IN (".implode(",", $arr_deal_ids_end).")";
		
		if($id){
			$where .= " AND id>='".$id."'";
		}
		
		$sql = "SELECT * FROM " . DB_PREFIX."deal_order ".$where.
									" ORDER by id limit 2 ";
		$list = $GLOBALS['db']->getAll($sql);
		$list[0]['total_price'] = sprintf("%.2f", $list[0]['total_price']); 
		
		$items = $GLOBALS['db']->getAll("select * from " . DB_PREFIX."deal_order_item where order_id=".$list[0]['id']);
		foreach($items as $key=>$value){
			$items[$key]['unit_price'] = sprintf("%.2f", $items[$key]['unit_price']); 
			$items[$key]['total_price'] = sprintf("%.2f", $items[$key]['total_price']);
		}
		
		$list[0]['item'] = $items;
		return $list;
	}
	
	private function orderLog($info, $order_id){
		$row = $GLOBALS['db']->getRow("select * from ". DB_PREFIX."deal_order where id=".$order_id);
		$sql = "insert into ".DB_PREFIX."deal_order_log (log_info, log_time, order_id) values ('".$row["order_sn"].$info."', '".time()."', '".$order_id."')";
		return $GLOBALS['db']->query($sql);
	}

}
?>