<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/6/24
 * Time: 下午9:50
 */

namespace Api\Service;

use Api\Model\AppointRecordModel;

class SystemLogService extends BaseService {

    public function log_appoint_record($uid, $record_id, $remark = '') {

        if (!$uid || !$record_id) {
            return FALSE;
        }

        $APPOINT_RECORD = D('AppointRecord');

        $before_record = $APPOINT_RECORD->find($record_id);


        $data = [
            'id' => $record_id,
            'status' => AppointRecordModel::$TEACHER_FINISHED,
            'gmt_modified' => time(),
        ];
        $APPOINT_RECORD->save($data);

        $after_record = $APPOINT_RECORD->find($record_id);

        $operate = json_encode(['before' => $before_record, 'after' => $after_record]);


        D('Api/SystemLog')->addLog($uid, $operate, $remark);

    }

}