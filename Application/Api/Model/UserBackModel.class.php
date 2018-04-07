<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/4/7
 * Time: 下午8:49
 */

namespace Api\Model;

class UserBackModel extends BaseModel {


    public function getByPassport($passport) {

        if (!$passport) {
            return FALSE;
        }

        $cache_key = 'appoint_user_back_by_' . $passport;
        $cache_value = S($cache_key);
        if ($cache_value) {
            return json_decode(S($cache_key), TRUE);
        }
        $user_back_item = $this->where(['passport' => $passport])->find();
        if ($user_back_item) {
            S($cache_key, json_encode($user_back_item), 3600);
        }
        return $user_back_item;
    }
}