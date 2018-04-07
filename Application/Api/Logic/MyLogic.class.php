<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/2/14
 * Time: 下午8:25
 */

namespace Api\Logic;

use Api\Model\WeixinModel;
use Api\Service\InvitationService;
use Api\Service\StudentService;
use Api\Service\TeacherService;
use Api\Service\WeixinService;

class MyLogic extends UserBaseLogic {

    public function __construct ()
    {
        parent::__construct();
    }

    /*
     * info，给my_index页面提供接口,
     * 返回数据， head_url， is_band, user_type
     */
    public function index ()
    {
        $openid = session('openid');
        $weixinService = new WeixinService();
        $weixin_user_result = $weixinService->getByOpenid($openid);
        if ($weixin_user_result['success'] === FALSE) {
            return $this->setError($weixin_user_result['message']);
        }
        $weixin_user = $weixin_user_result['data'];
        $data = ['avatar' => $weixin_user['avatar'], 'is_bind' => $weixin_user['type'] > 0 ? TRUE : FALSE,];
        return $this->setSuccess($data, '获取数据成功');
    }

    /*
     * 获取用户信息
     */
    public function info ()
    {
        $openid = session('openid');
        $weixinService = new WeixinService();
        $user_info_result = $weixinService->getByOpenid($openid);
        if ($user_info_result['success'] === FALSE) {
            return $user_info_result;
        }
        $user_type = $user_info_result['data']['type'];
        $uid = $user_info_result['data']['uid'];
        $user_info = [];
        if ($user_type == WeixinModel::$USER_TYPE_TEACHER) {
            $teacherService = new TeacherService();
            $teacher_info_result = $teacherService->getTeacherInfo($uid);
            if ($teacher_info_result['success'] === FALSE) {
                return $teacher_info_result;
            }
            $user_info = $teacher_info_result['data'];
        }
        if ($user_type == WeixinModel::$USER_TYPE_STUDENT) {
            $studentService = new StudentService();
            $student_info_result = $studentService->getStudentInfo($uid);
            if ($student_info_result['success'] === FALSE) {
                return $student_info_result;
            }
            $user_info = $student_info_result['data'];
        }
        if (!count($user_info)) {
            return ['success' => FALSE, 'message' => '获取用户信息失败'];
        }
        $user_info['user_type'] = WeixinModel::$USER_TYPE[$user_type];
        return ['success' => TRUE, 'data' => $user_info];
    }

    /*
     * 用户获取邀请码
     */
    public function invite ()
    {
        $user_type = $this->user_type;
        if ($user_type == WeixinModel::$USER_TYPE_STUDENT) {
            return $this->setSuccess(['invitor' => 'student']);
        }
        $uid = $this->uid;
        $invitationService = new InvitationService();
        $invitation_code_result = $invitationService->get_invitation_code($uid);
        if ($invitation_code_result['success'] === FALSE) {
            return $this->setError($invitation_code_result);
        }
        return $this->setSuccess(['invitor' => 'teacher', 'invitation_code' => $invitation_code_result['data']], '获取成功');
    }
}