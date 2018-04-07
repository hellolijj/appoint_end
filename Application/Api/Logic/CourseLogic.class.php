<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/2/16
 * Time: 上午12:21
 */

namespace Api\Logic;

use Api\Model\WeixinModel;
use Api\Service\ClassService;
use Api\Service\ClassServie;
use Api\Service\CourseService;
use Api\Service\QuestionSetService;
use Api\Service\SigninRecordService;
use Api\Service\WeixinService;

/*
 * 基于课程的类，调用课程类的用户都是已绑定用户
 */

class CourseLogic extends UserBaseLogic {

    public function __construct ()
    {
        parent::__construct();

    }

    /*
     * 创建一个课程
     */
    public function create ()
    {
        $openid = session('openid');
        $weixinService = new WeixinService();
        $weixin_user_result = $weixinService->getByOpenid($openid);
        if ($weixin_user_result['success'] === FALSE) {
            return $weixin_user_result;
        }
        $uid = $weixin_user_result['data']['uid'];
        $user_type = $weixin_user_result['data']['type'];
        if (!$uid || !is_numeric($uid)) {
            return $this->setError('uid参数错误');
        }
        if ($user_type != WeixinService::$USER_TYPE_TEACHER) {
            return $this->setError('不是教师用户');
        }
        // 入参校验
        $course_name = I('course_name');
        $course_img = I('course_img');
        $course_class_name = I('course_class_name');
        $course_remark = I('course_remark');
        if (!$course_name || !$course_img || !$course_class_name) {
            return $this->setError('参入参数不能为空');
        }
        // todo  课程数量要做限制
        $courseService = new CourseService();
        $crease_result = $courseService->create($uid, $course_name, $course_class_name, $course_img, $course_remark);
        if ($crease_result['success'] === FALSE) {
            return $this->setError($crease_result['message']);
        }
        return $this->setSuccess($crease_result['data'], '添加成功');
    }

    /*
     * 更新课程信息
     */
    public function update ()
    {
        $course_id = intval(I('course_id'));
        // 入参校验
        $course_name = I('course_name');
        $course_img = I('course_img');
        $course_class_name = I('course_class_name');
        $course_remark = I('course_remark');
        if (!$course_name || !$course_img || !$course_class_name || !$course_id) {
            return $this->setError('参入参数不能为空');
        }
        if ($this->user_type != WeixinService::$USER_TYPE_TEACHER) {
            return $this->setError('不是教师用户');
        }
        $data = ['name' => $course_name, 'class_name' => $course_class_name, 'logo' => $course_img, 'remark' => $course_remark, 'gmt_modified' => time(),];
        $Course = D('Course');
        $course_save = $Course->where(['id' => $course_id])->save($data);
        if (!$course_save) {
            return $this->setError($Course->getError());

        }
        return $this->setSuccess(NULL, '更新成功');
    }

    /*
     * list 正在使用的课程
     */
    public function list_in_use ()
    {
        $uid = session('uid');
        $user_type = session('user_type');
        $page = intval(I('page'));
        $page_size = 20;
        if (!$page) {
            $page = 1;
        }
        // 教师用户从course表查找
        if ($user_type == WeixinService::$USER_TYPE_TEACHER) {
            $courseService = new CourseService();
            $course_items = $courseService->list_in_use_for_teacher($uid, $page, $page_size);
            $course_count = D('Course')->countCourseByUid($uid);

        } elseif ($user_type == WeixinService::$USER_TYPE_STUDENT) {
            $classService = new ClassService();
            $course_items_result = $classService->list_in_use_for_student($uid, $page, $page_size);
            if ($course_items_result['success'] === FALSE) {
                return $this->setError($course_items_result['message']);
            }
            $course_items = $course_items_result['data'];
            $course_count = D('Class')->countClassByUid($uid);
        }
        $this->hasMorePage($course_count, $page, $page_size);
        if ($course_items) {
            return $this->setSuccess($course_items, '获取所有课程');
        } else {
            return $this->setError('你还没有创建课程');
        }
    }

    /*
    * list 锁上的所有的课程
    */
    public function list_in_lock ()
    {
        $uid = $this->uid;
        $user_type = $this->user_type;
        // 教师用户从course表查找
        if ($user_type == WeixinService::$USER_TYPE_TEACHER) {
            $courseService = new CourseService();
            $course_items = $courseService->list_in_lock_for_teacher($uid);
            $course_count = count($course_items);

        } elseif ($user_type == WeixinService::$USER_TYPE_STUDENT) {
            $classService = new ClassService();
            $course_items_result = $classService->list_in_lock_for_student($uid);
            if ($course_items_result['success'] === FALSE) {
                return $this->setError($course_items_result['message']);
            }
            $course_items = $course_items_result['data'];
            $course_count = count($course_items_result);
        }
        if ($course_items) {
            return $this->setSuccess($course_items, '获取所有课程');
        } else {
            return $this->setError('你还没有创建课程');
        }
    }

