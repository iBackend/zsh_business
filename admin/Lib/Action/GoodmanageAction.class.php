<?php
// +----------------------------------------------------------------------
// | 掌生活
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.jiepool.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Eric(1023753031@qq.com)
// +----------------------------------------------------------------------


class GoodmanageAction extends CommonAction
{
	
	public function index()
	{
		redirect(U('goodlist'));
	}

	public function category()
	{
		$this->display();
	}
	
	public function category_list()
	{
		$list = $this->cate_list();
		echo json_encode($list);
	}

	//添加或者修改商品类型
	public function saveCate()
	{
		$bid = $this->bid;
		$id = empty($_REQUEST['id'])?0:intval($_REQUEST['id']);
		if($id==0){
			
			$model = D('ShopCate');
			if ($model->create()) {
				$model->pid = $bid;
				$model->is_effect = 1;
				if ($model->add()) {
					$this->success('商品类型添加成功', true);
				}else {
					$this->error('商品类型添加失败', true);
				}
			}else {
				$this->error('数据错误', true);
			}
			
		}else {
			
			$sql = "update ".DB_PREFIX."shop_cate 
				set name = '".$_REQUEST['name']."',sort='".$_REQUEST['sort']."' where id=".$id;
			$GLOBALS['db']->query($sql);
			$this->success('商品类型修改成功', true);
		}
	}
	
	
	//删除商品类型
	public function deleteCate()
	{
		$id = $_REQUEST['id'];
		$model = D('ShopCate');
		//判断此类型是否被使用
		$sql = "select count(1) from ".DB_PREFIX."deal where shop_cate_id=".$id;
		$count = $GLOBALS['db']->getOne($sql);
		if($count){
			$this->error('此类型已经被使用，不能够直接删除！', true);
			return;
		}
		$model->delete($id);
		$this->success('删除成功', true);
	}

	public function goodlist()
	{
		// 输出分类
		$cate_list = D('ShopCate')->where("`pid` = {$this->bid}")->select();
		$this->assign('cate_list',$cate_list);
		$this->display();
	}
	
	public function goods_list()
	{
		$list = $this->good_list();
		echo json_encode($list);
	}

	public function addgood()
	{
		// 商家id
		$bid = $this->bid;
		// 城市列表
		$city = D('AreaNew');
		$city_list = $city->order("`sort` DESC")->select();
		$city_tree = list_to_tree($city_list,$pk='id',$pid='pid',$child='_child',$root=0);
		$city_tree2 = tree_to_list2($city_tree);
		// 输出分类
		$cate_list = D('ShopCate')->where("`pid` = {$bid}")->select();
		$this->assign('cate_list',$cate_list);
		$this->assign('citys',$city_tree2);
		
		if($_REQUEST['id']){
			$goods = D('deal')->where("`id` = {$_REQUEST['id']}")->select();
			if(!$goods){
				$this->error('您所选择的商品不存在！请在商品列表中选中商品进行查看！');
			}
			$this->assign('goods',$goods[0]);
		}
		$this->display();
	}
	
	
	public function savegood(){
		// 处理图片
		$file_upload_flag = false;
		foreach($_FILES as $f){
			if($f['size']>0){
				$file_upload_flag = true;
				break;
			}
		}
			
		if($file_upload_flag){
			$arr_img = $this->uploadImage();
			if($arr_img['status']==0)
			{
				$this->error($arr_img['info']);
			}
			foreach ($arr_img['data'] as $key => $value) {
				$_POST[$value['key']] = 'http://'.$_SERVER['HTTP_HOST'].$value['recpath'].$value['savename'];
			}
			$_POST['icon'] = 'http://'.$_SERVER['HTTP_HOST'].$arr_img['data'][0]['recpath'].$arr_img['data'][0]['savename'];
		}
		
		$deal_model = D('Deal');
		$deal_model->create();
		if($_POST['id']){
			//update
			$result = $deal_model->save();
			
		}else {
			//insert
			// 商家商品关联
			$deal_model->supplier_id = $this->location_id;
			$deal_model->is_effect = 1;
			$deal_model->success_time = time();
			$deal_model->create_time = time();
			$result = $deal_model->add();
			if ($result) {
				// 写关联
				$bid = $this->bid;
				$location = M('supplier_account_location_link')->where("`account_id` = {$bid}")->find();
				$location_id = $location['location_id'];
				$datas['location_id'] = $location_id;
				$datas['deal_id'] = $result;
				M('deal_location_link')->add($datas);
			}
		}
		
		if ($result) {
			$this->success('商品发布成功');
		}else {
			$this->error('商品发布失败');
		}
		
	}
	
	
	/**
	 * 商品详情
	 */
	public function detailGood()
	{
// 		$goods = D('deal')->where("`id` = {$_REQUEST['id']}")->select();
// 		if(!$goods){
// 			$this->error('您所选择的商品不存在！请在商品列表中选中商品进行查看！');
// 		}
		$goods = $this->getGoods($_REQUEST['id']);
		
		// 商家id
		$bid = $this->bid;
		// 城市列表
		$city = D('AreaNew');
		$city_list = $city->order("`sort` DESC")->select();
		$city_tree = list_to_tree($city_list,$pk='id',$pid='pid',$child='_child',$root=0);
		$city_tree2 = tree_to_list2($city_tree);
		
		// 输出分类
		$cate_list = D('ShopCate')->where("`pid` = {$bid}")->select();
		$this->assign('cate_list',$cate_list);
		$this->assign('citys',$city_tree2);
		$this->assign('goods',$goods[0]);
		$this->assign('next_goods',$goods[1]);
		
		$this->display();
	}

