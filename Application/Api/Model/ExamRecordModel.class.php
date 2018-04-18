<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/4/18
 * Time: 下午8:56
 */

namespace Api\Model;

class ExamRecordModel extends BaseModel {

    public function add($uid, $submit_id, $submit_arr) {

        if (!$uid || !$submit_id || count($submit_arr) == 0) {
            return FALSE;
        }

        foreach ($submit_arr as &$submit) {

            if (empty($submit['qid'])) {
                return FALSE;
            }

            $submit['submit_id'] = $submit_id;
            $submit['uid'] = $uid;
            $submit['status'] = 1;
            $submit['gmt_create'] = time();
            $submit['gmt_modified'] = time();
        }

        M('Exam_record')->addAll($submit_arr);
    }
}