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
    public function load_user_back() {

        // 将新加入的数据表 添加进入到 user_back里，以passport为键值，若重复则，不该，如新的，则添加

        $new_data = M('user_back_1')->select();

        p($new_data);



    }
}