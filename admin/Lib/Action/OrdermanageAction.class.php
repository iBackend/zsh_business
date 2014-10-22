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
			$order_list[$key]['order_status'] = L('PAY_STATUS_'.$value['pay_status']).'';
		}
		
		$this->assign('order',$order_list);
		$this->display();
	}

	public function ordercensus()
	{
		$this->display();
	}

	public function check()
	{
		$this->display();
	}

}
?>