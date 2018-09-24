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
        $user_item = $USER->getByPassport($passport);
        if ($user_item) {
            return ['status' => 1, 'data' => '重复绑定'];
        }

        // 2、完成绑定add操作
        $add_result = $USER->add($passport, $tel, WeixinModel::$USER_TYPE_BIND);
        if (!$add_result) {
            return ['status' => 1, 'data' => '绑定失败 001'];
        }

        $uid = $user_item['id'];
        $data = [
            'uid' => $uid,
            'type' => WeixinModel::$USER_TYPE_BIND,
        ];

        // 3、weixin表中 openid是否存在
        if (D('Api/Weixin')->where(['openid'=>session('openid')])->find()) {
            return ['status' => 1, 'data' => '绑定失败，请退出重新打开小程序'];
        }

        $update_result = D('Weixin')->updateInfo(session('openid'), $data);
        if (!$update_result) {
            return ['status' => 1, 'data' => '绑定失败 003'];
        }

        return $uid;
    }


    /**
     * 获取用户更多信息
     */
    public function get_more_info($uid) {
        if (!$uid) {
            return ['status' => 1, 'data' => '参数错误'];
        }

        $user_item = D('Api/User')->getByUid($uid);
        if (!$user_item) {
            return ['status' => 1, 'data' => '用户没有绑定'];
        }
        $passport  = $user_item['passport'];
        $user_back_item = D('UserBack')->getByPassport($passport);
        if (!$user_back_item) {
            return ['status' => 1, 'data' => '系统没有该用户信息'];
        }
        $user_weixin_item = D('Weixin')->getByUid($uid);

        $user_back_item['tel'] = $user_item['tel'];

        return array_merge($user_back_item, $user_weixin_item);
    }


    /**
     * @param $uid_arr
     * 根据passports来获取一个用户的更多信息
     */
    public function list_more_info_by_passports($passport_arr) {

        if (!$passport_arr) {
            return FALSE;
        }

        $user_items = D('Api/UserBack')->listByPassports($passport_arr);
        if (!$user_items) {
            return [];
        }

        $user_items = result_to_map($user_items, 'passport');

        return $user_items;
    }

    public function list_more_info_by_uids($uid_arr) {

        if (!$uid_arr) {
            return FALSE;
        }

        $user_items = D('Api/User')->listByUids($uid_arr);
        if (!$user_items) {
            return [];
        }

        $passport_arr = result_to_array($user_items, 'passport');
        p($passport_arr);
        $user_back_items = D('Api/UserBack')->listByPassports($passport_arr);
        $user_back_items = result_to_map($user_back_items, 'passport');
        p($user_back_items);

        $weixin_items = D('Api/Weixin')->listByUids($uid_arr);
        $weixin_items = result_to_map($weixin_items, 'uid');
        p($weixin_items);

        foreach ($user_items as &$user_item) {

            if ($user_item['passport']) {

                // 后面加入的id键会覆盖之前的键值
                unset($user_back_items[$user_item['passport']]['id']);
                $user_item = array_merge($user_item, $user_back_items[$user_item['passport']]);
            }

            if ($user_item['id']) {
                unset($weixin_items[$user_item['id']]['id']);
                $user_item = array_merge($user_item, $weixin_items[$user_item['id']]);
            }

        }

        return $user_items;
    }


    /**
     * 删除一个用户
     */
    public function del($uid) {

        // 删除user表。删除weixin表。缓存怎么班？session缓存无法操作呀。
        M('User')->delete($uid);

        M('Weixin')->where(['uid'=>$uid])->delete();

        // todo 缓存的问题
    }



}