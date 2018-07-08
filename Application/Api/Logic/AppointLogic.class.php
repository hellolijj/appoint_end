<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/4/30
 * Time: 上午10:29
 */

namespace Api\Logic;

use Api\Model\AppointRecordModel;
use Api\Model\TemplateIdModel;
use Api\Service\AppointRecordService;
use Api\Service\TempMsgService;
use Api\Service\UserService;

class AppointLogic extends UserBaseLogic {



    public function details() {
        $item = intval(I('data_id'));
        // 定义 不同的预约类型
        $appoint_info = AppointRecordModel::$APPOINT_TYPE;

        $s = '{"status":0,"data":[{"form_data":{"description":"<p>\u5546\u5bb6\u670d\u52a1<\/p><p>\u63d0\u4f9b\u514d\u8d39WiFi<\/p><p>\u63d0\u4f9b10\u4e2a\u514d\u8d39\u505c\u8f66\u4f4d<\/p><p>\u6e29\u99a8\u63d0\u793a<\/p><p>\u5982\u9700\u56e2\u8d2d\u5238\u53d1\u7968\uff0c\u8bf7\u60a8\u5728\u6d88\u8d39\u65f6\u5411\u5546\u6237\u54a8\u8be2<\/p><p>\u4e3a\u4e86\u4fdd\u969c\u60a8\u7684\u6743\u76ca\uff0c\u5efa\u8bae\u4f7f\u7528\u7f8e\u56e2\u3001\u70b9\u8bc4\u7f51\u7ebf\u4e0a\u652f\u4ed8\u3002\u82e5\u4f7f\u7528\u5176\u4ed6\u652f\u4ed8\u65b9\u5f0f\u5bfc\u81f4\u7ea0\u7eb7\uff0c\u7f8e\u56e2\u3001\u70b9\u8bc4\u7f51\u4e0d\u627f\u62c5\u4efb\u4f55\u8d23\u4efb\uff0c\u611f\u8c22\u60a8\u7684\u7406\u89e3\u548c\u652f\u6301\uff01<\/p><p><br \/><\/p>","img_urls":["http:\/\/img.weiye.me\/zcimgdir\/album\/file_596db4604e889.jpg","http:\/\/img.weiye.me\/zcimgdir\/album\/file_596db248c49ac.jpg"],"type":0,"delivery_id":"0","id":"3977372","app_id":"Z22PP52DLh","title":"\u5927\u5305\u5468\u4e00\u5230\u5468\u65e5\u4e0b\u5348\u4e03\u5c0f\u65f6\u6b22\u5531_copy_copy","cover":"http:\/\/img.weiye.me\/zcimgdir\/album\/file_596db52164dfd.jpg","price":"188.00","sale_price":"0.00","category":["\u5927\u5305"],"sales":"1","is_recommend":"0","stock":"40","weight":"9","goods_type":"1","max_can_use_integral":"0","integral":"-1","mass":"0.000","volume":"0.000","express_rule_id":"0","is_seckill":"2","seckill_status":"2","virtual_price":"0.00","add_time":"1525054168","update_time":"1525054168","viewed_count":"8","sell_time":"","status":"0","category_id":["2132636"],"model_items":[{"model":"30,31,-1","price":"188","stock":"40"}],"app_name":"\u9884\u7ea6\u5e94\u7528","express_fee":"\u5305\u90ae","server_time":1527492645,"model":[{"id":"0","name":"\u9884\u7ea6\u65f6\u957f","subModelName":["7"],"subModelId":["30"]},{"id":"1","name":"\u9884\u7ea6\u65f6\u6bb5","subModelName":["12:00-24:00"],"subModelId":["31"]},{"id":"2","name":"\u9884\u7ea6\u65e5\u671f","subModelName":["\u5168\u5e74"],"subModelId":["-1"]}],"appointment_info":{"goods_id":"3977372","unit":"\u5c0f\u65f6","unit_type":"2","revert_stock_seconds":"0","holiday_type":"1","custom_week_holiday":"255","custom_holiday":null,"is_deleted":"0","display_comment":"1","need_user_address":"1","appointment_desc":"","appointment_phone":"","force_appointment_start_date":"0000-00-00 00:00:00","force_appointment_end_date":"9999-12-31 23:59:59"},"recommend_type":0,"recommend_info":[],"is_group_buy_goods":0,"buyer_list":[],"recommend_goods_info":[],"app_cover":"http:\/\/cdn.jisuapp.cn\/zhichi_frontend\/static\/webapp\/images\/share_feng.jpg"},"weight":"9","unit":"\u5c0f\u65f6","holiday_type":"1"}],"is_more":0,"current_page":-1,"count":"1","total_page":1}';
        $return = json_decode($s, TRUE);
        $return['data'][0]['form_data']['title'] = $appoint_info[$item]['title'] ? $appoint_info[$item]['title'] : '';
        $return['data'][0]['form_data']['model'][0]['subModelName'][0] = $appoint_info[$item]['title'] ? $appoint_info[$item]['time'] : '';
        $return['data'][0]['form_data']['model'][1]['subModelName'][0] = $appoint_info[$item]['time_list'] ? $appoint_info[$item]['time_list'] : '';
        $return['data'][0]['form_data']['model'][2]['subModelName'][0] = $appoint_info[$item]['date'] ? $appoint_info[$item]['date'] : '';
        $return['data'][0]['form_data']['description'] = $appoint_info[$item]['description'] ? $appoint_info[$item]['description'] : '';

        return $return;
    }

