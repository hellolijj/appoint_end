<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/6/20
 * Time: 下午10:45
 */

namespace Admin\Controller;

use Admin\Service\DocsService;
use Api\Model\AppointRecordModel;
use Api\Service\AppointRecordService;
use Api\Service\SendMessageService;
use Api\Service\SystemLogService;
use Think\Page;

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
        $flag = I('flag');
        $where = [];
        if ($date) {
            list($start, $end) = explode('-', str_replace('/', '', str_replace(' ', '', $date)));
            p([$start, $end]);
            $where['date'] = [['gt', $start], ['lt', $end]];
        }

        // 查询今日预约
        if ($flag == 'today') {
            $where['date'] = date('Ymd', time());
        }
        $where['item_id'] = $item_id;
        $where['status'] = ['gt', 1];
        $p = $_GET['p'];

        $records = D('Api/AppointRecord')->listByPageWhere($where, $p, 10, 'id desc');

        // 如果是材料预约的话，就把材料预约的内容也加载进来
        if ($item_id == AppointRecordModel::$APPOINT_TYPE_DOCUMENT) {
            $docs_service = new DocsService();
            $docs_service->add_docs($records);
        }

        $appoint_service = new AppointRecordService();
        $appoint_service->convert_record_format($records);

        $this->assign('list',$records);// 赋值数据集
        $count      = D('Api/AppointRecord')->countByWhere($where);// 查询满足要求的总记录数
        $Page       = new Page($count, 10);
        $page       = $Page->show();// 分页显示输出
        $this->assign('page_content',$page);// 赋值分页输出

        $this->display();

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

    /**
     * 导入数据
     * 一次不能导出超过100条数据
     */

    public function download() {
        $item_id = intval(I('item_id'));
        if (!$item_id) {
            $this->error('页面不存在');

        }

        // 处理where搜索条件
        $date = I('date');
        $flag = I('flag');
        $where = [];
        if ($date) {
            list($start, $end) = explode('-', str_replace('/', '', str_replace(' ', '', $date)));
            p([$start, $end]);
            $where['date'] = [['gt', $start], ['lt', $end]];
        }

        // 查询今日预约
        if ($flag == 'today') {
            $where['date'] = date('Ymd', time());
        }
        $where['item_id'] = $item_id;
        $count      = D('Api/AppointRecord')->countByWhere($where);// 查询满足要求的总记录数

        // 一次不能导出超过15条数据
        if ($count > 15) {
            $this->error('一次不能导出超过15条数据');
        }
        $records = D('Api/AppointRecord')->where($where)->order('id desc')->select();
        $appoint_service = new AppointRecordService();
        $appoint_service->convert_record_format($records);

        p($records);
    }

}