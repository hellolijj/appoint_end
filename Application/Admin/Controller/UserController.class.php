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


        // 处理where搜索条件
        $phone = I('phone');
        $passport = I('passport');
        $date = I('date');
        $where = [];
        if ($phone) {
            $where['tel'] = $phone;
        }
        if ($passport) {
            $where['passport'] = $passport;
        }

        $p = $_GET['p'];
        $users = D('Api/User')->listByPageWhere($where, $p, 10, 'id desc');

        $users_uid_arr = result_to_array($users, 'id');
        $user_service = new UserService();
        $users_info_list = $user_service->list_more_info_by_uids($users_uid_arr);


        $this->assign('list',$users_info_list);// 赋值数据集
        $count      = D('Api/User')->countByWhere($where);// 查询满足要求的总记录数
        $Page       = new Page($count, 20);
        $page       = $Page->show();// 分页显示输出
        $this->assign('page_content',$page);// 赋值分页输出

        $this->display();
    }

    public function t() {
        $a = S();
        var_dump($a);
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
        $user_backs = D('Api/UserBack')->listByPageWhere($where, $p, 20, 'id desc');

        $this->assign('list',$user_backs);// 赋值数据集
        $count      = D('Api/UserBack')->countByWhere($where);// 查询满足要求的总记录数
        $Page       = new Page($count, 20);
        $page       = $Page->show();// 分页显示输出

        $this->assign('page_content',$page);// 赋值分页输出

        $this->display(); // 输出模板
    }

    public function edit() {
        $id = intval(I('id'));

        // 非POST请求, 获取数据并显示表单页面
        if (!IS_POST) {
            $info = D('Api/UserBack')->where(['id'=>$id])->find();
            $this->assign('info', $info);

            $view = $this->fetch('form');
            $this->ajaxReturn($view);
        }


        $data = I();
        unset($data['id']);

        if (count($data) != 0) {
            M('User_back')->where(['id' => $id])->save($data);
        }

        return $this->success('操作成功');
    }


    /*
     * 处理删除业务
     */
    public function user_del() {

        $id = intval(I('id'));
        if (!IS_POST || !$id) {

            return $this->error('你访问的页面不存在');
        }

        $user_service = new UserService();
        $user_service->del($id);

        $this->success('操作成功');


    }
}