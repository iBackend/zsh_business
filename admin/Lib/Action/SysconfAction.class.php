<?php
// +----------------------------------------------------------------------
// | 掌生活
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.jiepool.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Eric(1023753031@qq.com)
// +----------------------------------------------------------------------


class SysconfAction extends CommonAction
{
	
	public function index()
	{
		redirect(U('baseinfo'));
	}

	public function baseinfo()
	{
		$bid = $this->bid;
		$business = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."supplier_account_location_link a LEFT JOIN ".DB_PREFIX."supplier_location b ON a.`location_id` = b.`id` WHERE a.`account_id` = ".$bid);
		// 商家名
		$name = $business[0]['name'];
		$biz_id = $business[0]['id'];
		// 城市列表
		$city = D('AreaNew');
		$city_list = $city->order("`sort` DESC")->select();
		$city_tree = list_to_tree($city_list,$pk='id',$pid='pid',$child='_child',$root=0);
		// 
		// echo "<pre>";
		// var_dump($city_tree);
		// die();
		$city_tree2 = tree_to_list2($city_tree);

		if ($_POST) {
			$face = $this->uploadImage();
			
			$biz = D('supplier_location');
			$biz->create();
			$biz->id = $biz_id;
			if ($face['status'] == 1) {
				$biz->preview = 'http://'.$_SERVER['HTTP_HOST'].$face['data'][0]['recpath'].$face['data'][0]['savename'];
			}
			$re = $biz->save();
			if ($re) {
				$this->success('商户信息保存成功');
			}else {
				$this->error('商户信息保存失败');
			}
		}else {
			$this->assign('business_account',$name);
			$this->assign('business_info',$business[0]);
			$this->assign('citys',$city_tree2);
			$this->display();
		}

		
	}

	public function authinfo()
	{
		$bid = $this->bid;
		$location_id = D('supplier_account_location_link')->where("`account_id` = {$bid}")->getField('location_id');
		// 判断是不是服务到家商家
		$route = D('supplier_location')->where("`id` = {$location_id}")->getField('route');
		$this->assign('route',$route);
		$this->display();
	}

	public function payinfo()
	{
		$model = D('Payment');
		$bid = $this->bid;
		$list = $model->where("`adm_id` = {$bid}")->select();
		$this->assign('pay',$list);
		$this->display();
	}

	public function addpayment()
	{
		$bid = $this->bid;
		if ($_POST) {
			$model = D('Payment');
			if ($model->create()) {
				$model->adm_id = $bid;
				if ($model->add()) {
					$this->success('支付接口新增成功');
				}else {
					$this->error('支付接口新增失败');
				}
			}else {
				$this->error('支付接口新增失败');
			}
		}else {
			$this->display();
		}
	}

	public function editpayment()
	{
		$bid = $this->bid;
		$id = $_GET['id'];
		if ($_POST) {
			$model = D('Payment');
			if ($model->create()) {
				$model->adm_id = $bid;
				$model->id = $id;
				if ($model->save()) {
					$this->success('支付接口修改成功');
				}else {
					$this->error('支付接口修改失败');
				}
			}else {
				$this->error('支付接口修改失败');
			}
		}else {
			$this->assign('info',M('Payment')->find($id));
			$this->display('addpayment');
		}
	}

	public function deletepayment()
	{
		$id = $_GET['id'];
		D('payment')->delete($id);
		$this->success('删除成功');
	}

}
?>