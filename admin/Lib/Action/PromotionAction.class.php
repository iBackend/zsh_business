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
	
	
	//发货
	public function deliveryGoods()
	{
		$sql = "update ". DB_PREFIX."deal_order set order_status=2 where id=".$_REQUEST['order_id'];
		$GLOBALS['db']->query($sql);
		$this->success("发货成功！",true);
	}
	
	//拒绝
	public function rejectOrder()
	{
		$sql = "update ". DB_PREFIX."deal_order set order_status=0 where id=".$_REQUEST['order_id'];
		$GLOBALS['db']->query($sql);
		$this->success("拒绝成功！",true);
	}

	//修改
	public function editOrder()
	{
		$sql = "update ". DB_PREFIX."deal_order set total_price='".$_REQUEST['total_price']."' where id=".$_REQUEST['order_id'];
		$GLOBALS['db']->query($sql);
		$this->success("修改成功！",true);
	}
	
	//关闭订单
	public function closeOrder()
	{
		$sql = "update ". DB_PREFIX."deal_order set order_status=5 where id=".$_REQUEST['order_id'];
		$GLOBALS['db']->query($sql);
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
			$where.=" AND create_time>=".strtotime($filter['search_start'])." ";
		}
		if($filter['search_end']){
			$where.=" AND create_time<=".strtotime($filter['search_end'])." ";
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