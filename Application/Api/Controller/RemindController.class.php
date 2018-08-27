<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/6/16
 * Time: 下午12:05
 */

namespace Api\Controller;
use Api\Model\AppointRecordModel;
use Api\Model\TemplateIdModel;
use Api\Service\TempMsgService;
use Api\Service\UserService;
use Think\Template;

/**
 * 用于各种提醒、微信模版消息提醒、邮件提醒、
 * 定时提醒，操作提醒
 */
class RemindController extends BaseController {

    //系统首页
    public function index ()
    {
        echo "提醒模块";
    }

    // todo 定时提醒出发接口


    /**
     * 清除过期了的formid
     * 过期的定义是 当前时间 - formid生成时间 > 7
     *
     */
    public function clear_expire_formid() {

        $before_time = time() - 7 * 24 * 3600;

        D('Formid')->clearBeforeGmt_create($before_time);

    }


    /*
     * 先在数据库appoint_record数据库根据
     * (时间当天提醒)查出需要提醒的记录。
     */
    public function send() {

        $today_date = date('Ymd', time());
        $remind_list = D('AppointRecord')->listByDate($today_date);
        if (!$remind_list) {
            return FALSE;
        }

        $user_service = new UserService();
        $tempMsgService = new TempMsgService();
        foreach ($remind_list as $_list) {

            $uid = $_list['uid'];
            $title = AppointRecordModel::$APPOINT_TYPE[$_list['item_id']]['title'];
            $time = $_list['date'] . ' ' . $_list['time'];
            $user_info = $user_service->get_more_info($uid);

            $temp_data = [
                'keyword1'  => ['value'=>$time],
                'keyword2'  => ['value'=>$title . '提醒'],
                'keyword3'  => ['value'=>$user_info['name']],
                'keyword4'  => ['value'=>$_list['remark']?$_list['remark']:'用户预约记录'],
                'keyword5'  => ['value'=>'请按时办理业务'],
            ];
            $page = 'pages/o9j42s2GS3_page10000/o9j42s2GS3_page10000';
            $formid_item = D('Formid')->getOldestByUid($uid);
            $formid = $formid_item['formid'];

            $send_result = $tempMsgService->doSend($user_info['openid'], TemplateIdModel::$APPOINT_REMINDER_TEMPLATE_ID, $formid, $temp_data, $page);
            D('Formid')->deleteByUidAndFormid($uid, $formid);
            if ($send_result['success']) {
                ;
            } else {
                p([$user_info['openid'], TemplateIdModel::$APPOINT_REMINDER_TEMPLATE_ID, $formid, $temp_data, $page, $uid, $formid]);
                echo json_encode($send_result);
            }
            sleep(1);
        }
    }

}