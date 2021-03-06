<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/4/5
 * Time: 下午9:49
 */

namespace Api\Model;

class MethodConvertModel {

    public static $VALID_METHODS = [

        // 首次进入加载
        'AppUser/MarkWxXcxStatus' => 'User/status',
        'appShop/isNeedLogin' => 'User/no_shop',

        // 点击获取个人信息
        'AppUser/onLogin' => 'Weixin/check_login',  // 这个请求用于判断用户是否登陆
        'AppData/getXcxUserInfo' => 'Weixin/get_user_info',
        'appShop/getIntegralLog' => 'User/no_vip',
        'AppUser/LoginUser' => 'Weixin/login',   //用户登陆
        'AppUser/check_openid' => 'Weixin/check_openid',

        // 验证手机号码
        'AppUser/GetPhoneNumber' => 'Weixin/get_phone_number',

        // 保存用户信息
        'AppData/saveUserInfo' => 'User/bind_user_info',

        // 首页
        'AppExtensionInfo/carouselPhotoProjiect' => 'User/carousel_photo',  //获取首页轮廓图

        // 模拟考试
        'question/get_id_items' => 'Question/get_id_items',  // 获取考试题目
        'question/get_info' => 'Question/get_info',  // 获取考试题目详情
        'question/exam_submit' => 'Question/hand_paper',
        'question/get_submit_list' => 'Question/get_submit_list',

        // 预约
        'AppShop/getGoods' => 'Appoint/details',
        'AppShop/GetAssessList' => 'Appoint/access',
        'AppAppointment/getAppointmentList' => 'Appoint/time_list',
        'AppShop/AddCart' => 'Appoint/card',
        'AppShop/calculationPrice' => 'Appoint/calculate',
        'AppShop/AddDocument' => 'Appoint/add_document',   //材料申请


        'AppShop/getAppShopLocationInfo' => 'Appoint/shop_location',   //不知道干嘛的
        'AppShop/cartList' => 'Appoint/cart_list',
        'AppShop/addCartOrder' => 'Appoint/add_order',

        // 查看定点列表
        'AppShop/orderList' => 'Appoint/get_order_list',
        'AppShop/getOrder' => 'Appoint/get_order_info',
        'AppShop/addressList' => 'Appoint/address',
        'AppShop/cancelOrder' => 'Appoint/cancel_order',

        // 收集formid
        'AppShop/collectFormid' => 'Appoint/collect_formid',
        ''
     ];

    
}