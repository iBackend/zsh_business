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
		// 商家账号
		$bid = $this->bid;
		// 店铺ID
		$location_id = M('supplier_account_location_link')->where("`account_id` = {$bid}")->getField('location_id');
		// 商品组
		$arr_deal_ids = D('deal_location_link')->where("`location_id` = {$location_id}")->select();
		foreach ($arr_deal_ids as $key => $value) {
			$arr_deal_ids_end[] = $value['deal_id'];
		}
		// 订单筛选
		$where['deal_ids'] = array("IN",$arr_deal_ids_end);
		$order_list = M("deal_order")->where($where)->order("`id` desc")->select();
		// 带上附加字段
		foreach ($order_list as $key => $value) {
			$order_list[$key]['good_name'] = M('deal')->where("`id` = {$value['deal_ids']}")->getField('name');
			$order_list[$key]['pay_status'] = L('PAY_STATUS_'.$value['pay_status']).'';
			$order_list[$key]['order_status'] = L('ORDER_STATUS_'.$value['order_status']).'';
		}
		
		$this->assign('order',$order_list);
		$this->display();
	}
	
	// 首页显示的订单列表
	public function orderlist_startPage()
	{
		$order_list = $this->order_list();
		echo json_encode($order_list);
	}
	
	//发货
	public function deliveryGoods()
	{
		$sql = "update ". DB_PREFIX."deal_order set order_status=2 where id=".$_REQUEST['order_id'];
		$GLOBALS['db']->query($sql);
		
		$this->success("发货成功！",true);
	}

	//关闭订单
	public function closeOrder()
	{
		$sql = "update ". DB_PREFIX."deal_order set order_status=0 where id=".$_REQUEST['order_id'];
		$GLOBALS['db']->query($sql);
		$this->success("关闭订单成功！",true);
	}
	
	public function ordercensus()
	{
		$this->display();
	}

	public function check()
	{
		$this->display();
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
			$list[$key]['total_price'] = '￥'.$value['total_price'];
		}
	
		$arr = array('rows' => $list, 'filter' => $filter,
	        'page' => $filter['page_count'], 'total' => $filter['record_count']);
	
		return $arr;
	}

}
?>