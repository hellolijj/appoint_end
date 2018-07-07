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

        /**
         * 签证预约
         * visa appointment往下再分两个子预约菜单， new student-和old student
        预约时段和规则一样如下：上午，08：30-09：30，9：30-10：30， 10：30-11：30， 下午：13：30-14：30，14：30-15：30， 15：30-16：30 每个时段可预约5人
         */
        1234 => [
            'title' => '签证预约',
            'time' => '1',
            'time_list' => '08:30-09:30',
            'date' => '除周二外',
            'statement' => '说明',
            'description' => '<p>签证预约时间段</p><p>上午，08:30-09:30，09:30-10:30， 10:30-11:30， </p><p>下午：13:30-14:30，14:30-15:30，15:30-16:30</p><p>每个时间段最多可预约5人</p>',
            // 每个时间段最多可预约5人
            'appoint' => [
                'time_list' => [
                    '08:30-09:30',
                    '09:30-10:30',
                    '10:30-11:30',
                    '13:30-14:30',
                    '14:30-15:30',
                    '15:30-16:30',
                ],
                'max_count' => 5,
            ]
        ],


        /** 退费预约
         *  退费预约功能，提前3天预约，如6月1号只能预约到6月4日的时间。
         *  时间段为9:00～11:00，2:00～4:00，每个时间段有10个预约名额
         */
        1235 => [
            'title' => '退费预约',
            'time' => '2',
            'time_list' => '9:00-11:00',
            'date' => '提前3天预约（周二除外）',
            'statement' => '退费预约',
            'description' => '<p>提前3天预约，如6月1号只能预约到6月4日的时间</p><p>时间段为09:00-11:00，14:00-16:00，每个时间段有10个预约名额</p>',
            'appoint' => [
                'time_list' => [
                    '09:00-11:00',
                    '14:00-16:00',
                ],
                'max_count' => 10,
            ]
        ],

        /**
         * 国际生接待日预约
         * 国际生接待日international reception day,
         * 开放时间：每周五下午。预约时段： 下午：13:30-14：30，14：30-15：30，
         * 15：30-16：30，每个时段可预约5人
         */
        1236 => [
            'title' => '接待日预约',
            'time' => '1',
            'time_list' => '13:30-14:30',
            'date' => '每周五下午',
            'statement' => '说明',
            'description' => '<p>接待日预约</p><p>开放时间：每周五下午。预约时段： 下午：13:30-14:30，14:30-15:30，15:30-16:30，每个时段可预约5人</p>',
            'appoint' => [
                'time_list' => [
                    '13:30-14:30',
                    '14:30-15:30',
                    '15:30-16:30'
                ],
                'max_count' => 5,
            ]
        ],

        /**
         *  宿舍调换预约功能，
         *  只在6月份和12月份开放（周二不办理业务），预约方法与签证业务一样，提前2天预约，
         *  如6月1号只能预约到6月3日的时间。时间段为9:00～11:00，2:00～4:00，每个时间段有10个预约名额
         */
        1237 => [
            'title' => '宿舍调换预约',
            'time' => '2',
            'time_list' => '9:00-11:00',
            'date' => '只有6月份和12月份开放',
            'statement' => '说明',
            'description' => '<p>宿舍调换预约功能</p><p>只在6月份和12月份开放（周二不办理业务），预约方法与签证业务一样，提前2天预约</p><p>如6月1号只能预约到6月3日的时间。时间段为09:00-11:00，14:00-16:00，每个时间段有10个预约名额</p>',
            'appoint' => [
                'time_list' => [
                    '09:00-11:00',
                    '14:00-16:00',
                ],
                'max_count' => 10,
            ]
        ],

    ];

    public static $APPOINT_TYPE_VIA = 1234; //签证预约
    public static $APPOINT_TYPE_REFUND = 1235; //退费预约
    public static $APPOINT_TYPE_RECEPTION = 1236; //接待日预约
    public static $APPOINT_TYPE_CHANGE_ROOM = 1237; //宿舍调换预约


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

    public static $STATUS_VALID = [2, 5];



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

        M('Appoint_record')->where(['id' => $rid])->save(['status' => $status, 'gmt_modified' => time()]);
    }

    public function listStudentFinishedByUid($uid) {

        if (!$uid) {
            return FALSE;
        }

        $finished_list = M('Appoint_record')->where(['uid'=>$uid, 'status' => self::$STUDENT_FINISHED])->order('date desc, time asc, id desc')->select();

        return $finished_list;
    }


    public function deleteByRid($rid) {

        if (!$rid) {
            return FALSE;
        }

        M('Appoint_record')->where(['id'=>$rid])->delete();

        return TRUE;
    }

    /**
     * @param $date 日期 如 20180203
     *
     */
    public function listCountByDate($type_id, $date) {

        if (!$date || !$type_id) {
            return FALSE;
        }

        $appoint_record_list = M('Appoint_record')->field('time, count(*) as count')->where(['item_id'=>$type_id, 'date'=>$date, 'status'=>self::$STUDENT_FINISHED])->group('time')->select();
        $result = [];
        foreach ($appoint_record_list as $_list) {
            $result[$_list['time']] = intval($_list['count']);
        }

        return $result;
    }

    public function listByDate($date) {

        if (!$date) {
            return FALSE;
        }

        $list = M('Appoint_record')->where(['date'=>$date, 'status'=>self::$STUDENT_FINISHED])->select();

        return $list;

    }

    /**
     * @param $uid
     * @return bool
     *  教师处理完的单子
     */
    public function listTeacherFinishedByUid($uid) {

        if (!$uid) {
            return FALSE;
        }


        $finished_list = M('Appoint_record')->where(['uid'=>$uid, 'status' => self::$TEACHER_FINISHED])->order('date desc, time asc, id desc')->select();

        return $finished_list;
    }


    /**
     * @param $uid
     * @return bool
     * 有效的订单
     */
    public function listValidByUid($uid) {

        if (!$uid) {
            return FALSE;
        }

        /*$where['uid'] = $uid;
        $valid_status = [self::$STUDENT_FINISHED, self::$TEACHER_FINISHED];
        $where['status'] = ['in', implode(',', $valid_status)];

        $finished_list = M('Appoint_record')->where($where)->order('date desc, time asc, id desc')->select();*/

        $student_list = $this->listStudentFinishedByUid($uid);
        $teacher_list = $this->listTeacherFinishedByUid($uid);
        $finished_list = [$student_list, $teacher_list];

        return $finished_list;
    }


    public function listByItemid($item_id) {

        if (!$item_id) {
            return FALSE;
        }

        $where['item_id'] = $item_id;
        $where['status'] = ['in', implode(',', self::$STATUS_VALID)];
        $record_lists = M('Appoint_record')->where($where)->order('status asc, date desc, time asc, id desc')->select();

        return $record_lists;

    }





}