    public function access() {
        return ['status' => 0, 'data' => [], 'is_more' => 0, 'total_page' => 0, 'num' => [0,0,0,0,0]];
    }

    public function time_list() {
        $appoint_date = trim(I('day'));  // 预约日期
        $item = intval(I('data_id'));   //预约项目

        if (empty($appoint_date) || $item == 0) {
            return ['status' => 1, 'data' => '请选择时间段'];
        }

        // 对日期逻辑进行判断
        $choose_ts = strtotime($appoint_date);  // 选择20180403 实际上是选择 20180403 23：59：59：
        $now_ts = time();

        if (($choose_ts - $now_ts) / (24 * 3600) > 7) {
            return ['status' => 1, 'data' => '只能选择7天内的日期'];
        }
        if (date('w', $choose_ts) === 0) {
            return ['status' => 1, 'data' => '周日不上班'];
        }
        if (date('w', $choose_ts) == 2) {
            return ['status' => 1, 'data' => '周二不能预约'];
        }
        if (date('w', $choose_ts) == 6) {
            return ['status' => 1, 'data' => '周六不上班'];
        }

        if ($item == AppointRecordModel::$APPOINT_TYPE_VIA && ($choose_ts - $now_ts) / (24 * 3600) < 1) {
            return ['status' => 1, 'data' => '签证预约，不能预约今天的日期'];
        }
        if ($item == AppointRecordModel::$APPOINT_TYPE_REFUND && ($choose_ts - $now_ts) / (24 * 3600) < 2) {
            return ['status' => 1, 'data' => '退费预约，只能预约3天后的日期'];
        }
        if ($item == AppointRecordModel::$APPOINT_TYPE_RECEPTION && date('w', $choose_ts) != 5) {
            return ['status' => 1, 'data' => '接待日预约，只能预约周五的日期'];
        }
        if ($item == AppointRecordModel::$APPOINT_TYPE_CHANGE_ROOM && in_array(intval(date('m', $now_ts)), [1, 2, 3, 4, 5, 7, 8, 9, 10, 11])) {
            return ['status' => 1, 'data' => '宿舍调换预约，只能在6月或者12月'];
        }

        // 根据日期和item_id 来返回可以选择的预约项目
        $data = ' { "status": 0, "data": { "appointment_info": [{ "interval": "08:00-09:00", "expired": 0, "buyed": 0, "can_buy": 5 }, { "interval": "09:00-10:00", "expired": 0, "buyed": 0, "can_buy": 1 }, { "interval": "10:00-11:00", "expired": 0, "buyed": 0, "can_buy": 1 }, { "interval": "11:00-12:00", "expired": 0, "buyed": 0, "can_buy": 1 }, { "interval": "14:00-15:00", "expired": 0, "buyed": 0, "can_buy": 1 }, { "interval": "15:00-16:00", "expired": 0, "buyed": 0, "can_buy": 1 }, { "interval": "16:00-17:00", "expired": 0, "buyed": 0, "can_buy": 1 }], "can_select_interval": ["09:00-10:00","10:00-11:00","11:00-12:00"], "can_select_time_long": ["1"], "selected_interval": "12:00-24:00", "selected_time_long": "1", "selected_day": "20180430", "appointment_stock": "40", "appointment_price": "188", "unit_type": 2 }, "unit_type": "2" } ';
        $data = json_decode($data, TRUE);

        $appoint_type = AppointRecordModel::$APPOINT_TYPE;
        $valid_list = $appoint_type[$item]['appoint']['time_list'];

        if (!$valid_list) {
            return ['status' => 1, 'data' => '非法的项目参数'];
        }

        // 处理剩余人数
        $max_appoint_count  = $appoint_type[$item]['appoint']['max_count'];
        $use_appoint_count_array = D('AppointRecord')->listCountByDate($item, $appoint_date);
        unset($data['data']['appointment_info']);
        foreach ($valid_list as $_list) {
            $use_appoint_count = 0;
            if ($use_appoint_count_array[$_list]) {
                $use_appoint_count = $use_appoint_count_array[$_list];
            }

            $can_appoint_count = $max_appoint_count - $use_appoint_count;
            if ($can_appoint_count > 0) {
                $data['data']['appointment_info'][] = [
                    'interval' => $_list,
                    'expired' => 0,
                    'buyed' => 1,
                    'can_buy' => $can_appoint_count,
                ];
            } else {
                $data['data']['appointment_info'][] = [
                    'interval' => $_list,
                    'expired' => 1,
                    'buyed' => 1,
                    'can_buy' => 0,
                ];
            }
        }
        // 退费预约是预约2个小时
        if ($item == AppointRecordModel::$APPOINT_TYPE_REFUND || $item == AppointRecordModel::$APPOINT_TYPE_CHANGE_ROOM) {
            $data['data']['can_select_time_long'] = ['2'];
        }
        return $data;
    }

