<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2019/1/24
 * Time: 14:44
 */

namespace Admin\Controller;


class QueueController {


    public function download() {

        $t = new \SaeTaskQueue("download");
        $t->addTask("http://" . $_SERVER['HTTP_HOST'] . "/admin.php/Queue/test"); //添加列队任务1
        if (!$t->push()) {
            echo '出错:' . $t->errmsg();
        } else {
            echo '执行成功！请查看['  . 'sae_debug.log' . ']文件中的日志';
        }

    }

    public function test() {

        // todo 实现一个mail 发送

        $mail = new \SaeMail();

//        $ret = $mail->quickSend( 'to@sina.cn' , '邮件标题' , '邮件内容' , 'smtpaccount@unknown.com' , 'password' , 'smtp.unknown.com' , 25 )
        $ret = $mail->quickSend("1217046214@qq.com,21851172@zju.edu.cn", "这是新浪发给qq的邮件", "李俊君制作", "17826839787@163.com", "456123jkl", "smtp.163.com", 25); //指定smtp和端口

        //发送失败时输出错误码和错误信息
        if ($ret === false) {
            var_dump($mail->errno(), $mail->errmsg());
        } else {
            echo '发送成功';
        }



    }

}