<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/2/3
 * Time: 下午3:08
 */

namespace Api\Logic;


use Api\Service\UserService;

class UserLogic extends BaseLogic {

    public function status() {
        return ['data' => '', 'status' => 0];
    }


    public function no_shop() {
        return ['data' => 0, 'status' => 0];
    }

    public function no_vip() {
        return ['data' => 0, 'status' => 0, 'vip_level' => 0];
    }

    /**
     * 用户绑定
     * 完成用户openid与passport的绑定
     */
    public function bind_user_info()
    {

        $passport = I('passport');
        $phone = trim(I('phone'));

        if (!$passport || !$phone) {
            return $this->setError('信息不能为空');
        }

        // todo 根据passport查找用户
        $user_back_item = D('UserBack')->getByPassport($passport);
        if (!$user_back_item) {
            return $this->setError('该护照号不存在！');
        }
        // todo 将passport telphone 同时存入应用 同时绑定成为正式用户
        $userService = new UserService();
        $bind_result = $userService->bind($passport, $phone);
        if (is_array($bind_result)) {
            return $this->setError($bind_result['data']);
        }

        return $this->setSuccess();
    }


    public function carousel_photo()
    {
        $data = [['id' => 1, 'app_id' => 'mPxWOXi4p4', 'form_data' =>
            json_encode(['groupName' => '首页', 'groupId' => 1127301, 'pic' => 'http://pingshif-img.stor.sinaapp.com/2018-04-03/WechatIMG9.jpeg', 'isShow' => 1, 'action' => 'none', 'actionText' => 'welcome',]),
            'weight' => 1, 'type' => 1127301, 'apply_sub_shop' => 0, 'is_check' => 0,],
            ['id' => 2, 'app_id' => 'mPxWOXi4p4',
            'form_data' =>
                json_encode(['groupName' => '首页', 'groupId' => 1127301, 'pic' => 'http://pingshif-img.stor.sinaapp.com/2018-04-03/WechatIMG8.png', 'isShow' => 1, 'action' => 'none', 'actionText' => 'welcome',]),
            'weight' => 1, 'type' => 1127302, 'apply_sub_shop' => 0, 'is_check' => 0,],

        ];
        return ['status' => 0, 'is_more' => 0, 'current_page' => 1, 'data' => $data, 'count' => 2, 'total_page' => 1,];
    }
}