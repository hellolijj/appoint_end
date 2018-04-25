<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/2/25
 * Time: 下午7:35
 */

namespace Api\Service;

use Api\Model\QuestionBankModel;

class ExaminationService extends BaseService {

    /*
     * 随机出一百道题目。仅供测试
     */
    public function set_random_question ($sid)
    {
        if (!$sid) {
            return FALSE;
        }

        /**
         * 老师要40单选题 10多选题
         */

        $single_itmes = D('QuestionBank')->getIdsBySidAndType($sid, QuestionBankModel::$TYPE[QuestionBankModel::$TYPE_SINGLE]);
        $muti_items = D('QuestionBank')->getIdsBySidAndType($sid, QuestionBankModel::$TYPE[QuestionBankModel::$TYPE_MULTIPLE]);
        $question_items = array_merge($single_itmes, $muti_items);

        $single_rand_ids = array_rand($single_itmes, 40);
        $mutiple_rand_ids = array_rand($muti_items, 10);
        $rand_ids = array_merge($single_rand_ids, $mutiple_rand_ids);


        // todo 这么做性能不是很好，后续再优化
        $data = [];
        foreach ($rand_ids as $rand_id) {
            $data[] = $question_items[$rand_id];
        }
        return $data;
    }

    /**
     * 错题回顾时候从数据库读取题目
     */

    public function get_review($submit_id) {
        if (!$submit_id) {
            return FALSE;
        }
        $question_items = D('ExamRecord')->getBySubmitId($submit_id);

        // qid => id
        foreach ($question_items as &$_question_item) {
            $_question_item['question_id'] = $_question_item['qid'];
            $_question_item['collection'] = 0;
            unset($_question_item['submit_id']);
            unset($_question_item['uid']);
            unset($_question_item['id']);
            unset($_question_item['qid']);
            unset($_question_item['gmt_create']);
            unset($_question_item['gmt_modified']);
            unset($_question_item['status']);

            // resutl => answer
            $_question_item['answer'] = $_question_item['result'];
            unset($_question_item['result']);
        }
        $question_items = result_to_map($question_items, 'question_id');
        return $question_items;
    }

    /**
     * 给添加上考试的记录
     */
    public function add_record_more_at_exam($uid, $submit_id, &$question_items) {

        $question_id_arr = result_to_array($question_items, 'id');
        $question_record_items = D('ExamRecord')->getBySidAndQids($submit_id, $question_id_arr);
        $question_record_items = result_to_map($question_record_items, 'qid');


        foreach ($question_items as &$question_item) {
            $qid = $question_item['id'];

            $question_item['id_'] = $question_item['id'];
            $question_item['question_id'] = $question_item['id'];

            // 媒体类型
            $question_item['media_type'] = $question_item['media_id'] > 0 ? 1 : 0;
            $question_item['label_'] = '4.1.1.6';

            // 题干
            $question_item['question_'] = $question_item['content'];
            unset($question_item['content']);

            // 题目跟图片、视频相关
            $question_item['media_type'] = $question_item['media_id'] > 0 ? 1 : 0;
            $question_item['media_content'] = '';
            $question_item['media_width'] = '';
            $question_item['media_height'] = '';

            // 题目正确答案
            $question_item['answer_'] = $question_item['answer'];
            unset($question_item['answer']);

            // 题目解析
            $question_item['explain_'] = $question_item['analysis'];
            unset($question_item['analysis']);

            // 题目统计
            $question_item['false_count'] = 0;
            $question_item['true_count'] = 0;
            $question_item['wrong_rate'] = 0;
            $question_item['a_count'] = 0;
            $question_item['b_count'] = 0;
            $question_item['c_count'] = 0;
            $question_item['d_count'] = 0;


            // 题目类型
            $question_item['option_type'] = $question_item['type'] - 1 . '';
            unset($question_item['type']);

            // 收藏记录
            $question_item['collection'] = 0;  // 收藏初始值为0


            // 答题记录
            if ($question_record_items[$qid]) {
                $question_item['answer'] = $question_record_items[$qid]['result'];  // 实际上它是result
                $question_item['choose'] = $question_record_items[$qid]['choose'];
            }
        }




    }
}