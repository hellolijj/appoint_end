<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/2/25
 * Time: 下午2:43
 */

namespace Api\Model;

class QuestionCollectionModel extends BaseModel {

    public static $STATUS_COLLECT = 1;
    public static $STATUS_COLLECT_CANCEL = 0;

    public function getByUidCidQid ($uid, $cid, $qid)
    {
        if (!$uid || !$cid || !$qid) {
            return ['success' => FALSE, 'message' => '参数错误'];
        }

        $cache_key = 'pingshifen_question_collection_by_uid_' . $uid . '_cid_' . $cid . '_qid_' . $qid;
        $cache_value = S($cache_key);
        if ($cache_value) {
            return json_decode(S($cache_key), TRUE);
        }
        $question_collection_item = $this->where(['uid' => $uid, 'cid' => $cid, 'qid' => $qid])->find();
        if ($question_collection_item) {
            S($cache_key, json_encode($question_collection_item));
        }
        return $question_collection_item;
    }

    public function initialize ($uid, $cid, $qid)
    {
        if (!$uid || !$cid || !$qid) {
            return ['success' => FALSE, 'message' => '参数错误'];
        }
        $data = ['uid' => $uid, 'cid' => $cid, 'qid' => $qid, 'status' => self::$STATUS_COLLECT, 'gmt_create' => time(), 'gmt_modified' => time(),];
        $this->add($data);
    }

    public function updateByUidCidQid ($uid, $cid, $qid, $status)
    {
        if (!$uid || !$cid || !$qid) {
            return ['success' => FALSE, 'message' => '参数错误'];
        }
        if (!in_array($status, [self::$STATUS_COLLECT, self::$STATUS_COLLECT_CANCEL])) {
            return ['success' => FALSE, 'message' => '参数错误'];
        }

        $data = ['status' => $status, 'gmt_modified' => time(),];

        $this->where(['uid' => $uid, 'cid' => $cid, 'qid' => $qid])->save($data);

        $cache_key = 'pingshifen_question_collection_by_uid_' . $uid . '_cid_' . $cid . '_qid_' . $qid;
        S($cache_key, NULL);

    }


    public function getByUidAndQids ($uid, $qids)
    {
        if (!$uid || count($qids) == 0) {
            return ['success' => FALSE, '参数错误'];
        }

        // todo 考虑添加缓存机制 ，但是这张表又读又写 不好分离
        $question_collection = $this->where(['uid' => $uid, 'qid' => ['IN', implode(',', $qids)]])->select();
        return $question_collection;
    }

    /*
     * 注: 这里的ids 实际上是 qids
     */
    public function getIdsByUidAndCid ($uid, $cid)
    {
        if (!$uid || !$cid) {
            return ['success' => FALSE, '参数错误'];
        }
        $question_items = $this->where(['uid' => $uid, 'cid' => $cid])->field('qid as id')->select();
        return $question_items;
    }


}