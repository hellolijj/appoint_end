<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;

/**
 * 空模块，主要用于显示404页面，请不要删除
 */
class EmptyController extends HomeController{
	//没有任何方法，直接执行HomeController的_empty方法
	//请不要删除该控制器

    /* 空操作，用于输出404页面 */
    public function _empty(){
        $this->redirect('Index/index');
    }


    protected function _initialize(){

    }

    /* 用户登录检测 */
    protected function login(){
        /* 用户登录检测 */
        is_login() || $this->error('您还没有登录，请先登录！', U('User/login'));
    }
}
