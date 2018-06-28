
// +----------------------------------------------------------------------
// | Think.Admin
// +----------------------------------------------------------------------
// | 版权所有 2014~2017 广州楚才信息科技有限公司 [ http://www.cuci.cc ]
// +----------------------------------------------------------------------
// | 官方网站: http://think.ctolog.com
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | github开源项目：https://github.com/zoujingli/Think.Admin
// +----------------------------------------------------------------------

// 当前资源URL目录
var _root = (function () {
    var scripts = document.scripts, src = scripts[scripts.length - 1].src;
    return src.substring(0, src.lastIndexOf("/") + 1);
})();

// RequireJs 配置参数
require.config({
    waitSeconds: 0,
    baseUrl: _root,
    map: {'*': {css: _root + '../plugs/require/require.css.js'}},
    paths: {
        // 自定义插件（源码自创建或已修改源码）
        'admin.plugs': ['plugs'],
        'admin.listen': ['listen'],
        'template': ['../plugs/template/template'],
        'pcasunzips': ['../plugs/jquery/pcasunzips'],
        'ueditor': ['../plugs/ueditor/ueditor'],
        'jqzoom': ['../plugs/jquery/jquery.jqzoom'],
        'goods': ['../plugs/caigou/goods'],
        'loadSelect': ['../plugs/jquery/loadSelect'],
        // 开源插件(未修改源码)
        'pace': ['../plugs/jquery/pace.min'],
        'qrcode': ['../plugs/jquery/jquery.qrcode.min'],
        'json': ['../plugs/jquery/json2.min'],
        'layui': ['../plugs/layui/layui'],
        'jquery': ['../plugs/jquery/jquery.min'],
        'base64': ['../plugs/jquery/base64.min'],
        'angular': ['../plugs/angular/angular.min'],
        'ckeditor': ['../plugs/ckeditor/ckeditor'],
        'websocket': ['../plugs/socket/websocket'],
        'bootstrap': ['../plugs/bootstrap/js/bootstrap.min'],
        'bootstrap.typeahead': ['../plugs/bootstrap/js/bootstrap3-typeahead.min'],
        'jquery.ztree': ['../plugs/ztree/jquery.ztree.all.min'],
        'jquery.masonry': ['../plugs/jquery/masonry.min'],
        'jquery.cookies': ['../plugs/jquery/jquery.cookie'],
        'zeroclipboard': ['../plugs/ueditor/third-party/zeroclipboard/ZeroClipboard.min'],
    },
    shim: {
        'layui': {deps: ['jquery']},
        'ckeditor': {deps: ['jquery']},
        'ueditor': {deps: ['jquery','zeroclipboard','css!' + _root + '../plugs/ueditor/themes/default/css/ueditor.css']},
        'websocket': {deps: [_root + '../plugs/socket/swfobject.min.js']},
        'pcasunzips': {deps: ['jquery']},
        'admin.plugs': {deps: ['jquery', 'layui']},
        'admin.listen': {deps: ['jquery', 'jquery.cookies', 'admin.plugs']},
        'bootstrap': {deps: ['jquery']},
        'bootstrap.typeahead': {deps: ['bootstrap']},
        'jquery.ztree': {deps: ['jquery', 'css!' + _root + '../plugs/ztree/zTreeStyle/zTreeStyle.css']},
        'jquery.cookies': {deps: ['jquery']},
        'jquery.masonry': {deps: ['jquery']},
    },
    // deps: [],
    // 开启debug模式，不缓存资源
     urlArgs: "ver=" + (new Date()).getTime()
});

window.WEB_SOCKET_SWF_LOCATION = _root + "../plugs/socket/WebSocketMain.swf";
window.UEDITOR_HOME_URL = (window.ROOT_URL ? window.ROOT_URL + '/static/' : _root) + '../plugs/ueditor/';

// UI框架初始化
PageLayout.call(this);
function PageLayout(callback, custom, basic) {
    window.WEB_SOCKET_SWF_LOCATION = _root + "../plugs/socket/WebSocketMain.swf";
    require(basic || ['pace', 'jquery', 'layui', 'bootstrap'], function () {
        layui.config({dir: _root + '../plugs/layui/'});
        layui.use(['layer', 'form', 'laydate','element','upload'], function () {
            window.layer = layui.layer,     window.form = layui.form,window.laydate = layui.laydate , window.element = layui.element , window.upload = layui.upload;
            require(custom || ['admin.listen', 'ckeditor'], callback || false);
        });
    });
}
