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

        // 验证手机号码
        'AppUser/GetPhoneNumber' => 'Weixin/get_phone_number',

        // 保存用户信息
        'AppData/saveUserInfo' => 'User/bind_user_info',

        // 首页
        'AppExtensionInfo/carouselPhotoProjiect' => 'User/carousel_photo',  //获取首页轮廓图

        // 模拟考试
        'question/get_id_items' => 'question/get_id_items',  // 获取考试题目
        'question/get_info' => 'question/get_info',  // 获取考试题目详情
        'question/exam_submit' => 'question/hand_paper',


    ];

}