    /*
     * 检索课程
     */
    public function search ()
    {
        $course_id = intval(I('course_id'));
        if (!$course_id) {
            return $this->setError('传入的参数不能为空');
        }
        $courseService = new CourseService();
        $course = $courseService->search($course_id);
        return $course;
    }

    /*
     * 学生用户 添加课程
     */
    public function add ()
    {
        $course_id = intval(I('course_id'));
        $uid = intval(session('uid'));
        $user_type = intval(session('user_type'));
        if (!$course_id || !$uid || !$user_type) {
            return $this->setError('参数不能为空');
        }
        if ($user_type != WeixinService::$USER_TYPE_STUDENT) {
            return $this->setError('非学生用户不能加入课程');
        }
        $courseService = new CourseService();
        $course_add_result = $courseService->add($uid, $course_id);
        if ($course_add_result['success'] === FALSE) {
            return $this->setError($course_add_result['message']);
        }
        return $this->setSuccess($course_id, '添加成功');
    }

    /*
     * 退出课程
     */
    public function quite ()
    {

    }

    /*
     * 获取当前课程
     */
    public function current ()
    {
        $uid = session('uid');
        $user_type = session('user_type');
        $current_course_id = intval(I('current_course_id'));
        $courseService = new CourseService();
        $course_item_result = $courseService->get_current_course($uid, $user_type, $current_course_id);
        if ($course_item_result['success'] === FALSE) {
            return $this->setError($course_item_result['message']);
        }
        $course_item = $course_item_result['data'];
        $course_count = $course_count = D('Class')->countClassByUid($uid);
        $course_count = $course_count ? $course_count : 0;
        $course_item['course_count'] = $course_count;
        return $this->setSuccess($course_item);
    }


    /*
     * 获取课程信息返回给客户端
     */
    public function get_info ()
    {
        $course_id = intval(I('course_id'));
        if (!$course_id) {
            return $this->setError('参数错误');
        }
        $course_info = D('Course')->getById($course_id);
        if (empty($course_info)) {
            return $this->setError('查不到该课程信息');
        }
        // 添加课程集信息 todo 这里以后要封装
        $question_set = D('Question')->getByCid($course_id);
        $question_set_title = '';
        if ($question_set) {
            $question_set_item = D('QuestionSet')->getById($question_set['set_id']);
            if ($question_set_item) {
                $question_set_title = $question_set_item['title'];
            }
        }
        if ($question_set_title) {
            $course_info['question_set_title'] = $question_set_title;
        }
        return $this->setSuccess($course_info);
    }

    /*
     * list班级的所有学生列表
     */
    public function list_student ()
    {
        $course_id = intval(I('course_id'));
        if (!$course_id) {
            return $this->setError('参数错误');
        }
        $student_list = D('Class')->where(['cid' => $course_id])->select();
        if (empty($student_list)) {
            return $this->setError('该课程还没有学生加入');
        }

        // todo 不知道为什么，这里竟然能公用。后面要拆开
        $signinRecordService = new SigninRecordService();
        $signinRecordService->signin_record_add_info($student_list);

        return $this->setSuccess($student_list, '获取成功');
    }

    /*
     * 获取题库集
     */
    public function list_sets ()
    {
        $sets = D('QuestionSet')->listAll();
        if (empty($sets)) {
            return $this->setError('系统没有题库');
        }
        $questionSetService = new QuestionSetService();
        $questionSetService->items_add_teacher_info($sets);

        return $this->setSuccess($sets, '查询成功');
    }

    /*
     * 设置课程题库
     */
    public function set_question_set ()
    {
        $set_id = intval(I('set_id'));
        $cid = intval(I('course_id'));
        if (!$set_id || !$cid) {
            return $this->setError('参数错误');
        }
        $question_set = D('Question')->getByCid($cid);
        if ($question_set) {
            return $this->setError('该课程已经添加题库了，暂不支持多题库');
        }
        $user_type = $this->user_type;
        if ($user_type != WeixinModel::$USER_TYPE_TEACHER) {
            return $this->setError('此功能仅教师用户可操作');
        }
        $data = ['cid' => $cid, 'set_id' => $set_id, 'gmt_create' => time(), 'gmt_modified' => time(),];
        M('Question')->add($data);
        return $this->setSuccess([], '设置成功');
    }

    /*
     * 设置课程状态
     */
    public function set_status ()
    {
        $status = intval(I('course_status'));
        $cid = intval(I('course_id'));
        if (!$status || !$cid) {
            return $this->setError('参数错误');
        }
        $user_type = $this->user_type;
        if ($user_type != WeixinModel::$USER_TYPE_TEACHER) {
            return $this->setError('此功能仅教师用户可操作');
        }
        $data = ['status' => $status, 'id' => $cid, 'gmt_modified' => time()];
        $Course = D('Course');
        $save_result = $Course->where(['uid' => $this->uid])->save($data);
        if (!$save_result) {
            return $this->setError('更改失败');
        }
        return $this->setSuccess([], '更改成功');
    }
}