    //生成一个预约订单
    public function card() {
        $item_id = intval(I('goods_id'));
        $appoint_interval = I('appointment_interval');
        $appoint_date = I('appointment_day');
        $uid = session('uid');

        if (!$item_id || empty($appoint_interval) || !$appoint_date || !$uid) {
            return ['status' => 1, 'data' => '时间不能为为空'];
        }

        // 对学生预约数量的判断: 一个学生每个项目每天只能预约一次。要想重新预约得先把之前预约的删掉
        $appoint_record_service = new AppointRecordService();
        if (TRUE === $appoint_record_service->is_repeated($uid, $item_id)) {
            return ['status' => 1, 'data' => '每个项目每天只能预约一次'];
        }

        $appoint_record_id = D('AppointRecord')->add($uid, $item_id, $appoint_date, $appoint_interval);

        if (!$appoint_record_id) {
            return ['status' => 1, 'data' => '预约错误'];
        }

        session('appoint_record_id', $appoint_record_id);

        return ['status' => 0, 'data' => session('appoint_record_id'), ];
    }


    public function calculate() {
        $s = '{"status":0,"data":{"price":188,"original_price":188,"group_buy_discount_price":0,"express_fee":0,"use_balance":0,"balance":0,"discount_cut_price":0,"can_use_benefit":{"status":0,"vip_benefit":[],"coupon_benefit":[],"integral_benefit":[],"max_can_use_integral":[],"user_integral":[],"data":[],"selected_index":0},"selected_benefit_info":[],"goods_info":[{"description":"","img_urls":["",""],"type":0,"delivery_id":"0","id":"3977372","app_id":"Z22PP52DLh","title":"签证预约","cover":"http:\/\/img.weiye.me\/zcimgdir\/album\/file_596db52164dfd.jpg","price":"188","sale_price":"0.00","category":["大包"],"sales":"0","is_recommend":"0","stock":"51","weight":"9","goods_type":"1","max_can_use_integral":"0","integral":"-1","mass":"0.000","volume":"0.000","express_rule_id":"0","is_seckill":"2","seckill_status":"2","virtual_price":"0.00","add_time":"1525054168","update_time":"1525054168","category_id":["2132636"],"model_items":[{"model":"30,31,-1","price":"188","stock":"40"}],"app_name":"预约应用","express_fee":"包邮","server_time":1525068036,"model":[{"id":"0","name":"预约时长","subModelName":["1"],"subModelId":["30"]},{"id":"1","name":"预约时段","subModelName":["12:00-24:00"],"subModelId":["31"]},{"id":"2","name":"预约日期","subModelName":["全年"],"subModelId":["-1"]}],"appointment_info":{"appointment_unit_type":"2","appointment_day":"20180630","appointment_interval":"13:00-20:00","appointment_time_long":7,"need_user_address":"1"},"num":"1","original_price":"188"}],"deliver_fee":0,"min_deliver_fee":0,"is_take_deliver":0,"address":[]}}';
        $appoint_record_id = session('appoint_record_id');
        $appoint_record_item = D('AppointRecord')->getByRecordId($appoint_record_id);
        $map = [
            'item_id' => $appoint_record_item['item_id'],
            'date' => $appoint_record_item['date'],
            'time' => $appoint_record_item['time'],
            'status' => AppointRecordModel::$STUDENT_FINISHED,
        ];
        $max_appoint_count = AppointRecordModel::$APPOINT_TYPE[$map['item_id']]['appoint']['max_count'];
        $use_appoint_count = M('Appoint_record')->where($map)->count();
        $can_appoint_count = $max_appoint_count - $use_appoint_count;
        if ($can_appoint_count <= 0) {
            $can_appoint_count = 0;
        }

        $s = str_replace('188', $can_appoint_count, $s);
        return json_decode($s, TRUE);
    }

