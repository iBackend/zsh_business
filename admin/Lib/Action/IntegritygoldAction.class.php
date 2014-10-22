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

}
?>