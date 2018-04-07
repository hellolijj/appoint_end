<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/2/24
 * Time: 上午10:18
 */

namespace Api\Service;

use Api\Model\QuestionBankModel;
use Api\Model\QuestionRecordModel;

class QuestionRecordService extends BaseService {

    /*
     * 添加一条答题记录
     */
    public function add ($uid, $cid, $qid, $choose, $result)
    {
        if (!$uid || !$cid || !$qid || !$choose || !$result) {
            return ['success' => FALSE, 'message' => '参数不能为空'];
        }
        $question_item = D('QuestionBank')->getById($qid);
        if (!$question_item) {
            return ['success' => FALSE, 'message' => '获取正确答案错误'];
        }
        $answer = $question_item['answer'];
        $data = ['uid' => $uid, 'cid' => $cid, 'qid' => $qid, 'choose' => $choose, 'answer' => $answer, 'result' => $result, 'gmt_create' => time(), 'gmt_modified' => time()];
        M('Question_record')->add($data);
        $this->update_count($qid, $choose, $result);
        return ['success' => TRUE];
    }

    public function update_count ($qid, $choose, $result)
    {
        if (!$qid || !$choose || !$result) {
            return FALSE;
        }
        $QuestionCount = D('Question_count');
        $question_item = $QuestionCount->getByQid($qid);
        if (empty($question_item)) {
            $QuestionCount->initialize($qid);
            $question_item = $QuestionCount->getByQid($qid);
        }

        //$choose_化为 ABCD 选项
        $data = [];
        if ($choose >= QuestionBankModel::$OPTION_D) {
            $data['option_d'] = $question_item['option_d'] + 1;
            $choose -= QuestionBankModel::$OPTION_D;
        }
        if ($choose >= QuestionBankModel::$OPTION_C && $choose < QuestionBankModel::$OPTION_D) {
            $data['option_c'] = $question_item['option_c'] + 1;
            $choose -= QuestionBankModel::$OPTION_C;
        }
        if ($choose >= QuestionBankModel::$OPTION_B && $choose < QuestionBankModel::$OPTION_C) {
            $data['option_b'] = $question_item['option_b'] + 1;
            $choose -= QuestionBankModel::$OPTION_B;
        }
        if ($choose >= QuestionBankModel::$OPTION_A && $choose < QuestionBankModel::$OPTION_B) {
            $data['option_a'] = $question_item['option_a'] + 1;
        }
        if ($result == QuestionRecordModel::$RESULT_RIGHT) {
            $data['right_cnt'] = $question_item['right_cnt'] + 1;
        } elseif ($result == QuestionRecordModel::$RESULT_WRONG) {
            $data['wrong_cnt'] = $question_item['wrong_cnt'] + 1;
        }

        $data['gmt_modified'] = time();

        M('Question_count')->where(['qid' => $qid])->save($data);
    }
}