    public function cart_list() {
        $s = '{"status":0,"data":[{"id":"1031510","buyer_id":"e33581104d3fe47e2b867d38b93e8a8d","goods_id":"3977372","model_id":"0","app_id":"Z22PP52DLh","num":"1","add_time":"1525068035","parent_shop_app_id":"0","related_shop_app_id":"0","is_integral":"-1","is_seckill":"2","is_group_buy":"0","num_of_group_buy_people":"0","group_buy_team_token":"","appointment_unit_type":"2","appointment_start":"20180630 13:00","appointment_end":"20180630 20:00","cover":"http:\/\/img.weiye.me\/zcimgdir\/album\/file_596db52164dfd.jpg","title":"预约","max_can_use_integral":"0","stock":"40","price":"188","sales":null,"model":"","goods_type":"1","status":"0","mass":"0.000","volume":"0.000","express_rule_id":"0"},{"id":"1031504","buyer_id":"e33581104d3fe47e2b867d38b93e8a8d","goods_id":"3977372","model_id":"0","app_id":"Z22PP52DLh","num":"1","add_time":"1525067994","parent_shop_app_id":"0","related_shop_app_id":"0","is_integral":"-1","is_seckill":"2","is_group_buy":"0","num_of_group_buy_people":"0","group_buy_team_token":"","appointment_unit_type":"2","appointment_start":"20180430 16:30","appointment_end":"20180430 23:30","cover":"http:\/\/img.weiye.me\/zcimgdir\/album\/file_596db52164dfd.jpg","title":"\u5927\u5305\u5468\u4e00\u5230\u5468\u65e5\u4e0b\u5348\u4e03\u5c0f\u65f6\u6b22\u5531_copy_copy","max_can_use_integral":"0","stock":"40","price":"188","sales":null,"model":"","goods_type":"1","status":"0","mass":"0.000","volume":"0.000","express_rule_id":"0"},{"id":"1031488","buyer_id":"e33581104d3fe47e2b867d38b93e8a8d","goods_id":"3977372","model_id":"0","app_id":"Z22PP52DLh","num":"1","add_time":"1525067717","parent_shop_app_id":"0","related_shop_app_id":"0","is_integral":"-1","is_seckill":"2","is_group_buy":"0","num_of_group_buy_people":"0","group_buy_team_token":"","appointment_unit_type":"2","appointment_start":"20180430 17:00","appointment_end":"20180501 00:00","cover":"http:\/\/img.weiye.me\/zcimgdir\/album\/file_596db52164dfd.jpg","title":"\u5927\u5305\u5468\u4e00\u5230\u5468\u65e5\u4e0b\u5348\u4e03\u5c0f\u65f6\u6b22\u5531_copy_copy","max_can_use_integral":"0","stock":"40","price":"188","sales":null,"model":"","goods_type":"1","status":"0","mass":"0.000","volume":"0.000","express_rule_id":"0"}],"is_more":0,"current_page":1,"count":"3","total_page":1,"goods_list":{"3977372":{"app_id":"Z22PP52DLh","goods_id":"3977372","title":"\u5927\u5305\u5468\u4e00\u5230\u5468\u65e5\u4e0b\u5348\u4e03\u5c0f\u65f6\u6b22\u5531_copy_copy","cover":"http:\/\/img.weiye.me\/zcimgdir\/album\/file_596db52164dfd.jpg","price":"188","sale_price":"0.00","stock":"40","sales":null,"max_can_use_integral":"0","goods_type":"1","mass":"0.000","volume":"0.000","express_rule_id":"0","status":"0","model_data":null}},"take_out_info":[]}';
        $s = json_decode($s, TRUE);
        $appoint_record_id = session('appoint_record_id');
        $uid = session('uid');
        $appoint_record_item = D('AppointRecord')->getByRecordId($appoint_record_id);

        // 预约标题
        $appoint_type_id = $appoint_record_item['item_id'];
        $title = AppointRecordModel::$APPOINT_TYPE[$appoint_type_id]['title'];

        // 预约时间
        $appoint_date = $appoint_record_item['date'];
        $appoint_time = $appoint_record_item['time'];
        list($appoint_time_start, $appoint_time_end) = explode('-', $appoint_time);
        $appoint_start = $appoint_date . ' ' . $appoint_time_start;
        $appoint_end = $appoint_date . ' ' . $appoint_time_end;


        // 预约人信息
        $appointer_item = D('User')->getByUid($uid);
        $passport = $appointer_item['passport'];
        $appointer_info = D('UserBack')->getByPassport($passport); $appointer_item['name'];
        $appointer_name = $appointer_info['name'];

        $weixin = D('Weixin')->getByOpenid(session('openid'));
        $avatar = $weixin['avatar'];
        if (!$avatar) {
            $avatar = 'http://img.zhichiwangluo.com/zcimgdir/album/file_5ac5774ba3fc4.jpg';
        }

        // 剩余名额
        $leave = 8;

        $appoint_orgin = $s['data'][0];
        unset($s['data']);

        // 将更新的资料覆盖给orgin
        $appoint_orgin['appointment_start'] = $appoint_start;
        $appoint_orgin['appointment_end'] = $appoint_end;
        $appoint_orgin['cover'] = $avatar;
        $appoint_orgin['id'] = session('appoint_record_id');
        $appoint_orgin['price'] = $leave;
        $appoint_orgin['title'] = $title;


        $s['data'][] = $appoint_orgin;


        // good_list
        $s['goods_list'][3977372]['price'] = 8;

        $result = $s;

        return $result;
    }


