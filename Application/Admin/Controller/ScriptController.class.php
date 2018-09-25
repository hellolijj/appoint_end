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


    /**
     * 导入用户入库
     */
    public function load_user_back_1() {

        die('user_back_1已经入库，无需再操作');


        // 将新加入的数据表 添加进入到 user_back里，以passport为键值，若重复则，不该，如新的，则添加

        $NEW_USER = M('user_back_1');
        $new_data = $NEW_USER->select();
        $OLD_USER = M('user_back_deal');

        foreach ($new_data as $_new_data) {

            unset($_new_data['id']);
            if (is_null($_new_data['name'])) $_new_data['name'] = '';
            if (is_null($_new_data['class'])) $_new_data['class'] = '';
            if (is_null($_new_data['number'])) $_new_data['number'] = 0;
            if (is_null($_new_data['entry_year'])) $_new_data['entry_year'] = 0;

            if ($OLD_USER->where(['passport' => $_new_data['passport']])->find()) {
                continue;
            }

            $OLD_USER->add($_new_data);
        }
    }

    public function load_user_back_2() {
        die('user_back_2已经入库，无需再操作');
        // 将新加入的数据表 添加进入到 user_back里，以passport为键值，若重复则，不该，如新的，则添加

        $NEW_USER = M('user_back_2');
        $new_data = $NEW_USER->select();
        $OLD_USER = M('user_back_deal_2');

        foreach ($new_data as $_new_data) {

            unset($_new_data['id']);
            if (is_null($_new_data['first_name'])) $_new_data['first_name'] = '';
            if (is_null($_new_data['last_name'])) $_new_data['last_name'] = '';
            if (is_null($_new_data['name'])) $_new_data['name'] = '';
            if (is_null($_new_data['sex'])) $_new_data['sex'] = '';
            if (is_null($_new_data['country'])) $_new_data['country'] = '';
            if (is_null($_new_data['profession'])) $_new_data['profession'] = '';
            if (is_null($_new_data['college'])) $_new_data['college'] = '';
            if (is_null($_new_data['class'])) $_new_data['class'] = '';
            if (is_null($_new_data['number']) || !is_numeric($_new_data['entry_year'])) $_new_data['number'] = 0;
            if (is_null($_new_data['entry_year']) || !is_numeric($_new_data['entry_year'])) $_new_data['entry_year'] = 0;

            if ($OLD_USER->where(['passport' => $_new_data['passport']])->find()) {
                continue;
            }

            $OLD_USER->add($_new_data);
        }
    }

    public function load_user_back_3() {
        die('user_back_3已经入库，无需再操作');

        // 将新加入的数据表 添加进入到 user_back里，以passport为键值，若重复则，不该，如新的，则添加

        $NEW_USER = M('user_back_3');
        $new_data = $NEW_USER->select();
        $OLD_USER = M('user_back');

        foreach ($new_data as $_new_data) {

            unset($_new_data['id']);
            if (is_null($_new_data['first_name'])) $_new_data['first_name'] = '';
            if (is_null($_new_data['last_name'])) $_new_data['last_name'] = '';
            if (is_null($_new_data['name'])) $_new_data['name'] = '';
            if (is_null($_new_data['sex'])) $_new_data['sex'] = '';
            if (is_null($_new_data['country'])) $_new_data['country'] = '';
            if (is_null($_new_data['profession'])) $_new_data['profession'] = '';
            if (is_null($_new_data['college'])) $_new_data['college'] = '';
            if (is_null($_new_data['class'])) $_new_data['class'] = '';
            if (is_null($_new_data['number']) || !is_numeric($_new_data['entry_year'])) $_new_data['number'] = 0;
            if (is_null($_new_data['entry_year']) || !is_numeric($_new_data['entry_year'])) $_new_data['entry_year'] = 0;

            // passprot 去空格影响
            $_new_data['passport'] = trim($_new_data['passport']);

            if ($OLD_USER->where(['passport' => $_new_data['passport']])->find()) {
                continue;
            }

            $OLD_USER->add($_new_data);
        }
    }
}