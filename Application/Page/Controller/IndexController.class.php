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
        $this->display();
    }
}