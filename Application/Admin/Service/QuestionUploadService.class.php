<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/3/3
 * Time: 下午2:29
 */

namespace Admin\Service;

class QuestionUploadService {

    public static $OPTION_CONVERT = ['题干' => 'contents', '选项A' => 'option_a', '选项B' => 'option_b', '选项C' => 'option_c', '选项D' => 'option_d', '正确答案' => 'answer', '解析' => 'analysis',

        '章节' => 'chapter', '类型' => 'type',];

    public static $QUESTION_TYPE = ['判断题' => '1', '单选题' => '2', '多选题' => '3',];


    public function add_question ($data)
    {

    }

    public function option_convert ($answer)
    {
        $answer = strtoupper(trim($answer));

        if (!$answer) {
            die('不能为空');
        }

        if (is_numeric($answer)) {
            return $answer;
        }
        if ($answer == '对' || $answer == 'A') {
            return 16;
        }
        if ($answer == '错' || $answer == 'B') {
            return 32;
        }
        if ($answer == 'C') {
            return 64;
        }
        if ($answer == 'D') {
            return 128;
        }

        // 多选题
        $res = 0;

        $mutipl_arr = str_split($answer, 1);
        foreach ($mutipl_arr as $_mutipl) {
            if ($_mutipl == 'A') {
                $res += 16;
            } elseif ($_mutipl == 'B') {
                $res += 32;
            } elseif ($_mutipl == 'C') {
                $res += 64;
            } elseif ($_mutipl == 'D') {
                $res += 128;
            } else {
                var_dump($mutipl_arr);
                die('出现了未知的情况');
            }
        }
        return $res;
    }


    /*
     * 添加试题集
     */
    public function add_set ($title, $uid = 145, $count = 0)
    {
        if (!$title) {
            return FALSE;
        }

        $data = ['uid' => $uid, 'title' => $title, 'count' => $count, 'gmt_create' => time(), 'gmt_modified' => time(),];
        $map = ['uid' => $uid, 'title' => $title,];

        if (M('question_set')->where($map)->find()) {
            return FALSE;
        }

        $set_id = M('Question_set')->add($data);
        if (!$set_id) {
            return FALSE;
        }

        return $set_id;


    }

    public function add_chapter ($sid, $title, $count = 0)
    {
        if (!$sid || !$title) {
            return FALSE;
        }

        $data = ['sid' => $sid, 'title' => $title, 'count' => $count, 'gmt_create' => time(), 'gmt_modified' => time(),];
        $map = ['sid' => $sid, 'title' => $title,];

        if (M('question_chapter')->where($map)->find()) {
            return FALSE;
        }
        $chapter_id = M('question_chapter')->add($data);
        if (!$chapter_id) {
            return FALSE;
        }
        return $chapter_id;
    }


}