    public function shop_location() {
        $s = '{"status":0,"data":{"id":"1585042","app_id":"Z22PP52DLh","province_id":"0","city_id":"0","county_id":"0","shop_location":"","shop_contact":"","is_self_delivery":"0","region_string":""}}';
        return json_decode($s, TRUE);
    }

    public function add_order() {
        $remark = trim(I('remark'));
        $form_id = trim(I('formId'));

        //  操作数据库
        $appoint_record_id = session('appoint_record_id');
        D('AppointRecord')->addRemark($appoint_record_id, $remark);
        D('AppointRecord')->setRecordStatus($appoint_record_id, AppointRecordModel::$STUDENT_FINISHED);

        // 发送模版消息
        $tempMsgService = new TempMsgService();
        $uid = session('uid');
        $userService = new UserService();
        $user_info = $userService->get_more_info($uid);

        $record_item = D('AppointRecord')->getByRecordId($appoint_record_id);
        $record_date = $record_item['date'] ? $record_item['date'] : '20180615';
        $record_date = substr($record_date, 0, 4) . '-' . substr($record_date, 4, 2) . '-' . substr($record_date, 6, 2);

        $temp_data = [
            'keyword1'  => ['value'=>$user_info['name']],
            'keyword2'  => ['value'=>AppointRecordModel::$APPOINT_TYPE[$record_item['item_id']]['title']],
            'keyword3'  => ['value'=>$record_date . ' ' . $record_item['time']],
            'keyword4'  => ['value'=>$record_item['remark']?$record_item['remark']:'用户预约成功'],
        ];
        $page = 'pages/o9j42s2GS3_page10000/o9j42s2GS3_page10000';
        $send_result = $tempMsgService->doSend(session('openid'), TemplateIdModel::$APPOINT_SUCCESS_TEMPLATE_ID, $form_id, $temp_data, $page);
        if (!$send_result['success']) {
            return ['status' => 0, 'data' => $send_result['message']];
        }

        $s = '{"status":0,"data":"5b0ba93805a68639308115","session_key":"wx_webapp_session_key_610371647","form_id":"the formId is a mock one"}';
        return json_decode($s, TRUE);
    }

