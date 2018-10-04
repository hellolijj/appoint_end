<?php
$s = '
[{
    "id": 2,
	"pid": 0,
	"title": "国教预约系统后台管理",
	"node": "",
	"icon": "",
	"url": "#",
	"params": "",
	"target": "_self",
	"sort": 1000,
	"status": 1,
	"create_by": 0,
	"create_at": "2015-11-16 19:15:38",
	"sub": [{
        "id": 4,
		"pid": 2,
		"title": "系统配置",
		"node": "",
		"icon": "",
		"url": "#",
		"params": "",
		"target": "_self",
		"sort": 100,
		"status": 1,
		"create_by": 0,
		"create_at": "2016-03-14 18:12:55",
		"sub": [{
            "id": 94,
			"pid": 4,
			"title": "后台首页",
			"node": "",
			"icon": "",
			"url": "\/index.php\/Admin\/Index\/main.html",
			"params": "",
			"target": "_self",
			"sort": 1,
			"status": 1,
			"create_by": 0,
			"create_at": "2017-08-08 11:28:43"
		}]
	}, {
        "id": 102,
		"pid": 2,
		"title": "用户管理",
		"node": "",
		"icon": "",
		"url": "#",
		"params": "",
		"target": "_self",
		"sort": 300,
		"status": 1,
		"create_by": 0,
		"create_at": "2018-01-15 15:46:51",
		"sub": [{
            "id": 103,
			"pid": 102,
			"title": "注册绑定用户",
			"node": "",
			"icon": "",
			"url": "\/index.php\/Admin\/user\/all",
			"params": "",
			"target": "_self",
			"sort": 0,
			"status": 1,
			"create_by": 0,
			"create_at": "2018-01-15 15:47:03"
		}, {
            "id": 104,
			"pid": 102,
			"title": "所有用户",
			"node": "",
			"icon": "",
			"url": "\/index.php\/Admin\/user\/back",
			"params": "",
			"target": "_self",
			"sort": 0,
			"status": 1,
			"create_by": 0,
			"create_at": "2018-01-15 15:48:03"
		}, {
            "id": 105,
			"pid": 102,
			"title": "导入新用户",
			"node": "",
			"icon": "",
			"url": "\/appoint\/index.php\/Admin\/user\/load",
			"params": "",
			"target": "_self",
			"sort": 0,
			"status": 1,
			"create_by": 0,
			"create_at": "2018-01-15 15:48:03"
		}]
	}, {
        "id": 20,
		"pid": 2,
		"title": "预约平台",
		"node": "",
		"icon": "",
		"url": "#",
		"params": "",
		"target": "_self",
		"sort": 500,
		"status": 1,
		"create_by": 0,
		"create_at": "2016-03-14 18:11:41",
		"sub": [{
            "id": 19,
			"pid": 20,
			"title": "签证预约",
			"node": "",
			"icon": "fa fa-user-secret",
			"url": "\/index.php\/Admin\/Appoint\/visa?item_id=1234",
			"params": "",
			"target": "_self",
			"sort": 10,
			"status": 1,
			"create_by": 0,
			"create_at": "2015-11-17 13:18:12"
		}, {
            "id": 20,
			"pid": 21,
			"title": "退费预约",
			"node": "",
			"icon": "fa fa-user-secret",
			"url": "\/index.php\/Admin\/Appoint\/visa?item_id=1235",
			"params": "",
			"target": "_self",
			"sort": 10,
			"status": 1,
			"create_by": 0,
			"create_at": "2015-11-17 13:18:12"
		}, {
            "id": 20,
			"pid": 21,
			"title": "接待日预约",
			"node": "",
			"icon": "fa fa-user-secret",
			"url": "\/index.php\/Admin\/Appoint\/visa?item_id=1236",
			"params": "",
			"target": "_self",
			"sort": 10,
			"status": 1,
			"create_by": 0,
			"create_at": "2015-11-17 13:18:12"
		}, {
            "id": 20,
			"pid": 21,
			"title": "宿舍调换预约",
			"node": "",
			"icon": "fa fa-user-secret",
			"url": "\/index.php\/Admin\/Appoint\/visa?item_id=1237",
			"params": "",
			"target": "_self",
			"sort": 10,
			"status": 1,
			"create_by": 0,
			"create_at": "2015-11-17 13:18:12"
		}, {
            "id": 23,
			"pid": 24,
			"title": "申请咨询",
			"node": "",
			"icon": "fa fa-user-secret",
			"url": "\/index.php\/Admin\/Appoint\/visa?item_id=1238",
			"params": "",
			"target": "_self",
			"sort": 10,
			"status": 1,
			"create_by": 0,
			"create_at": "2015-11-17 13:18:12"
		},{
            "id": 24,
			"pid": 25,
			"title": "新生报到",
			"node": "",
			"icon": "fa fa-user-secret",
			"url": "\/index.php\/Admin\/Appoint\/visa?item_id=1239",
			"params": "",
			"target": "_self",
			"sort": 10,
			"status": 1,
			"create_by": 0,
			"create_at": "2015-11-17 13:18:12"
		}, {
            "id": 25,
			"pid": 26,
			"title": "缴费预约",
			"node": "",
			"icon": "fa fa-user-secret",
			"url": "\/index.php\/Admin\/Appoint\/visa?item_id=1240",
			"params": "",
			"target": "_self",
			"sort": 10,
			"status": 1,
			"create_by": 0,
			"create_at": "2015-11-17 13:18:12"
		}, {
            "id": 26,
			"pid": 27,
			"title": "材料预约",
			"node": "",
			"icon": "fa fa-user-secret",
			"url": "\/index.php\/Admin\/Appoint\/visa?item_id=1241",
			"params": "",
			"target": "_self",
			"sort": 10,
			"status": 1,
			"create_by": 0,
			"create_at": "2015-11-17 13:18:12"
		}]
	}]
}]';

return [
    'menus' => json_decode($s, TRUE),
];