	/**
	 * 删除商品
	 */
	function deleteGood()
	{
		$sql = "select * from ". DB_PREFIX."deal where id=".$_REQUEST['id'];
		$goods = $GLOBALS['db']->getRow($sql);
		if(!$goods){
			$this->error('您所选择删除的商品不存在！', true);
		}
		//删除商品与店铺的关系
		$sql = "delete from ". DB_PREFIX."deal_location_link where deal_id=".$goods['id'];
		$GLOBALS['db']->query($sql);
		//删除商品图片
		@unlink($goods['img']);
		@unlink($goods['icon']);
		
		//删除商品
		$sql = "delete from ". DB_PREFIX."deal where id=".$_REQUEST['id'];
		$GLOBALS['db']->query($sql);
		$this->success('商品“'.$goods['name'].'”删除成功！', true);
	}
	
	/**
	* 上架商品
	*/
	function upGood()
	{
		$sql = "update ". DB_PREFIX."deal set is_effect=1 where id=".$_REQUEST['id'];
		$GLOBALS['db']->query($sql);
		$this->success('商品上架成功！', true);
	}
	
	/**
	* 下架商品
	*/
	function downGood()
	{
		$sql = "update ". DB_PREFIX."deal set is_effect=0 where id=".$_REQUEST['id'];
		$GLOBALS['db']->query($sql);
		$this->success('商品下架成功！', true);
	}
	
	
	public function warehouse()
	{
		// 输出分类
		$cate_list = D('ShopCate')->where("`pid` = {$this->bid}")->select();
		$this->assign('cate_list',$cate_list);
		$this->display();
	}
	
	public function warehouse_list()
	{
		$_REQUEST['search_is_effect'] = 0;
		$list = $this->good_list();
		echo json_encode($list);
	}
	
