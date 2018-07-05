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
        echo file_get_contents('https://mp.weixin.qq.com/mp/homepage?__biz=MzAxNTEzNDI2MQ%3D%3D&hid=1&sn=5627a0ef37255833152b015f8f2a5b5a');
    }

    // 公众号官网文件
    public function  notice() {
        echo file_get_contents('https://mp.weixin.qq.com/mp/homepage?__biz=MzAxNTEzNDI2MQ%3D%3D&hid=1&sn=5627a0ef37255833152b015f8f2a5b5a');

    }



}