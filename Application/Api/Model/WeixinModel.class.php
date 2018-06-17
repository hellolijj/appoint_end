<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/1/27
 * Time: 上午11:58
 */

namespace Api\Model;

class WeixinModel extends BaseModel {

    public static $USER_TYPE_PASSER = 1;   //表示这个openid来个我们应用
    public static $USER_TYPE_REGISTER = 2;  // 表示这个openid点击了获取用户信息
    public static $USER_TYPE_BIND = 3;    // 表示这个openid完成了绑定




    public function getByOpenid ($openid)
    {
        if (!$openid) {
            return FALSE;
        }
        $cache_key = 'appoint_weixin_items_by_openid' . $openid;
        $cache_value = S($cache_key);
        if ($cache_value) {
            return json_decode(S($cache_key), TRUE);
        }
        $weixin_items = $this->where(['openid' => $openid])->find();
        if ($weixin_items) {
            S($cache_key, json_encode($weixin_items), 3600);
        }

        return $weixin_items;
    }

    public function addAsPasser($openid) {
        $data = ['openid' => $openid, 'type' => 1, 'gmt_create' => time(), 'gmt_modified' => time()];
        if (FALSE == $this->getByOpenid($openid)) {
            $this->add($data);
            $cache_key = 'appoint_weixin_items_by_openid' . $openid;
            S($cache_key, NULL);
        }
    }

    public function updateInfo($openid, $data) {
        if (!$openid || count($data) == 0) {
            return FALSE;
        }
        $data['gmt_modified'] = time();
        $save_result = $this->where(['openid' => $openid])->save($data);
        if (!$save_result) {
            return FALSE;
        }
        $cache_key = 'appoint_weixin_items_by_openid' . $openid;
        S($cache_key, NULL);
        return TRUE;
    }

    public function getByUid($uid) {
        if (!$uid) {
            return FALSE;
        }
        $cache_key = 'appoint_weixin_items_by_uid' . $uid;
        $cache_value = S($cache_key);
        if ($cache_value) {
            return json_decode(S($cache_key), TRUE);
        }
        $weixin_items = $this->where(['uid' => $uid])->find();
        if ($weixin_items) {
            S($cache_key, json_encode($weixin_items), 3600);
        }

        return $weixin_items;

    }

}