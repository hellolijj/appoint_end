<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/4/19
 * Time: 下午9:25
 */

namespace Admin\Controller;


class BaseApiController extends BaseController {

    public function _initialize()
    {
        if (empty(session('user'))) {
            $this->redirect('Login/login');
        }
    }

}