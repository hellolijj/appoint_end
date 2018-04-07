<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/2/28
 * Time: 下午1:49
 */

namespace Api\Model;

class QuestionSetModel extends BaseModel {


    public function listAll ()
    {

        $cache_key = 'pingshifen_question_set';
        $cache_value = S($cache_key);
        if ($cache_value) {
            return json_decode(S($cache_key), TRUE);
        }
        $set_items = $this->where(['status' => 1])->select();
        if ($set_items) {
            S($cache_key, json_encode($set_items), 3600);
        }
        return $set_items;
    }
}