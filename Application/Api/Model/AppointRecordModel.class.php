<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/6/1
 * Time: 下午11:57
 */

namespace Api\Model;

class AppointRecordModel extends BaseModel {

    public static $APPOINT_TYPE = [

        // 签证预约
        1234 => [
            'title' => '签证预约',
            'time' => '1',
            'time_list' => '8:30-9:30',
            'date' => '除周二外',
            'statement' => '说明',
            'description' => '<p>签证预约时间段，每个时间段最多可预约5人</p><p>8:30-9:30</p><p>9:30-10:30</p><p>10:30-11:30</p><p>13:30-14:30</p><p>14:30-15:30</p><p>15:30-16:30</p>',
        ],
        1235 => [
            'title' => '缴费预约',
            'time' => '1',
            'time_list' => '9:00-10:00',
            'date' => '工作时间',
            'statement' => '说明',
            'description' => '<p>签证预约</p><p>签证预约具体是干嘛的呀</p>',
        ],
        1236 => [
            'title' => '忘了预约',
            'time' => '1',
            'time_list' => '9:00-10:00',
            'date' => '工作时间',
            'statement' => '说明',
            'description' => '<p>签证预约</p><p>签证预约具体是干嘛的呀</p>',
        ],
        1237 => [
            'title' => 'refunt预约',
            'time' => '1',
            'time_list' => '9:00-10:00',
            'date' => '工作时间',
            'statement' => '说明',
            'description' => '<p>签证预约</p><p>签证预约具体是干嘛的呀</p>',
        ],

    ];

    public static $STATUS = [
        1 => '预约单子填写中， 并未完成',
        2 => '预约单子填写完成， 有效预约单子',
        3 => '预约单学生取消了',
        4 => '预约单子老师取消了',
        5 => '老师完成预约单'
    ];

    public static $STUDENT_UNFINISHED = 1;
    public static $STUDENT_FINISHED = 2;
    public static $STUDENT_CANCEL = 3;
    public static $TEACHER_FINISHED = 5;



    /*
     * 罗列所有的正在使用的课程
     */

    public function add ($uid, $item_id, $date, $time)
    {
        if (empty($uid) || empty($date) || empty($time)) {
            return FALSE;
        }

        $data = [

            'uid' => $uid,
            'item_id' => $item_id,
            'date' => $date,
            'time' => $time,
            'status' => 1,
            'gmt_create' => time(),
            'gmt_modified' => time(),
        ];

        return M('AppointRecord')->add($data);
    }

    /*
     * 查询预约记录
     */

    public function getByRecordId($rid) {

        if (!$rid) {
            return FALSE;
        }

        $record_item = $this->find($rid);

        return $record_item;
    }

    public function addRemark($rid, $remark) {

        $record = $this->getByRecordId($rid);

        if (!$record || !$rid) {
            return FALSE;
        }

        M('Appoint_record')->where(['id' => $rid])->save(['remark' => $remark]);
        return TRUE;
    }

    /*
     * @param $rid record_id
     * @param $status
     * 设置订单状态
     */
    public function setRecordStatus($rid, $status) {

        if (!$rid || !$status) {
            return FALSE;
        }

        M('Appoint_record')->where(['id' => $rid])->save(['status' => $status]);
    }

    public function listStudentFinishedByUid($uid) {

        if (!$uid) {
            return FALSE;
        }

        $finished_list = M('Appoint_record')->where(['uid'=>$uid, 'status' => self::$STUDENT_FINISHED])->select();

        return $finished_list;
    }

    public function deleteByRid($rid) {

        if (!$rid) {
            return FALSE;
        }

        M('Appoint_record')->where(['id'=>$rid])->delete();

        return TRUE;
    }


}