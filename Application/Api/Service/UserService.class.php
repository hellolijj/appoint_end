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
     * 检查是否符合绑定条件
     * return true or []
     */
    public function check_bind ($passport, $tel) {

        if (!$passport || !$tel) {
            return ['status' => 1, 'data' => '参数错误'];
        }

        // 1、查看是否已经绑定
        $user_item = D('Api/User')->getByPassport($passport);
        if ($user_item) {
            return ['status' => 1, 'data' => '重复绑定'];
        }

        // 2、weixin表中 openid是否存在
        $weixin_item = D('Api/Weixin')->where(['openid'=>session('openid')])->find();
        if (!$weixin_item) {
            return ['status' => 1, 'data' => '绑定失败，请退出重新打开小程序'];
        }

        return TRUE;
    }

    /**
     *  完成绑定操作
     */
    public function bind($passport, $tel) {
        if (!$passport || !$tel) {
            return ['status' => 1, 'data' => '参数错误'];
        }

        // 完成绑定add操作
        $add_result = D('Api/User')->add($passport, $tel, WeixinModel::$USER_TYPE_BIND);
        if (!$add_result) {
            // todo add error log
            return ['status' => 1, 'data' => '用户添加失败'];
        }
        $user_item = D('Api/User')->getByPassport($passport);
        $uid = $user_item['id'];  // 添加成功返回 uid
        $data = [
            'uid' => $uid,
            'type' => WeixinModel::$USER_TYPE_BIND,
        ];
        $update_result = D('Api/Weixin')->updateInfo(session('openid'), $data);
        if (!$update_result) {
            // todo add error log
            return ['status' => 1, 'data' => '注册更新错误'];
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
        $user_back_items = D('Api/UserBack')->listByPassports($passport_arr);
        $user_back_items = result_to_map($user_back_items, 'passport');


        $weixin_items = D('Api/Weixin')->listByUids($uid_arr);
        $weixin_items = result_to_map($weixin_items, 'uid');


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


    /***
     * @param $uid
     * @return bool|mixed
     * 判断是否是语言生，根据专业名称是否包含 "长期汉语言文化课程" 来判断
     */
    public function is_yuyan_student($uid) {
        if (!$uid) {
            return FALSE;
        }

        $more_info = $this->get_more_info($uid);
        echo $more_info['profession'];

        if (is_array($more_info) && strlen($more_info['profession']) > 0 && strops($more_info['profession'], "长期汉语言文化课程") !== FALSE) {
            return TRUE;
        }

        return FALSE;
    }

}