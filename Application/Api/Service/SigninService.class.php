<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/2/18
 * Time: 下午10:47
 */

namespace Api\Service;

class SigninService extends BaseService {

    /*
     * 给课程分类 分为 正在进行已经过时的，还没有开始的
     */
    public function classfy_signin_items ($signin_items)
    {
        if (empty($signin_items)) {
            return ['success' => FALSE, 'message' => '传入的参数为空'];
        }
        $tid = $signin_items[0]['uid'];
        $teacher = D('Teacher')->getById($tid);
        $before_signin_items = [];  // 已经过期的点名
        $last_signin_items = [];   //点名过程中
        $after_signin_items = [];  // 还没有开始
        $ts = time();
        foreach ($signin_items as $signin_item) {
            $signin_item['teacher_head_img'] = $teacher['head_img'];
            $signin_item['start_time_format'] = date('Y/m/d H:i', $signin_item['start_time']);
            $signin_item['end_time_format'] = date('H:i', $signin_item['end_time']);
            if ($ts < intval($signin_item['start_time'])) {
                $before_signin_items[] = $signin_item;
            } elseif ($ts >= intval($signin_item['start_time']) && $ts <= intval($signin_item['end_time'])) {
                $last_signin_items[] = $signin_item;
            } elseif ($ts > intval($signin_item['end_time'])) {
                $after_signin_items[] = $signin_item;
            }
        }

        return ['success' => TRUE, 'data' => ['before_start' => $before_signin_items, 'is_doing' => $last_signin_items, 'is_done' => $after_signin_items,]];
    }


    /*
     * 在线签到
     */
    public function check_signin_online ($uid, $cid, $sid)
    {
        if (!$uid || !$cid || !$sid) {
            return ['success' => FALSE, 'message' => '传入的参数为空'];
        }
        // 学生身份判断 ，是不是本班学生
        $classService = new ClassService();
        $is_join_course = $classService->is_join_course($uid, $cid);
        if ($is_join_course === FALSE) {
            return ['success' => FALSE, 'message' => '你还没有加入该课程'];
        }
        // 判断时间
        $ts = time();
        $signin_item = M('Signin')->find($sid);
        //        $signin_item = M('Signin')->cache('signin_' . $sid, 60)->find($sid);
        if (!$signin_item) {
            return ['success' => FALSE, 'message' => '本次签到不存在'];
        }
        $signin_start_time = intval($signin_item['start_time']);
        $signin_end_time = intval($signin_item['end_time']);
        if ($ts < $signin_start_time || $ts > $signin_end_time) {
            return ['success' => FALSE, 'message' => '不在规定时间内签到'];
        }
        // todo 判断地理位置逻辑

        // 判断是否重复签到
        $signinRecordService = new SigninRecordService();
        $is_signined = $signinRecordService->is_signined($uid, $sid);
        if (is_array($is_signined)) {
            return $is_signined;
        }
        if ($is_signined === TRUE) {
            return ['success' => FALSE, 'message' => '你已经签到过了'];
        }
        return ['success' => TRUE, 'message' => '验证成功'];
    }






}