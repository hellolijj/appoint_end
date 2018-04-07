<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/2/25
 * Time: 下午12:57
 */

namespace Api\Model;

class QuestionRecordModel extends BaseModel {

    public static $RESULT_RIGHT = 1;
    public static $RESULT_WRONG = 2;


    public function getByUidAndQids ($uid, $qids)
    {
        if (!$uid || count($qids) == 0) {
            return ['success' => FALSE, '参数错误'];
        }

        // todo 考虑添加缓存机制 ，但是这张表又读又写 不好分离
        $question_record = $this->where(['uid' => $uid, 'qid' => ['IN', implode(',', $qids)]])->select();
        return $question_record;
    }

    /*
     * 注: 这里的ids 实际上是 qids
     */
    public function getIdsByUidAndCid ($uid, $cid)
    {
        if (!$uid || !$cid) {
            return ['success' => FALSE, '参数错误'];
        }
        $question_items = $this->where(['uid' => $uid, 'cid' => $cid, 'result' => self::$RESULT_WRONG])->field('qid as id')->select();
        $question_items = result_to_map($question_items, 'id');

        return $question_items;
    }
}