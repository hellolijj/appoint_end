<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/4/21
 * Time: 上午11:27
 */

namespace Admin\Controller;

class UserController extends BaseApiController {


    /**
     * 查看系统所有的用户
     */
    public function all() {

        $this->title = '系统用户管理';
        $list = [
            ['id' => 1, 'username' => '李俊君', 'phone' => 123456],
            ['id' => 2, 'username' => '李俊君', 'phone' => 123456],
            ['id' => 3, 'username' => '李俊君', 'phone' => 123456],
        ];

        $this->assign('list', $list)->display();
    }

    public function t() {
        echo  'hello world';
    }
}