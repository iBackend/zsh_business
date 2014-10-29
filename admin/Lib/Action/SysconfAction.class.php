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
		$business = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."supplier_location WHERE `id` = ".$this->location_id);
		// 城市列表
		$city = D('AreaNew');
		$city_list = $city->order("`sort` DESC")->select();
		$city_tree = list_to_tree($city_list,$pk='id',$pid='pid',$child='_child',$root=0);
		$city_tree2 = tree_to_list2($city_tree);
			
		$this->assign('business_info',$business);
		$this->assign('citys',$city_tree2);
		$this->display();
	}
	
	
	public function basesave()
	{
		$business = $GLOBALS['db']->getOne("SELECT * FROM ".DB_PREFIX."supplier_location WHERE `id` = ".$this->location_id);
		
		$biz = D('supplier_location');
		$biz->create();
		$biz->id = $this->location_id;
		
		$file_upload_flag = false;
		foreach($_FILES as $f){
			if($f['size']>0){
				$file_upload_flag = true;
				break;
			}
		}
		
		if($file_upload_flag){
			$face = $this->uploadImage();
			if($face['status'] == 0){
				$this->error($face['info']);
			}
			if ($face['status'] == 1) {
				$biz->preview = 'http://'.$_SERVER['HTTP_HOST'].$face['data'][0]['recpath'].$face['data'][0]['savename'];
			}
		}
		
		$re = $biz->save();
		if ($re) {
			$this->success('商户信息保存成功');
		}else {
			$this->error('商户信息保存失败');
		}
	}

	public function authinfo()
	{
		$sql = "select * from ".DB_PREFIX."supplier_location_auth where location_id=".$this->location_id." limit 1";
		$auth = $GLOBALS['db']->getRow($sql);
		if(!$auth){
			$this->assign('warn',"您当前还没有提交认证信息！");
		}else {
			$this->assign('warn',"在已经认证的情况下，提交认证将需要重新认证！");
		}
		
		$auth['verify_info'] = $this->get_verify_info($auth['verify_status']);
		$this->assign('auth',$auth);
		$this->display();
	}
	
	private function get_verify_info($status){
		switch ($status){
			case 0:
				return "未认证";
			case 1:
				return "已认证";
		}
		return "审核状态不合法";
	}
	
	public function authsave()
	{
		$biz = D('supplier_location_auth');
		$biz->create();
		
		$data['location_id'] = $this->location_id;
		$data['supplier_id'] = $this->bid;
		$data['id_card'] = $_REQUEST['id_card'];

		$file_upload_flag = false;
		foreach($_FILES as $f){
			if($f['size']>0){
				$file_upload_flag = true;
				break;
			}
		}
		
		if($file_upload_flag){
			$face = $this->uploadImage();
			if($face['status'] == 0){
				$this->error($face['info']);
			}
				
			foreach ($face['data'] as $res){
				if($res['size']<1){
					continue;
				}
				$data[$res['key']] = 'http://'.$_SERVER['HTTP_HOST'].$res['recpath'].$res['savename'];
			}
		}
		
		$data['apply_route'] = empty($_REQUEST['apply_route'])?0:1;
		
		if($_REQUEST['id']){
			$data['id'] = $_REQUEST['id'];
			$re = $biz->save($data);
		}else {
			$re = $biz->add($data);
		}
// 		echo $biz->getLastSql(); die();
		
		if ($re) {
			$this->success('认证信息保存成功');
		}else {
			$this->error('认证信息保存失败');
		}
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

	
	public function accountInfo()
	{
		$model = D('admin');
		$bid = $this->bid;
		$list = $model->where("`id` = {$bid}")->select();
		$this->assign('admin',$list[0]);
		$this->display();
	}
	
	function accountsave(){
		$data = array();
		if($_REQUEST['adm_password'] && $_REQUEST['adm_password_cnf']){
			if($_REQUEST['adm_password'] != $_REQUEST['adm_password_cnf']){
				$this->error('密码不一致！');
			}else {
				$data['adm_password'] = md5($_REQUEST['adm_password']);
			}
		}
		
		$file_upload_flag = false;
		foreach($_FILES as $f){
			if($f['size']>0){
				$file_upload_flag = true;
				break;
			}
		}
		
		if($file_upload_flag){
			$face = $this->uploadImage();
			if($face['status'] == 0){
				$this->error($face['info']);
			}
			if ($face['status'] == 1) {
				$data['img'] = 'http://'.$_SERVER['HTTP_HOST'].$face['data'][0]['recpath'].$face['data'][0]['savename'];
			}
		}
		
		$data['name'] = $_REQUEST['name'];
		$data['phone'] = $_REQUEST['phone'];
		$data['email'] = $_REQUEST['email'];
		$data['wx'] = $_REQUEST['wx'];
		
		$sql = "update ".DB_PREFIX."admin set 
			name = '".$data['name']."',
			phone = '".$data['phone']."',
			email = '".$data['email']."',
			name = '".$data['name']."',
			wx = '".$data['wx']."' ";
			
		if(isset($data['adm_password'])){
			$sql .= ",adm_password = '".$data['adm_password']."' ";
		}
		if(isset($data['img'])){
			$sql .= ",img = '".$data['img']."' ";
		}
		
		$sql .= "where id=".$this->bid;
		$re = $GLOBALS['db']->query($sql);
		
		if ($re) {
			$this->success('账号信息修改成功');
		}else {
			$this->error('账号信息修改失败');
		}
	}
}
?>