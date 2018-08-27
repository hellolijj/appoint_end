<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/6/16
 * Time: 下午11:47
 */

namespace Api\Model;

class FormidModel extends BaseModel {


    public function add($uid, $formid) {

        if (!$uid || !$formid) {
            return FALSE;
        }

        $data = [
            'uid' => $uid,
            'formid' => $formid,
            'status' => 1,
            'gmt_create' => time(),
            'gmt_modified' => time(),
        ];

        M('Formid')->add($data);

    }

    public function deleteByUidAndFormid($uid, $formid) {
        if (!$uid || !$formid) {
            return FALSE;
        }

        $map = [
            'uid' => $uid,
            'formid' => $formid,
        ];

        M('Formid')->where($map)->delete();

    }

    /**
     * @param $time 创建时间
     */
    public function clearBeforeGmt_create($before_time) {

        if (!$before_time) {
            return FALSE;
        }

        // gmt_create <= before_time
        $map = [
            'gmt_create' => ['elt', $before_time],
        ];

        M('Formid')->where($map)->delete();

    }


    public function getNewestByUid($uid) {

        if (!$uid) {
            return FALSE;
        }

        $list = M('Formid')->where(['uid'=>$uid])->order('id desc')->limit(1)->select();

        return $list[0];
    }

}