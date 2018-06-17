<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/6/9
 * Time: 下午4:10
 */


namespace Api\Service;


class AppointRecordService extends BaseService {

    /**
     * @param $uid
     * @param $item_id 项目id
     * 判断1个学生1天只能预约1个项目次数
     */
    public function is_repeated($uid, $item_id) {

        if (!$uid || !$item_id) {
            return FALSE;
        }
        $today_begin_ts = strtotime(date('Y-m-d', time()));
        $today_end_ts = strtotime(date('Y-m-d', time())) + 24 * 3600 - 1;

        $map = [
            'uid' => $uid,
            'item_id' => $item_id,
            'status' => 2,
            'gmt_create' => [
                ['gt', $today_begin_ts],
                ['lt', $today_end_ts],
            ]
        ];

        if (M('Appoint_record')->where($map)->find()) {
            return TRUE;
        }
        return FALSE;
    }
}