<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/2/23
 * Time: 下午2:53
 */

namespace Api\Model;

class QuestionBankModel extends BaseModel {

    public static $OPTION_A = 16;
    public static $OPTION_B = 32;
    public static $OPTION_C = 64;
    public static $OPTION_D = 128;

    public static $TYPE_JUDGE = 1;
    public static $TYPE_SINGLE = 2;
    public static $TYPE_MULTIPLE = 3;

    public static $TYPE = [1 => 'pdlx', 2 => 'dxlx', 3 => 'dclx',];

    public static $TYPE_MEDIAL = 4;


    /*
     * 根据sid获取题目列表
     */
    public function getIdsBySid ($sid)
    {
        if (!$sid) {
            return ['success' => FALSE, 'message' => '参数不能为空'];
        }
        $cache_key = 'pingshifen_question_bank_by_sid_' . $sid;
        $cache_value = F($cache_key);
        if ($cache_value) {
            return json_decode(F($cache_key), TRUE);
        }
        $question_items = $this->where(['set_id' => $sid])->field('id')->select();
        if ($question_items) {
            F($cache_key, json_encode($question_items));
        }
        return $question_items;
    }


    public function getByIds (array $id_arr, $page = 1, $page_size = 6)
    {
        if (!$id_arr) {
            return FALSE;
        }
        $cache_key = 'pingshifen_question_by_ids_' . json_encode($id_arr);
        $cache_value = F($cache_key);
        if ($cache_value) {
            return json_decode(F($cache_key), TRUE);
        }
        $question_items = $this->where(['id' => ['IN', implode(',', $id_arr)]])->select();
        if ($question_items) {
            F($cache_key, json_encode($question_items));
        }
        return $question_items;
    }

    public function getIdsByChapterId ($chapter_id)
    {
        if (!$chapter_id) {
            return ['success' => FALSE, 'message' => '参数不能为空'];
        }
        $cache_key = 'pingshifen_question_bank_by_chapter_id_' . $chapter_id;
        $cache_value = F($cache_key);
        if ($cache_value) {
            return json_decode(F($cache_key), TRUE);
        }
        $question_items = $this->where(['chapter_id' => $chapter_id])->field('id')->select();
        if ($question_items) {
            F($cache_key, json_encode($question_items));
        }
        return $question_items;
    }

    public function getIdsBySidAndType ($sid, $type)
    {
        if (!$type || !$sid) {
            return ['success' => FALSE, 'message' => '参数不能为空'];
        }
        if (!in_array($type, QuestionBankModel::$TYPE)) {
            return ['success' => FALSE, 'message' => '题目类型不对'];
        }
        $cache_key = 'pingshifen_question_bank_by_sid_' . $sid . '_type_' . $type;
        $cache_value = F($cache_key);
        if ($cache_value) {
            return json_decode(F($cache_key), TRUE);
        }
        $question_items = $this->where(['set_id' => $sid, 'type' => array_search($type, QuestionBankModel::$TYPE)])->field('id')->select();
        if ($question_items) {
            F($cache_key, json_encode($question_items));
        }
        return $question_items;
    }

}