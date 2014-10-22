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
		$bid = $this->bid;
		$shop_cate = D('ShopCate');
		$map['pid'] = $bid;
		$this->_list($shop_cate,$map,'sort');
		$this->display();
	}

	// 增加类目
	public function addcategory()
	{
		$bid = $this->bid;
		if ($_POST) {
			$model = D('ShopCate');
			if ($model->create()) {
				$model->pid = $bid;
				$model->is_effect = 1;
				if ($model->add()) {
					$this->success('商品类目添加成功');
				}else {
					$this->error('商品类目添加失败');
				}
			}else {
				$this->error('数据错误');
			}
		}else {
			$this->display();
		}
	}

	public function dcategory()
	{
		$id = $_GET['id'];
		$model = D('ShopCate');
		$model->delete($id);
		$this->success('删除成功');
	}

	public function goodlist()
	{
		$bid = $this->bid;
		$location = M('supplier_account_location_link')->where("`account_id` = {$bid}")->find();
		$location_id = $location['location_id'];
		// 商品关联表
		$deal_list = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."deal_location_link a LEFT JOIN ".DB_PREFIX."deal b ON a.`deal_id` = b.`id` WHERE a.`location_id` = ".$location_id." order by b.`id` desc");
		foreach ($deal_list as $key => $value) {
			$deal_list[$key]['shop_cate_id'] = M('ShopCate')->where("`id` = {$value['shop_cate_id']}")->getField('name');
		}

		// 输出分类
		$cate_list = D('ShopCate')->where("`pid` = {$bid}")->select();
		$this->assign('cate_list',$cate_list);
		$this->assign('deal_list',$deal_list);
		$this->display();
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

		if ($_POST) {
			// 处理图片
			$arr_img = $this->uploadImage();
			foreach ($arr_img['data'] as $key => $value) {
				$_POST[$value['key']] = 'http://'.$_SERVER['HTTP_HOST'].$value['recpath'].$value['savename'];
			}
			// 商家商品关联
			$supplier_id = D('supplier_account_location_link')->where("`account_id` = {$bid}")->getField('location_id');
			$deal_model = D('Deal');
			$deal_model->create();
			$deal_model->supplier_id = $supplier_id;
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
				$this->success('商品发布成功');
			}else {
				$this->error('商品发布失败');
			}

		}else {
			// 输出分类
			$cate_list = D('ShopCate')->where("`pid` = {$bid}")->select();
			$this->assign('cate_list',$cate_list);
			$this->assign('citys',$city_tree2);
			$this->display();
		}
		
	}

	public function warehouse()
	{
		$this->display();
	}

}
?>