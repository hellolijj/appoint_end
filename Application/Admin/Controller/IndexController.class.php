<?php
namespace Admin\Controller;



class IndexController extends BaseApiController {



    public function index ()
    {
        $menus = C('menus');
        $this->assign('menus', $menus);
        $this->display('index');
    }

    public function home() {
        echo 'dsf';
    }

    public function main() {
        $this->assign('title', '系统首页');

        $this->display();
    }




    public function email() {
        $mail = new \SaeMail();

        $ret = $mail->quickSend("hello_lijj@qq.com", "这是新浪发给qq的邮件", "李俊君制作", "hello_lijj@sina.com", "hello_lijj", "smtp.sina.com", 25); //指定smtp和端口

        //发送失败时输出错误码和错误信息
        if ($ret === false) {
            var_dump($mail->errno(), $mail->errmsg());
        }

        echo '发送成功';
    }

    public function send() {
        email();
    }

    public function t() {
        $a = C('APP_SUB_DOMAIN_DEPLOY');
        p($a);
        echo U('admin');
    }

}