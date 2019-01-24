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

        echo "hello world";

    }

}