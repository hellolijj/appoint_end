<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/1/27
 * Time: 下午10:00
 */

namespace Api\Logic;

use Think\Controller;

class SchoolLogic extends Controller {


    public function search ()
    {
        $s_name = I('s_name');
        $page = intval(I('page'));
        $page_size = intval(I('page_size'));
        if (!$s_name) {
            return ['success' => FALSE, 'message' => '参数为空', 'data' => NULL, 'is_openid' => session('openid') ? TRUE : FALSE];
        }
        $page = empty($page) ? 1 : $page;
        $page_size = empty($page_size) ? 20 : $page_size;
        $where['name'] = ['LIKE', '%' . $s_name . '%'];
        $SCHOOL = D('School');
        $school_lists = $SCHOOL->listByPageWhere($where, $page, $page_size, 'id');
//        $total_count = $SCHOOL->countByWhere($where);
//        $this->hasMorePage($total_count, $page, $page_size);

        if ($school_lists) {
            return ['success' => TRUE, 'message' => '获取学校成功', 'data' => $school_lists, 'is_openid' => session('openid') ? TRUE : FALSE];
        } else {
            return ['success' => FALSE, 'message' => '搜索不到学校', 'data' => NULL, 'is_openid' => session('openid') ? TRUE : FALSE];
        }
    }


}