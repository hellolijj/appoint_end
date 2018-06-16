<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/6/16
 * Time: 下午12:05
 */

namespace Api\Controller;

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

        $today_date = date('Ymd', time()+3*24*3600);
        $remind_list = D('AppointRecord')->listByDate($today_date);
        $remind_uid_list = result_to_array($remind_list, 'uid');

        // todo 转换更多的uids => more_info
        p($remind_uid_list);die;

        foreach ($remind_list as $_list) {

        }

    }

}