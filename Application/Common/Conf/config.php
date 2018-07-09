<?php
return array(


    'MODULE_ALLOW_LIST' => array ('Api', 'Admin', 'Page'),
    'DEFAULT_MODULE' => 'Api',     // 默认访问Api
    'SHOW_PAGE_TRACE'   =>  true,

    'URL_CASE_INSENSITIVE'  =>  true,  //url不区分大小写
    'LOAD_EXT_CONFIG'   => 'db',

    // 配置路由
    'URL_MODEL' => 1,
    'URL_ROUTER_ON'	=> true,
    'URL_MAP_RULES'=>array(
        '/sdfa*/' => 'Gateway/route',
        '/^(*)$/' => 'Gateway/route'
    ),

    /* 公众号的相关配置 */
    'APP_ID' => 'wx0f0fe4d0f2fffd6c', 'APP_SECRET' => '5a6190163c43ca87e911440049d93d79', 'APP_LOGO' => 'http://pingshif-img.stor.sinaapp.com/2018-02-21/logo01222_1979.jpg',

    'SHOW_PAGE_TRACE' => FALSE,  // 关闭trance
);