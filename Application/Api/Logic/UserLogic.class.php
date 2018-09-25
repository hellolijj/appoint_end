<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/2/3
 * Time: 下午3:08
 */

namespace Api\Logic;


use Api\Model\TemplateIdModel;
use Api\Service\TempMsgService;
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

        $passport = trim(I('passport'));
        $phone = trim(I('phone'));
        $formid = trim(I('formid'));

        if (!$passport || !$phone) {
            return $this->setError('信息不能为空');
        }

        // 手机号判断  由于特殊的原因，10位数字的手机号也放过
        if (strlen($phone) != 10) {
            $regex = '/^1[345678]{1}\d{9}$/';
            if (!preg_match($regex, $phone)) {
                return $this->setError('手机号码格式错误');
            }
        }

        // 根据passport查找用户
        $user_back_item = D('UserBack')->getByPassport($passport);
        if (!$user_back_item) {
            return $this->setError('该护照号不存在！');
        }
        // 将passport telphone 同时存入应用 同时绑定成为正式用户
        $userService = new UserService();
        $bind_result = $userService->check_bind($passport, $phone);
        if (is_array($bind_result)) {
            return $this->setError($bind_result['data']);
        }
        $bind_result = $userService->bind($passport, $phone);
        if (is_array($bind_result)) {
            return $this->setError($bind_result['data']);
        }

        session('uid', $bind_result);
        // 完成用户信息的绑定
        $user_service = new UserService();
        $user_info = $user_service->get_more_info(session('uid'));


        // 发送微信模版消息

        $tempMsgService = new TempMsgService();
        $temp_data = [
            'keyword1'  => ['value'=>$user_info['name'] ? $user_info['name'] : $user_info['first_name']],
            'keyword2'  => ['value'=>$user_info['number'] ? $user_info['number'] : '暂未设置学号'],
            'keyword3'  => ['value'=>$user_info['college'] ? $user_info['college'] : '暂未设置学院'],
            'keyword4'  => ['value'=>$user_info['class'] ? $user_info['class'] : '暂未设置班级'],
            'keyword5'  => ['value'=>$phone],
            'keyword6'  => ['value'=>$user_info['nickname'] ? $user_info['nickname'] : '国创预约'],
            'keyword7'  => ['value'=>date('Y-m-d H:i:s', time())],
            'keyword8'  => ['value'=>'绑定成功'],
        ];

        $page = 'pages/o9j42s2GS3_page10000/o9j42s2GS3_page10000';
        $send_result = $tempMsgService->doSend($user_info['openid'], TemplateIdModel::$BIND_SUCCESS_TEMPLATE_ID, $formid, $temp_data, $page);

        if (!$send_result['success']) {
            // todo save in error log
            // return ['status' => 0, 'data' => $send_result['message']];
        }
        return $this->setSuccess($user_info);
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