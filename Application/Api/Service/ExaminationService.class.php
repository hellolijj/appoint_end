<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/2/25
 * Time: 下午7:35
 */

namespace Api\Service;

class ExaminationService extends BaseService {

    /*
     * 随机出一百道题目。仅供测试
     */
    public function set_random_question ($sid)
    {
        if (!$sid) {
            return FALSE;
        }
        $question_items = D('QuestionBank')->getIdsBySid($sid);
        $rand_ids = array_rand($question_items, 100);

        // todo 这么做性能不是很好，后续再优化
        $data = [];
        foreach ($rand_ids as $rand_id) {
            $data[] = $question_items[$rand_id];
        }
        return $data;
    }
}