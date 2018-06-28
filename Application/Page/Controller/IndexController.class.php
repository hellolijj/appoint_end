<?php
namespace Page\Controller;

use Think\Controller;

class IndexController extends Controller
{
    public function index()
    {
        echo "page service";
    }

    // 使用帮助页面
    public function help() {
        echo file_get_contents('https://mp.weixin.qq.com/mp/homepage?__biz=MzIyMDcxODY1NQ==&hid=2&sn=d5b0d12af79428a4a23690de4bd20c99&scene=18#wechat_redirect');
    }



}