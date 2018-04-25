<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/4/19
 * Time: 下午9:04
 */


/**
 * 不继承Base的类
 */


namespace Admin\Controller;

class LoginController extends BaseController {



    public function login() {
        if (IS_POST) {
            I('get.name','','htmlspecialchars');

            $username = I('post.username', '', 'trim');
            $password = I('post.password', '', 'trim');
            strlen($username) < 4 && $this->error('登录账号长度不能少于4位有效字符!');
            strlen($password) < 4 && $this->error('登录密码长度不能少于4位有效字符!');


            // 用户信息验证
            $user = D('SystemUser')->where(['username' => $username])->find();
            empty($user) && $this->error('登录账号不存在，请重新输入!');
            ($user['password'] !== md5($password)) && $this->error('登录密码与账号不匹配，请重新输入!');
            empty($user['status']) && $this->error('账号已经被禁用，请联系管理!');

            // 更新登录信息
            session('user', $user);

            $this->success('', U('Index/index'));

        } else {
            $this->display();
        }
    }

    public function logout() {
        session('user', null);
        session_destroy();
        $this->success('退出登录成功！', 'login');
    }


}