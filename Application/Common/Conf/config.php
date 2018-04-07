<?php
return array(
    /* 数据库配置 */
    'DB_TYPE' => 'mysql', // 数据库类型
    'DB_HOST' => '127.0.0.1', // 服务器地址
    'DB_NAME' => 'pingshifen', // 数据库名
    'DB_USER' => 'root', // 用户名
    'DB_PWD' => '',  // 密码
    'DB_PORT' => '3306', // 端口
    'DB_PREFIX' => 'appoint_', // 数据库表前缀

    // 默认访问Api
    'MODULE_ALLOW_LIST' => array ('Api'),
    'DEFAULT_MODULE' => 'Api',

    // 配置路由
    'URL_MODEL' => 1,
    'URL_ROUTER_ON'	=> true,
    'URL_MAP_RULES'=>array(
//        '/^new\/(\d{4})\/(\d{2})$/' => 'News/achive?year=:1&month=:2'
        '/sdfa*/' => 'Gateway/route',
        '/^(*)$/' => 'Gateway/route'

    ),
);