    /*
     * 查看订单列表
     */
    public function get_order_list() {
        $s = '{"status":0,"data":[{"form_data":{"app_info":{"app_name":"\u9884\u7ea6\u5e94\u7528","app_logo":"http:\/\/cdn.jisuapp.cn\/zhichi_frontend\/static\/invitation\/images\/logo.png"},"goods_info":[{"goods_id":"3977372","price":"188","stock":"40","goods_name":"\u5927\u5305\u5468\u4e00\u5230\u5468\u65e5\u4e0b\u5348\u4e03\u5c0f\u65f6\u6b22\u5531_copy_copy","is_integral":"-1","cover":"http:\/\/img.weiye.me\/zcimgdir\/album\/file_596db52164dfd.jpg","model":"","max_can_use_integral":"0","model_id":"0","num":"1","sub_shop_app_id":null,"related_shop_app_id":"0","mass":"0.000","volume":"0.000","express_rule_id":"0","is_seckill":2}],"remark":"f d\u68b5\u8482\u5188","sub_shop_info":{"name":""},"location_id":0,"take_out_info":null,"form_id":"the formId is a mock one","appointment_order_info":{"appointment_unit_type":"2","appointment_day":"20180609","appointment_interval":"15:30-22:30","appointment_time_long":7,"need_user_address":"1","appointment_to_store_time":"12:00","appointment_user_name":"","appointment_user_phone":"","appointment_time":"2018-06-09 15:30 ~ 15:30"},"buyer_info":{"nickname":"\u674e\u4fca\u541b","phone":null,"message":""},"original_price":188,"selected_benefit_info":"","selected_benefit":{"discount_type":null},"original_express_fee":0,"address_info":{"name":"\u5f20\u4e09","contact":"020-81167888","detailAddress":"\u65b0\u6e2f\u4e2d\u8def397\u53f7","province":{"text":"\u5e7f\u4e1c\u7701","id":"19"},"city":{"text":"\u5e7f\u5dde\u5e02","id":"231"},"district":{"text":"\u6d77\u73e0\u533a","id":"2129"},"address_id":"169690"},"tostore_data":{"tostore_order_type":"","tostore_appointment_time":"","tostore_buyer_phone":"","tostore_remark":"","location_id":0},"additional_info":"","id":"658958","order_id":"5b1b7410f0d92678562271","buyer_id":"e33581104d3fe47e2b867d38b93e8a8d","app_id":"Z22PP52DLh","transaction_id":null,"payment_id":"0","pay_mode_id":"0","status":"0","total_price":"188.00","add_time":"2018-06-09 14:30:41","payment_time":"0","refund_time":"0","goods_type":"1","parent_shop_app_id":"0","use_balance":"0.00","is_self_delivery":"0","discount_cut_price":"0.00","has_seckill":"0","team_token":"","is_group_buy_order":"0","appointment_time":"2018-06-09 15:30 ~ 15:30","goods_num":1}}],"is_more":0,"current_page":1,"count":"1","total_page":1,"current_goods_type":"1","goods_type_list":["1"],"take_out_info":[]}';
        $s = json_decode($s, TRUE);

        $order_orgin = $s['data'][0];
        unset($s['data']);

        $uid = session('uid');

        $type = I('idx_arr');

        // 预约订单列表
        if ($type) {
            if ($type['idx_value'] == "0") {
                $order_list = D('AppointRecord')->listStudentFinishedByUid($uid);
            } elseif ($type['idx_value'] == "1") {
                $order_list = D('AppointRecord')->listTeacherFinishedByUid($uid);
            }
        } else {
            $order_list = D('AppointRecord')->listValidByUid($uid);
        }


        // 预约人信息
        $appointer_item = D('User')->getByUid($uid);
        $passport = $appointer_item['passport'];
        $appointer_info = D('UserBack')->getByPassport($passport);
        $appointer_name = $appointer_info['name'];

        $weixin = D('Weixin')->getByOpenid(session('openid'));
        $avatar = $weixin['avatar'];
        if (!$avatar) {
            $avatar = 'http://img.zhichiwangluo.com/zcimgdir/album/file_5ac5774ba3fc4.jpg';
        }

        if (empty($order_list)) {
            $s = '{"status":0,"data":[],"current_goods_type":"1","goods_type_list":["1"]}';
            return json_decode($s, TRUE);
        }

        foreach ($order_list as $order_item) {
            $temp = $order_orgin;

            $title = AppointRecordModel::$APPOINT_TYPE[$order_item['item_id']]['title'] .'_' .$appointer_name . '_' . $order_item['date'] . '~' . $order_item['time'];
            $create_time = date('Y-m-d H:i:s', $order_item['gmt_create']);

            $temp['form_data']['add_time'] = $create_time;
            $temp['form_data']['goods_info'][0]['goods_name'] = $title;
            $temp['form_data']['goods_info'][0]['cover'] = $avatar;
            $temp['form_data']['order_id'] = $order_item['id'];
            $temp['form_data']['status'] = $order_item['status'];

            $s['data'][] = $temp;
        }


        return $s;
    }

