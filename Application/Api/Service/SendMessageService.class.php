<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/3/26
 * Time: 下午10:49
 */

namespace Api\Service;

use Api\Model\AppointRecordModel;
use Api\Model\TemplateIdModel;

class SendMessageService extends BaseService {




    /*
     * 教师处理完预约信息通知
     */
    public function teacher_finish_appoint($rid) {

        if (!$rid) {
            return FALSE;
        }

        $user_service = new UserService();
        $tempMsgService = new TempMsgService();
        $record_item = D('Api/AppointRecord')->getByRecordId($rid);
        $uid = $record_item['uid'];

        $user_info = $user_service->get_more_info($uid);
        $formid_item = D('Api/Formid')->getOldestByUid($uid);
        $formid = $formid_item['formid'];

        $record_date = $record_item['date'] ? $record_item['date'] : '20180615';
        $record_date = substr($record_date, 0, 4) . '-' . substr($record_date, 4, 2) . '-' . substr($record_date, 6, 2);



        $temp_data = [
            'keyword1'  => ['value'=>AppointRecordModel::$APPOINT_TYPE[$record_item['item_id']]['title']],
            'keyword2'  => ['value'=>$record_date . ' ' . $record_item['time']],
            'keyword3'  => ['value'=>$user_info['name']],
            'keyword4'  => ['value'=>'用户预约处理完成'],
            'keyword5'  => ['value'=>date('Y-m-d H:i:s', time())],
            'keyword6'  => ['value'=>'本次预约处理完成']
        ];

        $page = 'pages/o9j42s2GS3_page10000/o9j42s2GS3_page10000';
//        p([$user_info['openid'], TemplateIdModel::$APPOINT_TEACHER_DEAL, $formid, $temp_data, $page]);die;
        $send_result = $tempMsgService->doSend($user_info['openid'], TemplateIdModel::$APPOINT_TEACHER_DEAL, $formid, $temp_data, $page);
        D('Api/Formid')->deleteByUidAndFormid($uid, $formid);
        if (!$send_result['success']) {
            return ['status' => 0, 'data' => $send_result['message']];
        }

    }
}