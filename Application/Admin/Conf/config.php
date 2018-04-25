<?php

return array(


    'TMPL_PARSE_STRING' => array('__PUBLIC__' => __ROOT__ . '/Public/Admin', // 更改默认的/Public 替换规则
        '__JS__' => '/Public/JS/', // 增加新的JS类库路径替换规则
        '__UPLOAD__' => '/Uploads', // 增加新的上传路径替换规则
        '__STATIC__' => '/Public/Admin',
    ),

//    'LAYOUT_ON' => TRUE, 'LAYOUT_NAME' => 'layout',

    'APP_ROOT' => 'http://127.0.0.1/pingshifen/',

    'UPLOAD_DIR' => './Uploads/',
    'LOAD_EXT_CONFIG'   => 'menu',

);