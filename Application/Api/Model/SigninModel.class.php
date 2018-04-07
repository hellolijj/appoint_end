<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/1/27
 * Time: 下午4:36
 */

namespace Api\Model;
class SigninModel extends BaseModel {

    public function add ($uid, $cid, $title, $start_time, $end_time, $address, $latitude, $longitude, $radius)
    {
        $data = ['uid' => $uid, 'cid' => $cid, 'title' => $title, 'start_time' => $start_time, 'end_time' => $end_time, 'address' => $address, 'latitude' => $latitude, 'longitude' => $longitude, 'radius' => $radius, 'gmt_create' => time(), 'gmt_modified' => time(),];
        $cache_key = 'pingshifen_signin_by_cid_' . $cid;
        S($cache_key, NULL);
        return M('Signin')->add($data);
    }


    public function listByCid ($cid, $page = 1, $page_size = 20)
    {
        if (!$cid) {
            return FALSE;
        }
        $cache_key = 'pingshifen_signin_by_cid_' . $cid;
        $cache_value = S($cache_key);
        if ($cache_value) {
            return json_decode(S($cache_key), TRUE);
        }
        $signin_items = M('Signin')->where(['cid' => $cid])->order('gmt_create desc')->select();
        if ($signin_items) {
            S($cache_key, json_encode($signin_items));
        }
        return $signin_items;
    }
    /**
     * 签到数加1
     */
    public function countIncByid ($cid, $sid)
    {
        if (!$sid) {
            return FALSE;
        }
        $this->where(['id' => $sid])->setInc('count');
        $cache_key = 'pingshifen_signin_by_cid_' . $cid;
        S($cache_key, NULL);
    }

    /**
     * 签到数减1
     */
    public function countDecById ($cid, $sid) {
        if (!$sid) {
            return FALSE;
        }
        $this->where(['id' => $sid])->setDec('count');
        $cache_key = 'pingshifen_signin_by_cid_' . $cid;
        S($cache_key, NULL);
    }

    public function getCidById ($id)
    {
        if (!$id) {
            return FALSE;
        }
        $cache_key = 'pingshifen_signin_get_cid_by_id_' . $id;
        $cache_value = S($cache_key);
        if ($cache_value) {
            return json_decode(S($cache_key), TRUE);
        }
        $signin_cid = M('Signin')->where(['id' => $id])->getField('cid');
        if ($signin_cid) {
            S($cache_key, $signin_cid, 3600);
        }
        return $signin_cid;
    }

    public function getById ($id)
    {
        if (!$id) {
            return FALSE;
        }
        $cache_key = 'pingshifen_signin_get_by_id_' . $id;
        $cache_value = S($cache_key);
        if ($cache_value) {
            return json_decode(S($cache_key), TRUE);
        }
        $signin_item = M('Signin')->where(['id' => $id])->find();
        if ($signin_item) {
            S($cache_key, json_encode($signin_item), 3600);
        }
        return $signin_item;
    }


}