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

    /**
     * @param $passport_arr
     * @return bool|mixed
     * 根据护照的列表查询用户列表
     */
    public function listByPassports($passport_arr) {

        if (count($passport_arr) == 0) {
            return FALSE;
        }

        $cache_key = 'appoint_user_bank_by_passport_' . json_encode($passport_arr);
        $cache_value = S($cache_key);
        if ($cache_value) {
            return json_decode(S($cache_key), TRUE);
        }

        $where['passport'] = ['in', implode(',', $passport_arr)];
        $user_items = $this->where($where)->select();
        if ($user_items) {
            S($cache_key, json_encode($user_items), 3600);
        }
        return $user_items;

    }

    public function listByPage($page = 1, $page_size = 20) {

        $user_back_items = $this->page($page, $page_size)->select();

        return $user_back_items;

    }

    public function listByPageWhere ($where, $page_num, $page_size = 20, $order_by = 'id DESC')
    {
        return parent::listByPageWhere($where, $page_num, $page_size, $order_by); // TODO: Change the autogenerated stub
    }

    public function countByWhere (array $where)
    {
        return parent::countByWhere($where); // TODO: Change the autogenerated stub
    }
}