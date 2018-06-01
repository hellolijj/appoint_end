<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/1/27
 * Time: 下午10:57
 * api项目的网关
 */

namespace Api\Controller;

use Api\Logic\BaseLogic;
use Api\Model\MethodConvertModel;

class GatewayController extends BaseController {

    protected $method = '';

    protected $params = [];

    public function __construct ()
    {
        parent::__construct();
    }


    /**
     * 1、检查关键信息 时间错 method在不在
     * 2、检查openid 并缓存 student info
     * 3、执行指定的logic方法
     */
    public function route ()
    {
        $this->check();

        $method_arr = explode('/', $this->method);
        $logic_name = $method_arr[0];
        $function_name = $method_arr[1];

        $class_name = '\Api\Logic\\' . $logic_name . 'Logic';
        $LOGIC = new $class_name();

        $result = new BaseLogic();

        // 对方法的判断
        if (!method_exists($LOGIC, $function_name)) {
            $this->ajaxReturn($result->setError('无效的API参数'));
        }

        $this->ajaxReturn($LOGIC->{$function_name}());
    }

    private function check ()
    {

        $result = new BaseLogic();
        $method = I('r') ? I('r') : I('get.r');
        $timestamp = I('timestamp');

        // 检查时间戳
        if (!empty($timestamp)) {
            $offset_time = abs(intval($timestamp) - time());
            if ($offset_time > 600) {
                $this->ajaxReturn($result->setError('失效的时间戳'));
            }
        }

        // 检查logic类 方法
        if (empty($method) || !in_array($method, array_keys(MethodConvertModel::$VALID_METHODS))) {
            $this->ajaxReturn($result->setError('601 无效的API方法'));
        }

        $this->method = MethodConvertModel::$VALID_METHODS[$method];


        // 检查应用名称 和 接口合法性
        list($logic_name, $function_name) = explode('/', $this->method);
        if (!in_array(strtoupper($logic_name), C('API_LIST'))) {
            $this->ajaxReturn($result->setError('602 无效的API方法'));
        }

    }


}