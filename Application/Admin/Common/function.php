<?php

function email() {

    $mail = new \SaeMail();

    $ret = $mail->quickSend("hello_lijj@foxmail.com", "这是qq发给foxmail的邮件", "lijjmake", "hello_lijj@qq.com", "hello_lijj", "smtp.qq.com", 465, TRUE); //指定smtp和端口

//    'zvalkqmewgnyjfeh'
    //发送失败时输出错误码和错误信息
    if ($ret === false) {
        var_dump($mail->errno(), $mail->errmsg());
    }

    echo '发送成功';

}