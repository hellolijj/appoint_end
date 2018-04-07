<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/2/25
 * Time: 下午2:31
 */

namespace Api\Service;

use Api\Model\QuestionCollectionModel;

class QuestionCollectService extends BaseService {


    public function set_collect ($uid, $cid, $qid, $status)
    {
        if (!$uid || !$cid || !$qid) {
            return ['success' => FALSE, 'message' => '参数错误'];
        }
        if (!in_array($status, [QuestionCollectionModel::$STATUS_COLLECT, QuestionCollectionModel::$STATUS_COLLECT_CANCEL])) {
            return ['success' => FALSE, 'message' => '参数错误'];
        }

        $QuestionCollection = D('QuestionCollection');
        $question_collection_item = $QuestionCollection->getByUidCidQid($uid, $cid, $qid);
        if (empty($question_collection_item)) {
            $QuestionCollection->initialize($uid, $cid, $qid);
        } else {
            $QuestionCollection->updateByUidCidQid($uid, $cid, $qid, $status);
        }
    }
}