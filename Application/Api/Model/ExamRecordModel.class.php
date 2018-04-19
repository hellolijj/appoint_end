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

    public function getBySubmitId($submit_id) {
        if (!$submit_id) {
            return FALSE;
        }
        $cache_key = 'appoint_exam_record_by_submit_id_' . $submit_id;
        $cache_value = S($cache_key);
        if ($cache_value) {
            return json_decode(S($cache_key), TRUE);
        }
        $exam_record_item = $this->where(['submit_id' => $submit_id])->select();
        if ($exam_record_item) {
            S($cache_key, json_encode($exam_record_item), 3600);
        }
        return $exam_record_item;
    }


    public function getBySidAndQids($submit_id, $qids) {

        if (!$submit_id || count($qids) == 0) {
            return FALSE;
        }
        $cache_key = 'appoint_question_by_submit_id_' . $submit_id . '_and_qids_' . json_encode($qids);
        $cache_value = F($cache_key);
        if ($cache_value) {
            return json_decode(F($cache_key), TRUE);
        }
        $question_items = $this->where(['submit_id' => $submit_id,  'qid' => ['IN', implode(',', $qids)]])->select();
        if ($question_items) {
            F($cache_key, json_encode($question_items));
        }
        return $question_items;
    }

}