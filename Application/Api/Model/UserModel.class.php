<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/4/7
 * Time: 下午9:42
 */

namespace Api\Model;

class UserModel extends BaseModel {

    public static $filed = [
        'id',
        'passport',
        'first_name',
        'last_name',
        'name',
        'sex',
        'country',
        'profession',
        'college',
        'class',
        'number',
        'entry_year',
    ];

    public function getByUid($uid) {
        if (!$uid) {
            return FALSE;
        }
        $cache_key = 'appoint_user_by_uid' . $uid;
        $cache_value = S($cache_key);
        if ($cache_value) {
            return json_decode(S($cache_key), TRUE);
        }
        $user_item = $this->where(['id' => $uid])->find();
        if ($user_item) {
            S($cache_key, json_encode($user_item), 3600);
        }
        return $user_item;
    }

    public function getByTel($tel) {
        if (!$tel) {
            return FALSE;
        }
        $cache_key = 'appoint_user_by_tel_' . $tel;
        $cache_value = S($cache_key);
        if ($cache_value) {
            return json_decode(S($cache_key), TRUE);
        }
        $user_item = $this->where(['tel' => $tel])->find();
        if ($user_item) {
            S($cache_key, json_encode($user_item), 3600);
        }
        return $user_item;
    }

    public function getByPassport($passport) {
        if (!$passport) {
            return FALSE;
        }
        $cache_key = 'appoint_user_by_passport_' . $passport;
        $cache_value = S($cache_key);
        if ($cache_value) {
            return json_decode(S($cache_key), TRUE);
        }
        $user_item = $this->where(['passport' => $passport])->find();
        if ($user_item) {
            S($cache_key, json_encode($user_item), 3600);
        }
        return $user_item;
    }

    public function add($passport, $telphone, $type) {

        if (!$passport || !$telphone || !$type) {
            return FALSE;
        }

        if ($this->getByPassport($passport)) {
            return FALSE;
        }

        $data = [
            'passport' => strtoupper(trim($passport)),
            'tel' => $telphone,
            'type' => $type,
            'status' => 1,
            'gmt_create' => time(),
            'gmt_modified' => time(),
        ];
        M('User')->add($data);
        $cache_key = 'appoint_user_by_tel_' . $telphone;
        S($cache_key, NULL);
        return TRUE;
    }

    /***
     * @param $uid_arr uid 列表
     *
     */

    public function listByUids($uid_arr) {

        if (count($uid_arr) == 0) {
            return FALSE;
        }

        $cache_key = 'appoint_user_by_uids_' . json_encode($uid_arr);
        $cache_value = S($cache_key);
        if ($cache_value) {
            return json_decode(S($cache_key), TRUE);
        }

        $where['id'] = ['in', implode(',', $uid_arr)];
        $user_items = $this->where($where)->select();
        if ($user_items) {
            S($cache_key, json_encode($user_items), 3600);
        }
        return $user_items;


    }

}