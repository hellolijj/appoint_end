<?php

namespace Api\Logic;

use Api\Model\QuestionBankModel;
use Api\Model\QuestionCollectionModel;
use Api\Service\ExaminationService;
use Api\Service\QuestionCollectService;
use Api\Service\QuestionRecordService;
use Api\Service\QuestionService;
use Api\Service\QuestionSpecialService;

class QuestionLogic extends UserBaseLogic {
    /*
     * 获取get_试题ids列表
     */

    public $cid = 0;
    public $sid = 0;


    /*
     * 获取该课程的当前模块信息
     */
    public static function get_current ($course_id)
    {
        if (!$course_id) {
            return ['success' => FALSE, 'message' => '参数错误'];
        }
        // 收藏数量 错题数量
        $question_collection_id_arr = D('QuestionCollection')->getIdsByUidAndCid(session('uid'), $course_id);
        $collection_count = count($question_collection_id_arr);
        $question_wrong_record_id_arr = D('QuestionRecord')->getIdsByUidAndCid(session('uid'), $course_id);
        $wrong_record_count = count($question_wrong_record_id_arr);

        $question = D('Question')->getByCid($course_id);
        $question_set = D('QuestionSet')->getById($question['set_id']);
        $question_set_title = $question_set['title'];
        $question_set_count = $question_set['count'];

        $data = ["subjectHeader" => $question_set_title, "subject" => "kemu3", "titleTota" => $question_set_count, "highest" => 0, "collection" => $collection_count, "answerError" => $wrong_record_count];
        return ['success' => TRUE, 'data' => $data];
    }

    /*
     * parms type
     *     'wdsc' => 收藏
     *     'wdct' => 错题回顾
     */
    public function get_id_items ()
    {


        $chapter_id = intval(I('chapterID'));
        $type = trim(I('type'));
        $course_id = 7;
        $type = 'mnks';
        $this->uid = 12012;
        $this->cid = $course_id;
        $this->sid = 1001;


        if ($chapter_id) {
            $question_id_arr = D('QuestionBank')->getIdsByChapterid($chapter_id);
        } elseif (in_array($type, QuestionBankModel::$TYPE)) {
            $question_id_arr = D('QuestionBank')->getIdsBySidAndType($this->sid, $type);
        } elseif ($type == 'wdsc') {
            $question_id_arr = D('QuestionCollection')->getIdsByUidAndCid($this->uid, $this->cid);
        } elseif ($type == 'wdct') {
            $question_id_arr = D('QuestionRecord')->getIdsByUidAndCid($this->uid, $this->cid);
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



        $question_ids = str_replace('[', '', $question_ids);
        $question_ids = str_replace(']', '', $question_ids);
        if (!$cid || !$question_ids) {
            return ['status' => 0, 'data' => '参数为空',];
        }
        $question_id_arr = explode(',', $question_ids);
        $question_items = D('QuestionBank')->getByIds($question_id_arr);
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
     * 收藏该题目
     */
    public function collect ()
    {
        $cid = intval(I('course_id'));
        $qid = intval(I('questionID'));
        $collection = intval(I('collection'));  //  前端传过来的是 0 or 1
        if (!$cid || !$qid) {
            return ['status' => 0, 'data' => '参数错误',];
        }

        if (!in_array($collection, [QuestionCollectionModel::$STATUS_COLLECT, QuestionCollectionModel::$STATUS_COLLECT_CANCEL])) {
            return ['status' => 0, 'data' => '参数错误'];
        }

        $questionCollectService = new QuestionCollectService();
        $questionCollectService->set_collect($this->uid, $cid, $qid, $collection);

        return ['status' => 0, 'data' => '成功'];
    }

    /*
     * 获取章节列表接口
     */
    public function chapter ()
    {
        $question_chapter_items = D('QuestionChapter')->listBySid($this->sid);
        if (empty($question_chapter_items) || count($question_chapter_items) == 0) {
            return ['status' => 0, 'data' => '没有题库'];
        }
        return ['status' => 1, 'msg' => '成功', 'data' => $question_chapter_items];
    }

    /*
     * 专题训练
     */
    public function special ()
    {

        $questionSpeicialService = new QuestionSpecialService();
        $question_special_items = $questionSpeicialService->test();

        return ['status' => 1, 'msg' => '成功', 'data' => $question_special_items];
    }


    /*
     * 模拟考试提交试卷
     */
    public function hand_paper ()
    {

        $data = ['mid' => 'Mp20180225195814703967', 'score' => 65,];
        return ['status' => 1, 'msg' => '成功', 'data' => $data];
    }


}