<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/4/7
 * Time: 下午9:53
 */

namespace Api\Service;

use Api\Model\WeixinModel;
use function PHPSTORM_META\type;

class UserService extends BaseService {

    /**
     * 用户绑定成为正式用户
     * return true or []
     */
    public function bind ($passport, $tel) {

        if (!$passport || !$tel) {
            return ['status' => 1, 'data' => '参数错误'];
        }

        $USER = D('User');

        // 1、查看是否已经绑定
        $user_item = $USER->getByTel($tel);
        if ($user_item) {
            return ['status' => 1, 'data' => '重复绑定'];
        }

        // 2、完成绑定add操作
        $add_result = $USER->add($passport, $tel, WeixinModel::$USER_TYPE_BIND);
        if (!$add_result) {
            return ['status' => 1, 'data' => '绑定失败 001'];
        }

        // 3、getByTel type状态
        $user_item = $USER->getByTel($tel);
        if (!$user_item) {
            return ['status' => 1, 'data' => '绑定失败 002'];
        }
        $uid = $user_item['id'];
        $data = [
            'uid' => $uid,
            'type' => WeixinModel::$USER_TYPE_BIND,
        ];
        $update_result = D('Weixin')->updateInfo(session('openid'), $data);
        if (!$update_result) {
            return ['status' => 1, 'data' => '绑定失败 003'];
        }

        return TRUE;
    }


    /**
     * 获取用户更多信息
     */
    public function get_more_info($uid) {
        if (!$uid) {
            return ['status' => 1, 'data' => '参数错误'];
        }

        $user_item = D('User')->getByUid($uid);
        if (!$user_item) {
            return ['status' => 1, 'data' => '用户没有绑定'];
        }
        $passport  = $user_item['passport'];
        $user_back_item = D('UserBack')->getByPassport($passport);
        if (!$user_back_item) {
            return ['status' => 1, 'data' => '系统没有该用户信息'];
        }

        $user_back_item['tel'] = $user_item['tel'];

        return $user_back_item;
    }

}