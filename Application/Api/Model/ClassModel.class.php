<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/2/17
 * Time: 下午7:49
 */

namespace Api\Model;


class ClassModel extends BaseModel {

    public static $STATUS_IN_USE = 1;

    /*
     * 罗列所有的正在使用的课程
     */
    public function getClassByUid ($uid, $page, $page_size)
    {
        if (!$uid || !is_numeric($uid)) {
            return ['success' => FALSE, 'message' => 'uid参数错误'];
        }
        $where = ['uid' => $uid, 'status' => self::$STATUS_IN_USE,];
        $page = $page ? $page : 1;
        $page_size = $page_size ? $page_size : 20;
        $list_in_use = $this->cache(60)->where($where)->order('gmt_create desc')->page($page)->select();
        return $list_in_use;
    }

    public function countClassByUid ($uid)
    {
        $where = ['uid' => $uid, 'status' => self::$STATUS_IN_USE,];
        $count = $this->cache(60)->where($where)->count();
        if (!$count) {
            return 0;
        }
        return $count;
    }

    public function getByUidAndCid ($uid, $cid)
    {
        if (!$uid || !$cid) {
            return FALSE;
        }

        $cache_key = 'pingshifen_class_by_uid_' . $uid . '_cid_' . $cid;
        $cache_value = S($cache_key);
        if ($cache_value) {
            return json_decode(S($cache_key), TRUE);
        }
        $class_item = $this->where(['uid' => $uid, 'cid' => $cid])->find();
        if ($class_item) {
            S($cache_key, json_encode($class_item), 3600);
        }
        return $class_item;
    }



}