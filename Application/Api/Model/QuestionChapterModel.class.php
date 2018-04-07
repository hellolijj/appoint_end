<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/2/25
 * Time: 下午4:26
 */

namespace Api\Model;


class QuestionChapterModel extends BaseModel {

    public function listBySid ($sid)
    {
        if (!$sid) {
            return FALSE;
        }

        $question_chapter_items = $this->where(['sid' => $sid])->select();

        return $question_chapter_items;
    }
}