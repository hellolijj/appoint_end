<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/1/27
 * Time: 下午12:13
 */

namespace Api\Service;

class BaseService {

    public static $USER_TYPE_STUDENT = 1;        // 学生用户
    public static $USER_TYPE_TEACHER = 2;        // 教师用户
    public static $USER_TYPE_UN_REGISTER = 0;    // 未注册用户
    public static $SYSTEM_UID = 145;             // 系统操作 系统uid

    public static $current_user_type = -1;       // 未存入数据类型
    public static $current_user_info = NULL;    // 用户信息
    public static $current_user_openid = NULL;
    public static $current_user_uid = 0;


    public static $GET_USER_LOGIN_TYPE_KEY = 'get_user_type_';

    public function __construct ()
    {
        self::$current_user_openid = session('openid');
        self::$current_user_type = $this->getUserType();
        self::$current_user_info = $this->getUserInfo();

    }


    /**
     * 获取用户类型
     */
    private function getUserType ()
    {
        $user_type = self::$current_user_type;
        $openid = self::$current_user_openid;

        if (!$openid) {
            return $user_type;
        }

        $cache_key = self::$GET_USER_LOGIN_TYPE_KEY . $openid;
        $cache_value = json_decode(S($cache_key));


        if (!empty($cache_value->user_type)) {
            $user_type = $cache_value->user_type;

        } else {
            // todo 从数据库中取 user_type
            $weixin = D('weixin')->getByOpenid($openid);
            if ($weixin) {
                $user_type = $weixin['type'];
                $user_type_json = json_encode(['user_type' => $user_type]);
                S($cache_key, $user_type_json, 3600);
            }
        }

        return $user_type;
    }

    /*
     * 获取用户信息
     */
    private function getUserInfo ()
    {
        $user_type = self::$current_user_type;
        $user_info = self::$current_user_info;
        $openid = self::$current_user_openid;
        $uid = self::$current_user_uid;

        if ($user_type <= 0 || !$openid || !$uid) {
            return NULL;
        }

        $cache_key = 'user_info_by_uid_' . $uid;
        $cache_value = json_decode(S($cache_key), TRUE);
        if (isset($cache_value['user_info']) && !empty($cache_value['user_info'])) {
            $user_info = $cache_value['user_info'];
        } elseif ($user_type == self::$USER_TYPE_STUDENT) {
            $user_info = D('student')->getById($uid);
            if (!$user_info) {
                S($cache_key, json_encode($user_info), 3600);
            }
        } elseif ($user_type == self::$USER_TYPE_TEACHER) {
            $user_info = D('teacher')->getById($uid);
            if (!empty($user_info)) {
                S($cache_key, json_encode($user_info), 3600);
            }
        }
        return $user_info;
    }

}