	/**
	*  返回商品类型数据
	* @access  public
	* @param
	* @return void
	*/
	function cate_list()
	{
		$filter = array();
		$filter['sort']    = empty($_REQUEST['sort'])    ? 'id' : trim($_REQUEST['sort']);
		$filter['order'] = empty($_REQUEST['order']) ? 'DESC'     : trim($_REQUEST['order']);
		$filter['page'] = empty($_REQUEST['page']) ? '1'     : trim($_REQUEST['page']);
		$filter['page_size']	= empty($_REQUEST['rows']) ? '25'     : trim($_REQUEST['rows']);
		
		// 订单筛选
		$where = " WHERE pid IN (".$this->bid.")";
		$sql = "SELECT COUNT(*) FROM " . DB_PREFIX."shop_cate".$where;
		$filter['record_count'] = $GLOBALS['db']->getOne($sql);
	
			/* 分页大小 */
		$filter = $this->page_and_size($filter);
	
		$sql = "SELECT * ".
		" FROM " . DB_PREFIX."shop_cate".$where.
		" ORDER by " . $filter['sort'] . ' ' . $filter['order'] .
		" LIMIT " . $filter['start'] . ',' . $filter['page_size'];
		
		$list = $GLOBALS['db']->getAll($sql);
		$arr = array('rows' => $list, 'filter' => $filter, 'page' => $filter['page_count'], 'total' => $filter['record_count']);
		return $arr;
	}
	
	/**
	*  返回商品数据
	* @access  public
	* @param
	* @return void
	*/
	function good_list(){
		$filter = array();
		
		$filter['search_cate_id']    = empty($_REQUEST['search_cate_id'])    ? '' : trim($_REQUEST['search_cate_id']);
		$filter['search_name'] = empty($_REQUEST['search_name']) ? ''     : trim($_REQUEST['search_name']);
		$filter['search_is_effect'] = isset($_REQUEST['search_is_effect'])? trim($_REQUEST['search_is_effect']):'';
		
		$filter['sort']    = empty($_REQUEST['sort'])    ? 'id' : trim($_REQUEST['sort']);
		$filter['order'] = empty($_REQUEST['order']) ? 'DESC'     : trim($_REQUEST['order']);
		$filter['page'] = empty($_REQUEST['page']) ? '1'     : trim($_REQUEST['page']);
		$filter['page_size']	= empty($_REQUEST['rows']) ? '25'     : trim($_REQUEST['rows']);
		
		$bid = $this->bid;
		$location = M('supplier_account_location_link')->where("`account_id` = {$bid}")->find();
		$location_id = $location['location_id'];

		$where = " WHERE id IN (select deal_id from ".DB_PREFIX."deal_location_link where location_id=$location_id) ";
		if($filter['search_cate_id']){
			$where.=" AND shop_cate_id=".$filter['search_cate_id']." ";
		}
		if($filter['search_name']){
			$where.=" AND name LIKE '%".$filter['search_name']."%' ";
		}
		if(isset($_REQUEST['search_is_effect']) && $filter['search_is_effect']!=''){
			$where.=" AND is_effect =".$_REQUEST['search_is_effect']." ";
		}
		
		$sql = "SELECT COUNT(*) FROM " . DB_PREFIX."deal".$where;
		$filter['record_count'] = $GLOBALS['db']->getOne($sql);
		
		/* 分页大小 */
		$filter = $this->page_and_size($filter);
		
		$sql = "SELECT * ".
				" FROM " . DB_PREFIX."deal".$where.
				" ORDER by " . $filter['sort'] . ' ' . $filter['order'] .
				" LIMIT " . $filter['start'] . ',' . $filter['page_size'];
		
		$list = $GLOBALS['db']->getAll($sql);
		foreach ($list as $key => $value) {
			$list[$key]['category'] = M('ShopCate')->where("`id` = {$value['shop_cate_id']}")->getField('name');
			$list[$key]['is_effect_val'] = $value['is_effect']?"上架":"下架";
		}
		
		$arr = array('rows' => $list, 'filter' => $filter, 'page' => $filter['page_count'], 'total' => $filter['record_count']);
		return $arr;
	}
			
	
	function getGoods($id=''){
		$bid = $this->bid;
		$location = M('supplier_account_location_link')->where("`account_id` = {$bid}")->find();
		$location_id = $location['location_id'];
	
		$where = " WHERE id IN (select deal_id from ".DB_PREFIX."deal_location_link where location_id=$location_id) ";
		if($id){
			$where .= " AND id>='".$id."'";
		}
		
		$sql = "SELECT * ".
							" FROM " . DB_PREFIX."deal".$where.
							" ORDER by id limit 2 ";
		$list = $GLOBALS['db']->getAll($sql);
		return $list;
	}
}
?>