<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/2/17
 * Time: 下午7:51
 */


namespace Api\Service;


class ClassService extends BaseService {

    /*
     * 判断uid是否加入课程
     */
    public function is_first_add ($uid, $course_id)
    {
        if (!$uid || !$course_id) {
            return ['success' => FALSE, 'message' => '参数不能为空'];
        }
        $Class = D('Class');
        $is_add_result = $Class->getByUidAndCid($uid, $course_id);
        if ($is_add_result) {
            return ['success' => FALSE, 'message' => '你已经加入了'];
        }
        return ['success' => TRUE];
    }

    /*
     * 判断uid是否加入课程cid
     * @return true || false || array
     */
    public function is_join_course ($uid, $course_id)
    {
        if (!$uid || !$course_id) {
            return ['success' => FALSE, 'message' => '参数不能为空'];
        }
        $Class = D('Class');
        $is_add_result = $Class->getByUidAndCid($uid, $course_id);
        if (!empty($is_add_result)) {
            return TRUE;
        }
        return FALSE;
    }

    /*
     * 罗列出学生所有在使用的课程
     */
    public function list_in_use_for_student ($sid, $page, $page_size)
    {
        if (!$sid || !is_numeric($sid)) {
            return ['success' => FALSE, 'message' => '参数错误'];
        }
        // todo uid -> cid -> tid
        $classes = D('Class')->getClassByUid($sid);
        if (empty($classes)) {
            return ['success' => FALSE, 'message' => '你还没有加入课程'];
        }
        $course_ids = result_to_array($classes, 'cid');
        $courses = D('Course')->getCourseByUids($course_ids, $page, $page_size);
        if (!$courses) {
            return ['success' => FALSE, 'message' => '无锁定课程'];
        }
        $tids = result_to_array($courses, 'uid');
        $teachers = D('Teacher')->getByIds($tids);
        $teachers_result = result_to_map($teachers, 'id');
        foreach ($courses as $key => &$course) {
            $tid = $course['uid'];  //教师uid
            // 添加教师信息
            if ($teachers_result[$tid]) {
                $course['teacher'] = ['name' => $teachers_result[$tid]['name'], 'school' => $teachers_result[$tid]['school'],];
            }
        }
        return ['success' => TRUE, 'data' => $courses];
    }

    public function list_in_lock_for_student ($sid)
    {
        if (!$sid || !is_numeric($sid)) {
            return ['success' => FALSE, 'message' => '参数错误'];
        }
        $classes = D('Class')->getClassByUid($sid);
        if (empty($classes)) {
            return ['success' => FALSE, 'message' => '你还没有加入课程'];
        }
        $course_ids = result_to_array($classes, 'cid');
        $courses = D('Course')->listLockCourseByCids($course_ids);
        if (!$courses) {
            return ['success' => FALSE, 'message' => '查不到课程'];
        }
        $tids = result_to_array($courses, 'uid');
        $teachers = D('Teacher')->getByIds($tids);
        $teachers_result = result_to_map($teachers, 'id');
        foreach ($courses as $key => &$course) {
            $tid = $course['uid'];  //教师uid
            // 添加教师信息
            if ($teachers_result[$tid]) {
                $course['teacher'] = ['name' => $teachers_result[$tid]['name'], 'school' => $teachers_result[$tid]['school'],];
            }
        }
        return ['success' => TRUE, 'data' => $courses];


    }
}