    /*
     * 获取订单详细信息
     */
    public function get_order_info() {
        $s = '{"status":0,"data":[{"form_data":{"app_info":{"app_name":"\u9884\u7ea6\u5e94\u7528","app_logo":"http:\/\/cdn.jisuapp.cn\/zhichi_frontend\/static\/invitation\/images\/logo.png"},"goods_info":[{"goods_id":"3977372","price":"188","stock":"40","goods_name":"\u5927\u5305\u5468\u4e00\u5230\u5468\u65e5\u4e0b\u5348\u4e03\u5c0f\u65f6\u6b22\u5531_copy_copy","is_integral":"-1","cover":"http:\/\/img.weiye.me\/zcimgdir\/album\/file_596db52164dfd.jpg","model":"","max_can_use_integral":"0","model_id":"0","num":"1","sub_shop_app_id":null,"related_shop_app_id":"0","mass":"0.000","volume":"0.000","express_rule_id":"0","is_seckill":2,"delivery_id":"0"}],"remark":"f d\u68b5\u8482\u5188","sub_shop_info":null,"location_id":0,"take_out_info":null,"form_id":"the formId is a mock one","appointment_order_info":{"appointment_unit_type":"2","appointment_day":"20180609","appointment_interval":"15:30-22:30","appointment_time_long":7,"need_user_address":"1","appointment_to_store_time":"12:00","appointment_user_name":"","appointment_user_phone":"","appointment_time":"2018-06-09 15:30 ~ 22:30"},"buyer_info":{"nickname":"\u674e\u4fca\u541b","phone":null,"message":""},"original_price":"188.00","selected_benefit_info":[],"selected_benefit":[],"original_express_fee":0,"address_info":{"name":"\u5f20\u4e09","contact":"020-81167888","detailAddress":"\u65b0\u6e2f\u4e2d\u8def397\u53f7","province":{"text":"\u5e7f\u4e1c\u7701","id":"19"},"city":{"text":"\u5e7f\u5dde\u5e02","id":"231"},"district":{"text":"\u6d77\u73e0\u533a","id":"2129"},"address_id":"169690"},"tostore_data":{"tostore_order_type":"","tostore_appointment_time":"","tostore_buyer_phone":"","tostore_remark":"","location_id":0},"additional_info":"","id":"658958","order_id":"5b1b7410f0d92678562271","buyer_id":"e33581104d3fe47e2b867d38b93e8a8d","app_id":"Z22PP52DLh","transaction_id":"","payment_id":"0","pay_mode_id":"0","status":"7","total_price":"188.00","add_time":"2018-06-09 14:30:41","payment_time":0,"refund_time":"0","goods_type":"1","parent_shop_app_id":"0","use_balance":"0.00","is_self_delivery":"0","discount_cut_price":"0.00","has_seckill":"0","team_token":"","is_group_buy_order":"0","appointment_time":"2018-06-09 15:30 ~ 22:30","order_total_price":"188.00","can_use_benefit":{"status":0,"vip_benefit":[],"coupon_benefit":[],"integral_benefit":[],"max_can_use_integral":[],"user_integral":[],"data":[],"selected_index":0}},"is_deleted":"0","time_long":"0","appointment_goods_id":"3977372","related_shop_app_id":"0","is_integral":"0","express_fee":"0.00","is_hide":"0","reverted_sale":"1","is_distribution_order":"0","location_id":"0","balance":"0.00"}],"is_more":0,"current_page":1,"count":"1","total_page":1}';
        $s = json_decode($s, TRUE);

        $order_orgin = $s['data'][0];
        unset($s['data']);

        $r_id = intval(I('order_id'));
        $uid = session('uid');


        // 预约人信息
        $appointer_item = D('User')->getByUid($uid);
        $passport = $appointer_item['passport'];
        $appointer_info = D('UserBack')->getByPassport($passport); $appointer_item['name'];
        $appointer_name = $appointer_info['name'];

        $weixin = D('Weixin')->getByOpenid(session('openid'));
        $avatar = $weixin['avatar'];
        if (!$avatar) {
            $avatar = 'http://img.zhichiwangluo.com/zcimgdir/album/file_5ac5774ba3fc4.jpg';
        }

        $order_item = D('AppointRecord')->getByRecordId($r_id);
        $title = AppointRecordModel::$APPOINT_TYPE[$order_item['item_id']]['title'] .'_' .$appointer_name . '_' . $order_item['date'] . '~' . $order_item['time'];

        $order_orgin['form_data']['goods_info'][0]['goods_name'] = $title;
        $order_orgin['form_data']['goods_info'][0]['cover'] = $avatar;
        $order_orgin['form_data']['appointment_order_info']['appointment_day'] = $order_item['date'];
        $order_orgin['form_data']['appointment_order_info']['appointment_interval'] = $order_item['time'];
        $order_orgin['form_data']['remark'] = $order_item['remark'];
        $order_orgin['form_data']['status'] = $order_item['status'];
        $order_orgin['form_data']['order_id'] = $order_item['gmt_create'];


        $s['data'][] = $order_orgin;

        return $s;
    }

