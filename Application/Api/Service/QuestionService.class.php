<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/2/23
 * Time: 下午2:47
 */

namespace Api\Service;

class QuestionService extends BaseService {

    /*
     * 获取题目的ids
     */
    public function add_resutlt_and_collection (&$question_item_arr, $uid)
    {

        $question_id_arr = result_to_array($question_item_arr, 'id');
        $question_record_items = D('QuestionRecord')->getByUidAndQids($uid, $question_id_arr);
        $question_record_items = result_to_map($question_record_items, 'qid');

        $question_collection_items = D('QuestionCollection')->getByUidAndQids($uid, $question_id_arr);
        $question_collection_items = result_to_map($question_collection_items, 'qid');


        foreach ($question_item_arr as &$question_item) {
            $question_item['question_id'] = $question_item['id'];
            $qid = $question_item['id'];

            // 添加 答题记录
            if ($question_record_items[$qid]) {
                $question_item['answer'] = $question_record_items[$qid]['result'];  // 实际上它是result
                $question_item['choose'] = $question_record_items[$qid]['choose'];
            }

            // 添加收藏记录
            $question_item['collection'] = 0;
            if ($question_collection_items[$qid]) {
                $question_item['collection'] = intval($question_record_items[$qid]['status']);
            }

            unset($question_item['id']);
        }
    }

    /*
     * 给题目增加其他的字段
     */
    public function add_more_field ($uid, &$question_items)
    {
        $question_id_arr = result_to_array($question_items, 'id');
        $question_record_items = D('QuestionRecord')->getByUidAndQids($uid, $question_id_arr);
        $question_record_items = result_to_map($question_record_items, 'qid');

        $question_collection_items = D('QuestionCollection')->getByUidAndQids($uid, $question_id_arr);
        $question_collection_items = result_to_map($question_collection_items, 'qid');

        $question_count_items = D('QuestionCount')->getByQids($question_id_arr);
        $question_count_items = result_to_map($question_count_items, 'qid');



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
            if ($question_count_items[$qid]) {
                $question_item['false_count'] = intval($question_count_items[$qid]['wrong_cnt']);
                $question_item['true_count'] = intval($question_count_items[$qid]['right_cnt']);
                $total = $question_item['false_count'] + $question_item['true_count'];
                if ($total) {
                    $question_item['wrong_rate'] = round($question_item['false_count'] / $total, 2);
                }
                $question_item['a_count'] = intval($question_count_items[$qid]['option_a']);
                $question_item['b_count'] = intval($question_count_items[$qid]['option_b']);
                $question_item['c_count'] = intval($question_count_items[$qid]['option_c']);
                $question_item['d_count'] = intval($question_count_items[$qid]['option_d']);
            }


            // 题目类型
            $question_item['option_type'] = $question_item['type'] - 1 . '';
            unset($question_item['type']);

            // 收藏记录
            $question_item['collection'] = 0;  // 收藏初始值为0
            if ($question_collection_items[$qid]) {
                $question_item['collection'] = intval($question_record_items[$qid]['status']);
            }

            // 答题记录
            if ($question_record_items[$qid]) {
                $question_item['answer'] = $question_record_items[$qid]['result'];  // 实际上它是result
                $question_item['choose'] = $question_record_items[$qid]['choose'];
            }

        }

    }
}