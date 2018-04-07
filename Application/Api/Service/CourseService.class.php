<?php

namespace Api\Service;

use Api\Logic\QuestionLogic;
use Api\Model\CourseModel;
use Api\Model\WeixinModel;

class CourseService extends BaseService {


    public function create ($uid, $course_name, $course_class_name, $course_logo, $course_remark)
    {
        $data = ['uid' => $uid, 'name' => $course_name, 'class_name' => $course_class_name, 'logo' => $course_logo, 'remark' => $course_remark, 'status' => 1, 'gmt_create' => time(), 'gmt_modified' => time(),];
        $COURSE = M('Course');
        $create_result = $COURSE->add($data);
        if (!$create_result) {
            return ['success' => FALSE, 'message' => $COURSE->getError()];
        }
        return ['success' => TRUE, 'data' => $create_result, 'message' => '数据添加成功'];
    }

    public function list_in_use_for_teacher ($tid, $page, $page_size)
    {
        if (!$tid || !is_numeric($tid)) {
            return ['success' => FALSE, 'message' => 'uid参数错误'];
        }
        $teacher = D('Teacher')->getById($tid);
        $course_lists = D('Course')->getCourseByUid($tid, $page, $page_size);
        foreach ($course_lists as &$course_list) {
            $course_list['teacher'] = ['name' => $teacher['name'], 'school' => $teacher['school'],];
        }
        return $course_lists;
    }

    public function list_in_lock_for_teacher ($tid)
    {
        if (!$tid || !is_numeric($tid)) {
            return ['success' => FALSE, 'message' => 'uid参数错误'];
        }
        $teacher = D('Teacher')->getById($tid);
        $course_lists = D('Course')->listLockCourseByUid($tid);
        foreach ($course_lists as &$course_list) {
            $course_list['teacher'] = ['name' => $teacher['name'], 'school' => $teacher['school'],];
        }
        return $course_lists;
    }

    /*
     * 输入course_id 添加课程
     * todo 1、教师用户不能添加课程 2、你已经添加了该课程不能重复添加
     */
    public function add ($uid, $course_id)
    {
        if (!$course_id || !$uid) {
            return ['success' => FALSE, 'message' => 'uid参数错误'];
        }
        $is_add_result = D('Class')->getByUidAndCid($uid, $course_id);
        if ($is_add_result) {
            return ['success' => FALSE, 'message' => '该课程你已加入，不能重复添加'];
        }
        $data = ['uid' => $uid, 'cid' => $course_id, 'gmt_create' => time(), 'gmt_modified' => time(),];
        M('Class')->add($data);
        M('Course')->where(['id' => $course_id])->setInc('count');
    }

    public function search ($course_id)
    {
        if (!$course_id || !is_numeric($course_id)) {
            return ['success' => FALSE, 'message' => 'uid参数错误'];
        }
        $course = D('Course')->cache(60)->find($course_id);
        if (empty($course)) {
            return ['success' => FALSE, 'message' => '该课程不存在'];
        }
        $uid = $course['uid'];
        $teacher = D('Teacher')->cache(60)->find($uid);
        $course['school'] = $teacher['school'];
        $course['teacher_name'] = $teacher['name'];
        $course['is_ok'] = TRUE;

        // 对课程状态判断 1、正常 2、已经加入 3、课程已经锁定
        if ($course['status'] == CourseModel::$STATUS_LOCKED) {
            $course['is_ok'] = FALSE;
            $course['err_message'] = '该课程已锁定不能加入';
        }
        return ['success' => TRUE, 'data' => $course];
    }


    public function get_current_course ($uid, $user_type, $course_id = '')
    {
        if (!$uid || !$user_type || !is_numeric($course_id)) {
            return ['success' => FALSE, 'message' => '参数不能为空'];
        }
        if (!in_array($user_type, [WeixinModel::$USER_TYPE_STUDENT, WeixinModel::$USER_TYPE_TEACHER])) {
            return ['success' => FALSE, 'message' => '用户类型错误'];
        }
        if ($user_type == WeixinModel::$USER_TYPE_TEACHER) {
            $where['uid'] = $uid;
            if ($course_id) {
                $where['id'] = $course_id;
            }
            $course = D('Course')->cache(60)->where($where)->order('gmt_create desc')->find();
            if (!$course) {
                return ['success' => FALSE, 'message' => '你还没有课程，快去创建课程吧～'];
            }
        } elseif ($user_type == WeixinModel::$USER_TYPE_STUDENT) {
            unset($where);
            $where['uid'] = $uid;
            if ($course_id) {
                $where['cid'] = $course_id;
            }
            $CLASS = D('Class');
            $class = $CLASS->cache(60)->where($where)->order('gmt_create desc')->find();
            if (!$class) {
                if ($course_id) {
                    return ['success' => FALSE, 'message' => 'NO_JOIN'];
                }
                return ['success' => FALSE, 'message' => '你没有加入该课程，快去加入吧'];
            }
            $course = D('Course')->cache(60)->order('gmt_create desc')->find($class['cid']);
            if (!$course) {
                return ['success' => FALSE, 'message' => '你还没有课程，快去加入课程吧～'];
            }
        }

        $tid = $course['uid'];
        $teacher = D('Teacher')->getById($tid);
        $course['teacher'] = ['name' => $teacher['name'], 'school' => $teacher['school']];

        $course_question_model = QuestionLogic::get_current($course['id']);

        if ($course_question_model['success'] === FALSE) {
            return $course_question_model;
        }
        $course['question'] = [$course_question_model['data']];
        return ['success' => TRUE, 'data' => $course];
    }


}