<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/3/22
 * Time: 上午11:29
 */


namespace Api\Service;


use Api\Model\SigninRecordModel;

class SigninReplaceService extends BaseService {

    /*
     * todo 1、判断是否为该课程教师 2、根据不同的操作方法进行不同的操作。
     */
    public function replace($teacher_uid, $course_id, $signin_id, $student_uid, $operation) {

        $signin_item = D('Signin')->getById($signin_id);
        if (empty($signin_item)) {
            return ['success' => FALSE, 'message' => '参数错误'];
        }
        if ($signin_item['uid'] != $teacher_uid || $signin_item['cid'] != $course_id) {
            return ['success' => FALSE, 'message' => '只能教师操作'];
        }
        $this->deal_not_by_self($course_id, $signin_id, $student_uid,  array_search($operation, SigninRecordModel::$STATUS_REPLANCE));
        return ['success' => TRUE];
    }


    /**
     * 非本人操作
     * 1、判断是否已经存在记录，如果存在则update 否则 add
     * 2、add 数量加1 、 update 只有在 absence方法时候，才会减1 其他情况不变
     */
    private function deal_not_by_self($course_id, $signin_id, $student_id, $op_type_status) {
        $SIGNIN_RECORD = D('SigninRecord');
        $is_operated = $SIGNIN_RECORD->is_operated($course_id, $signin_id, $student_id);
        if ($is_operated) {
            $SIGNIN_RECORD->update_with_status($course_id, $signin_id, $student_id, $op_type_status);
            if ($op_type_status == SigninRecordModel::$STATUS_ABSENCE) {
                D('Signin')->countDecById($course_id, $signin_id);
            }
        } else {
            $SIGNIN_RECORD->add_with_status($course_id, $signin_id, $student_id, $op_type_status);
            D('Signin')->countIncById($course_id, $signin_id);
        }
    }




}