<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/4/19
 * Time: 下午8:45
 */

namespace Admin\Controller;

use Think\Controller;

class BaseController extends Controller {

    public function error($message = '操作失败', $url = '', $wait = 3) {

        $data = [
            'code' => 0,
            'data' => '',
            'msg' => $message,
            'wait' => $wait,
            'url' => $url,
        ];
        $this->ajaxReturn($data);
    }

    public function  success($message = '操作成功', $url = '', $wait = 3) {

        $data = [
            'code' => 1,
            'data' => '',
            'msg' => $message,
            'wait' => $wait,
            'url' => $url,
        ];
        $this->ajaxReturn($data);
    }

}
