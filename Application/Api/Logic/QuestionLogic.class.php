<?php

namespace Api\Logic;

use Api\Model\QuestionBankModel;
use Api\Service\ExaminationService;
use Api\Service\QuestionRecordService;
use Api\Service\QuestionService;

class QuestionLogic extends UserBaseLogic {

    public $cid = 0;
    public $sid = 0;



    /*
     * parms type
     *     'wdsc' => 收藏
     *     'wdct' => 错题回顾
     */
    public function get_id_items ()
    {
        $course_id = 7;
        $type = 'mnks';
        $this->uid = session('uid');
        $this->cid = $course_id;
        $this->sid = 1011;
        $exam_submit_id = I('mnksRecordID');
        if ($exam_submit_id) {
            return $this->exam_review($exam_submit_id);
        }

        if (in_array($type, QuestionBankModel::$TYPE)) {
            $question_id_arr = D('QuestionBank')->getIdsBySidAndType($this->sid, $type);
        } elseif ($type == 'mnks') {
            $examinationService = new ExaminationService();
            $question_id_arr = $examinationService->set_random_question($this->sid);
        } else {
            $question_id_arr = D('QuestionBank')->getIdsBySid($this->sid);
        }

        if (empty($question_id_arr)) {
            return ['status' => 0, 'data' => '题库没有题目',];
        }
        if ($question_id_arr['success'] === FALSE) {
            return ['status' => 0, 'data' => $question_id_arr['message']];
        }
        // 给数组增加收藏和答题情况
        $questionService = new QuestionService();
        $questionService->add_resutlt_and_collection($question_id_arr, $this->uid);

        return ['status' => 1, 'msg' => '成功', 'count' => count($question_id_arr), 'data' => $question_id_arr,];
    }

    /*
     * 获取题目的全部的内容
     */
    public function get_info ()
    {

        $cid = 7;
        $question_ids = trim(I('questionID'));
        $exam_submit_id = I('mnksRecordID');

        $question_ids = str_replace('[', '', $question_ids);
        $question_ids = str_replace(']', '', $question_ids);

        if (!$cid || !$question_ids) {
            return ['status' => 0, 'data' => '参数为空',];
        }

        $question_id_arr = explode(',', $question_ids);



        $question_items = D('QuestionBank')->getByIds($question_id_arr);

        if ($exam_submit_id) {
            return $this->exam_review_info($exam_submit_id, $question_items);
        }
        // 给数组增加其他的字段
        $questionService = new QuestionService();
        $questionService->add_more_field($this->uid, $question_items);

        return ['status' => 1, 'msg' => '成功', 'data' => $question_items,];

    }

    /*
     * 判断用户提交的答案并记录
     */
    public function submit ()
    {
        $cid = intval(I('course_id'));
        $qid = intval(I('questionID'));
        $result = intval(I('answer'));  // 回答结果判断的正误  1正确 2错误
        $choose = intval(I('choose'));  //

        if (!$cid || !$qid || !$result || !$choose) {
            return ['status' => 0, 'data' => '参数错误',];
        }

        $questionRecordService = new QuestionRecordService();
        $add_record_result = $questionRecordService->add($this->uid, $cid, $qid, $choose, $result);
        if ($add_record_result['success'] === FALSE) {
            return ['status' => 0, 'msg' => $add_record_result['message']];
        }
        return ['status' => 1, 'msg' => '成功'];
    }



    /*
     * 模拟考试提交试卷
     */
    public function hand_paper ()
    {
        $answer = $_GET['record'];
        $answer_arr = json_decode($answer, TRUE);
        $time = intval(I('useTime'));

        if (!$time || count($answer_arr) == 0) {
            return ['status' => 0, 'msg' => '提交失败'];
        }


        $score = 0;
        foreach ($answer_arr as &$_answer) {

            if ($_answer['answer'] == 1)
                $score += 2;

            $_answer['qid'] = $_answer['id'];
            unset($_answer['id']);

            $_answer['result'] = $_answer['answer'];
            unset($_answer['answer']);
        }

        $exam_submit_id = D('ExamSubmit')->add(session('uid'), 1, $score);
        if (!$exam_submit_id) {
            return ['status' => 0, 'msg' => '提交失败'];
        }

        D('ExamRecord')->add(session('uid'), $exam_submit_id, $answer_arr);

        $data = ['mid' => $exam_submit_id, 'score' => $score, 'time' => $time];
        return ['status' => 1, 'msg' => '成功', 'data' => $data];
    }

    /**
     * 互殴去答题列表
     */
    public function get_submit_list() {

        $uid = session('uid');

        $exam_lists = D('ExamSubmit')->list_items($uid);
        foreach ($exam_lists as &$exam_list) {
            $exam_list['time'] = date('Y-m-d H:i', $exam_list['gmt_create']);
        }

        return ['status' => 0, 'data' => $exam_lists];
    }

    /**
     * 模拟考试的错题回顾
     */
    private function exam_review($exam_submit_id) {

        if (!$exam_submit_id) {
            return ['status' => 0, 'mgs' => '参数不能为空'];
        }

        $examinationService = new ExaminationService();
        $record_id_arr = $examinationService->get_review($exam_submit_id);
        return ['status' => 1, 'msg' => '成功', 'count' => count($record_id_arr), 'data' => $record_id_arr,];
    }


    /**
     * @param $exam_submit_id 学生用户提交答案id
     * 展示考试错题回顾想详细信息
     */
    private function exam_review_info($exam_submit_id, $question_items) {

        if (!$exam_submit_id || count($question_items) == 0) {
            return ['status' => 0, 'mgs' => '参数不能为空'];
        }

        $examinationService = new ExaminationService();
        $examinationService->add_record_more_at_exam(session('uid'), $exam_submit_id, $question_items);
        return ['status' => 1, 'msg' => '成功', 'data' => $question_items,];
    }

}