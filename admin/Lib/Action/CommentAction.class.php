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

	public function answer()
	{
		$this->display();
	}

}
?>