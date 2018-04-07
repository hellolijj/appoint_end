<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/1/27
 * Time: 下午1:22
 */

namespace Api\Service;

class StudentService extends BaseService {

    public function __construct ()
    {
        parent::__construct();
    }


    public function bind ($name, $tel, $school, $number, $enter_year, $head_img, $sex)
    {
        $data = ['name' => $name, 'tel' => $tel, 'school' => $school, 'number' => $number, 'enter_year' => $enter_year, 'head_img' => $head_img, 'sex' => $sex, 'gmt_create' => time(), 'gmt_modified' => time(),];
        $openid = session('openid');
        $weixinService = new WeixinService();
        $is_bind = $weixinService->is_bind($openid, 'student');
        if (TRUE === $is_bind) {
            return ['success' => TRUE, 'message' => '已经绑定过了'];
        } elseif (is_array($is_bind)) {
            return $is_bind;
        } else {
            $uid = M('Student')->add($data);
            $weixinService->BeStudent($openid, $uid);
            return TRUE;
        }
    }

    /*
     * 获取学生用户信息
     */
    public function getStudentInfo ($uid)
    {
        if (!$uid || !is_numeric($uid)) {
            return ['success' => FALSE, 'message' => 'uid参数错误'];
        }
        $cache_key = 'student_uid_' . $uid;
        $student_user = json_decode(S($cache_key), TRUE);
        if (!count($student_user)) {
            $student_user = M('Student')->getById($uid);
            if (!$student_user) {
                return ['success' => FALSE, 'message' => '读取学生信息失败'];
            }
            S($cache_key, json_encode($student_user), 3600);
        }
        return ['success' => TRUE, 'data' => $student_user];
    }


}