    public function address() {
        $s = '{"status":0,"data":[{"id":"169690","buyer_id":"e33581104d3fe47e2b867d38b93e8a8d","address_info":{"name":"\u5f20\u4e09","contact":"020-81167888","detailAddress":"\u65b0\u6e2f\u4e2d\u8def397\u53f7","province":{"text":"\u5e7f\u4e1c\u7701","id":"19"},"city":{"text":"\u5e7f\u5dde\u5e02","id":"231"},"district":{"text":"\u6d77\u73e0\u533a","id":"2129"}},"add_time":"1525069068","is_default":"0","telphone":"020-81167888","detail_address":"\u65b0\u6e2f\u4e2d\u8def397\u53f7","latitude":"23.09642","longitude":"113.32377"}],"is_more":0,"current_page":-1,"count":"1","total_page":1}';
        $s = json_decode($s, TRUE);
        return $s;
    }

    /*
     * 取消预约订单
     */
    public function cancel_order() {

        $rid = intval(I('order_id'));
        $form_id = trim(I('formId'));
        $uid = session('uid');

        // 发送模版消息
        $tempMsgService = new TempMsgService();
        $userService = new UserService();
        $user_info = $userService->get_more_info($uid);

        $record_item = D('AppointRecord')->getByRecordId($rid);
        $record_date = $record_item['date'] ? $record_item['date'] : '20180615';
        $record_date = substr($record_date, 0, 4) . '-' . substr($record_date, 4, 2) . '-' . substr($record_date, 6, 2);

        $temp_data = [
            'keyword1'  => ['value'=>$user_info['name']],
            'keyword2'  => ['value'=>AppointRecordModel::$APPOINT_TYPE[$record_item['item_id']]['title']],
            'keyword3'  => ['value'=>$record_date . ' ' . $record_item['time']],
            'keyword4'  => ['value'=>date('Y-m-d H:i:s', time())],
            'keyword5'  => ['value'=>'用户已取消预约'],
        ];

        $page = 'pages/o9j42s2GS3_page10000/o9j42s2GS3_page10000';
        $template_id = TemplateIdModel::$APPOINT_CANCEL_TEMPLATE_ID;
        $send_result = $tempMsgService->doSend(session('openid'), $template_id, $form_id, $temp_data, $page);
        if (!$send_result['success']) {
            return ['status' => 0, 'data' => $send_result['message']];
        }

        // 删除预约记录 todo 这里的删除不应该是真正的删除记录，只要标记下就好
        D('AppointRecord')->deleteByRid($rid);
        $s = '{"status":0,"data":"659386"}';
        return json_decode($s, TRUE);

    }


    /**
     * 收集formid
     */
    public function collect_formid() {

        $formid = trim(I('formid'));
        $uid = session('uid');

        if (!$formid || !$uid) {
            return ['status' => 0, 'data' => ''];
        }

        // 排查电脑设备模拟的formid
        if ($formid == 'the formId is a mock one') {
            return ['status' => 0, 'data' => ''];
        }

        D('Formid')->add($uid, $formid);

        return ['status' => 0, 'data'=>[$uid, $formid]];

    }

}