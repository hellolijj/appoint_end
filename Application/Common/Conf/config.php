<?php
return array(


    'MODULE_ALLOW_LIST' => array ('Api'),
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
);