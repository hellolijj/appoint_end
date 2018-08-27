<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/4/18
 * Time: 下午9:04
 */

namespace Api\Model;

class ExamSubmitModel extends BaseModel {

    public function add($uid, $examid = 1, $score) {

        if (!$uid) {
            return FALSE;
        }

        $data = [
            'exam_id' => 1,
            'uid' => $uid,
            'score' => $score,
            'status' => 1,
            'gmt_create' => time(),
            'gmt_modified' => time(),
        ];


        $exam_submit_id = M('Exam_submit')->add($data);
        return $exam_submit_id;
    }


    public function list_items($uid) {
        if (!$uid) {
            return FALSE;
        }

        $exam_submit_lists = M('Exam_submit')->where(['uid' => $uid])->select();
        return $exam_submit_lists;



    }


}