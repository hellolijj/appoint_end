<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/4/21
 * Time: 上午11:27
 */

namespace Admin\Controller;

use Api\Service\UserService;
use Think\Page;

class UserController extends BaseApiController {

    /**
     * 查看系统所有的用户
     */
    public function all() {
        $this->assign('title', '注册绑定用户');
        $users = M('User')->select();
        $users_uid_arr = result_to_array($users, 'id');
        $user_service = new UserService();
        $users_info_list = $user_service->list_more_info_by_uids($users_uid_arr);

        $this->assign('list', $users_info_list)->display();
    }

    public function t() {
        echo  'hello world';
    }


    /**
     * 这是所有的储存用户，信息存与user_back表中
     */
    public function back() {
        $this->assign('title', '所有用户管理');

        // 处理where搜索条件
        $name = I('name');
        $passport = I('passport');
        $number = I('number');
        $where = [];
        if ($name) {
            $where['name'] = $name;
        }
        if ($passport) {
            $where['passport'] = $passport;
        }
        if ($number) {
            $where['number'] = $number;
        }


        $p = $_GET['p'];
        $user_backs = D('Api/UserBack')->listByPageWhere($where, $p, 20, 'id desc   ');

        $this->assign('list',$user_backs);// 赋值数据集
        $count      = D('Api/UserBack')->countByWhere($where);// 查询满足要求的总记录数
        $Page       = new Page($count, 20);
        $page       = $Page->show();// 分页显示输出

        $this->assign('page_content',$page);// 赋值分页输出

        $this->display(); // 输出模板
    }

    public function edit() {
        $id = intval(I('id'));
        if (IS_POST) {
            return $this->success('操作成功');
        }
        $info = D('UserBack')->where(['id'=>$id])->find();
        $this->assign('info', $info);
        $view = $this->fetch('form');
        $this->ajaxReturn($view);
    }
}