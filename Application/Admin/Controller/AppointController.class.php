<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/6/20
 * Time: 下午10:45
 */

namespace Admin\Controller;

use Api\Model\AppointRecordModel;
use Api\Service\AppointRecordService;
use Api\Service\SendMessageService;
use Api\Service\SystemLogService;

class AppointController extends BaseApiController {


    /**
     * 签证预约
     */
    public function visa() {

        $item_id = intval(I('item_id'));
        if (!$item_id) {
            $this->error('页面不存在');
        }
        $title = AppointRecordModel::$APPOINT_TYPE[$item_id]['title'];
        $this->assign('title', $title);

        // 处理where搜索条件
        $date = I('date');
        $where = [];
        if ($date) {
            list($start, $end) = explode('-', str_replace('/', '', str_replace(' ', '', $date)));
            $where['date'] = [['gt', $start], ['lt', $end]];
        }
        $where['item_id'] = $item_id;
        $p = $_GET['p'];
        $records = D('Api/AppointRecord')->listByPageWhere($where, $p, 10, 'id desc');
        p($records); die;


        $appoint_service = new AppointRecordService();
        $appoint_service->convert_record_format($records);
        $this->assign('list', $records)->display();
    }


    /**
     * 标记该预约处理成功
     *
     * // todo 测试一遍，然后所有的D方法都有加上模块名字
     */

    public function deal() {
        if (!IS_POST) {
            return $this->error('你访问的界面不存在');
        }

        $rid = intval(I('id'));
        $flag = I('field');
        if (!$rid || $flag != 'appoint_deal') {
            return $this->error('api参数错误');
        }

        $admin_user = session('user');

        $system_log_service = new SystemLogService();
        $system_log_service->log_appoint_record($admin_user['id'], $rid, '教师处成学生预约理完');
        $send_message_service = new SendMessageService();
        $send_message_service->teacher_finish_appoint($rid);

        return $this->success('操作成功');

    }
}