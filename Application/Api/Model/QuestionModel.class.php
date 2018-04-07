<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/2/23
 * Time: 下午2:37
 */

namespace Api\Model;

class QuestionModel extends BaseModel {

    public function getByCid ($cid)
    {
        if (!$cid) {
            return FALSE;
        }
        $cache_key = 'pingshifen_question_by_id_' . $cid;
        $cache_value = S($cache_key);
        if ($cache_value) {
            return json_decode(S($cache_key), TRUE);
        }
        $question_item = $this->where(['cid' => $cid])->find();
        if ($question_item) {
            S($cache_key, json_encode($question_item), 3600);
        }
        return $question_item;
    }


}