<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/2/28
 * Time: 下午1:52
 */

namespace Api\Service;

class QuestionSetService extends BaseService {

    public function items_add_teacher_info (&$set_items)
    {
        $uids = result_to_array($set_items, 'uid');
        $teachers = D('Teacher')->getByIds($uids);
        $teachers = result_to_map($teachers);
        foreach ($set_items as &$set_item) {
            $uid = $set_item['uid'];
            $set_item['author'] = '系统题库';
            $set_item['school'] = '系统';
            $set_item['logo'] = C('APP_LOGO');
            if ($teachers[$uid]) {
                $set_item['author'] = $teachers[$uid]['name'];
                $set_item['school'] = $teachers[$uid]['school'];
                $set_item['logo'] = $teachers[$uid]['head_img'];
            }
        }

    }
}