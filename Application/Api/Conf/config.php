<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/1/27
 * Time: 上午12:14
 */

return array(

    'APP_NAME' => 'appoint', 'API_LIST' => ['STUDENT', 'WEIXIN', 'TEACHER', 'MY', 'COURSE', 'SIGNIN', 'QUESTION', 'USER'],

    /* 数据库配置 */
    'DB_TYPE' => 'mysql', // 数据库类型
    'DB_HOST' => '127.0.0.1', // 服务器地址
    'DB_NAME' => 'pingshifen', // 数据库名
    'DB_USER' => 'root', // 用户名
    'DB_PWD' => '',  // 密码
    'DB_PORT' => '3306', // 端口
    'DB_PREFIX' => 'appoint_', // 数据库表前缀

    'DB_PARAMS' => array(\PDO::ATTR_CASE => \PDO::CASE_NATURAL), // 数据库兼容大小写

    /* 文件上传路径 */
    'APP_ROOT' => 'http://127.0.0.1/appoint/', 'UPLOAD_DIR' => 'Uploads/Picture/', 'UPLOAD_ROOT' => 'http://127.0.0.1/appoint/Uploads/Picture/',

    /* 公众号的相关配置 */
    'APP_ID' => 'wx0f0fe4d0f2fffd6c', 'APP_SECRET' => '5a6190163c43ca87e911440049d93d79', 'APP_LOGO' => 'http://pingshif-img.stor.sinaapp.com/2018-02-21/logo01222_1979.jpg',

    /*默认控制器默认方法*/
    'DEFAULT_CONTROLLER'     => 'Gateway', // 默认控制器名称
    'DEFAULT_ACTION'         => 'route', // 默认操作名称
);