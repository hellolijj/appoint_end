<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/2/22
 * Time: 下午11:01
 */

namespace Admin\Controller;

use Think\Controller;

class ScriptController extends Controller {

    /*
     * 整理大学近现代史平台
     *
     */
    public function jxds_plat ()
    {
        die('慎重，此脚本仅仅运行此一次');

        $orgin_bank = M('hs_questionbank');
        $orgin_data = $orgin_bank->limit()->select();
        foreach ($orgin_data as &$item) {

            // 处理多选题
            if ($item['answer'] == '对' || $item['answer'] == '错') {
                $item['option_a'] = '正确';
                $item['option_b'] = '错误';
            }
            // 处理类型
            $item['type']++;

            $item['answer'] = $this->convert_jxds_answer($item['answer']);
            $item['gmt_create'] = time();
            $item['gmt_modified'] = time();
            unset($item['id']);
        }
        /*echo "<pre>";
        var_dump($orgin_data);*/
        // M('question_bank')->addAll($orgin_data);
    }

    public function newer_plat ()
    {
        $orgin_bank = M('cn_questionbank');
        $orgin_data = $orgin_bank->limit()->select();
        $start_id = M('question_bank')->max('chapter_id');
        $set_id = M('question_set')->max('id') + 1;
        foreach ($orgin_data as &$item) {
            // 处理多选题
            if ($item['right_answer'] == '对' || $item['right_answer'] == '错') {
                $item['option_a'] = '正确';
                $item['option_b'] = '错误';
            }

            $item['answer'] = $this->convert_newer_answer($item['right_answer']);
            unset($item['right_answer']);

            // add new field
            $item['set_id'] = $set_id;
            $item['chapter_id'] = $item['chapter'] + $start_id;
            unset($item['chapter']);

            $item['content'] = $item['contents'];
            unset($item['contents']);

            $item['gmt_create'] = strtotime($item['time']);
            $item['gmt_modified'] = strtotime($item['time']);
            unset($item['time']);

            unset($item['id']);
        }
        /*echo "<pre>";
        var_dump($orgin_data);*/
        M('question_bank')->addAll($orgin_data);


    }

    /*
     * 新生教育平台
     */

    public function add_chpater_count ()
    {

        die('这是更新chapter数量的脚步，只能运行一次');
        $chapters = M('question_chapter')->select();
        foreach ($chapters as $chapter) {
            $chapter_id = $chapter['id'];
            $count = M('question_bank')->where(['chapter_id' => $chapter_id])->count();
            var_dump($count);
            //            M('question_chapter')->where(['id' => $chapter_id])->save(['count' => $count]);
        }
    }

    private function convert_jxds_answer ($answer)
    {
        if (!$answer) {
            die('不能为空');
        }

        if (is_numeric($answer)) {
            return $answer;
        }
        if ($answer == '对') {
            return 16;
        }
        if ($answer == '错') {
            return 32;
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
                echo '位置情况';
            }
        }
        return $res;
    }

    private function convert_newer_answer ($answer)
    {

        die('该程序不能运行');
        $answer = trim($answer);

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


    public function erji_plat ()
    {
        $erji_items = M('co_2016_testerji', '')->where(['test_num' => 5])->select();
        $chapter_id = 26;
        $set_id = 1004;

        $option = ['A' => 16, 'B' => 32, 'C' => 64, 'D' => 128];

        $data = [];
        echo "<pre>";
        //        var_dump($erji_items);
        foreach ($erji_items as $erji_item) {
            $data['content'] = $erji_item['question'];
            $data['option_a'] = str_replace('[A]', '', $erji_item['opta']);
            $data['option_b'] = str_replace('[B]', '', $erji_item['optb']);
            $data['option_c'] = str_replace('[C]', '', $erji_item['optc']);
            $data['option_d'] = str_replace('[D]', '', $erji_item['optd']);
            $data['answer'] = $option[$erji_item['rightans']];
            $data['analysis'] = '';
            $data['gmt_create'] = time();
            $data['gmt_modified'] = time();

            /* add more */
            $data['set_id'] = $set_id;
            $data['chapter_id'] = $chapter_id;
            $data['type'] = 1;

            M('question_bank')->add($data);
        }

    }
}