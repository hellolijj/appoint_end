<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/2/16
 * Time: 下午9:42
 */

namespace Api\Logic;

/*
 * userbase 调用此类表示都是已经注册用户
 */
use Api\Model\WeixinModel;
use Api\Service\WeixinService;

class UserBaseLogic extends BaseLogic {

    public $uid = 0;
    public $user_type = 0;

    public function __construct ()
    {
//        parent::__construct();

    }

    private function check_uid ()
    {

    }
}
