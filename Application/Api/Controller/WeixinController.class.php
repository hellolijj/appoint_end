<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/2/3
 * Time: 上午10:48
 */


namespace Api\Controller;

use Api\Model\WeixinModel;
use Api\Service\WeixinService;
use Think\Controller;

/**
 * 微信控制器
 * 处理与微信相关的逻辑
 */
class WeixinController extends Controller {
    //系统首页
    public function index ()
    {

    }





    /*
     * 小程序的授权登陆接口
     */
    public function login ()
    {
        $code = I("code");
        $rawData = htmlspecialchars(I("rawData"));
        $signature = I("signature");
        $encryptedData = I("encryptedData");
        $iv = I("iv");
        $from = I('from');
        if ($from) {
            $wxHelper = NEW  \Weixin\Xiaochengxu\WXLoginHelper($code, $rawData, $signature, $encryptedData, $iv, $from);
        } else {
            $wxHelper = NEW  \Weixin\Xiaochengxu\WXLoginHelper($code, $rawData, $signature, $encryptedData, $iv);
        }
        $data_result = $wxHelper->checkLoginV2();
        if ($data_result['success'] === FALSE) {
            $this->ajaxReturn(['success' => FALSE, 'message' => $data_result['message']]);
        }
        $this->ajaxReturn(['success' => TRUE, 'data' => $data_result]);

        $data = $data_result['data'];
        $data['avatar'] = $data['avatarUrl'];    //解决命名大小写问题
        $data['nickname'] = $data['nickName'];
        $save_result = $this->save_weixin_user($data);
        if ($save_result['success'] === FALSE) {
            $this->ajaxReturn(['success' => FALSE, 'message' => $save_result['message']]);
        }
        S($data['session3rd'], json_encode($data), 3600);  // 此缓存用于后面的验证是否登陆
        session('openid', $data['openId']);
        $weixin_user_result = $this->get_user_info(session('openid'));
        if ($weixin_user_result['success'] === TRUE && $weixin_user_result['data']['openid']) {
            $regData = $weixin_user_result['data'];
            $regData['session3rd'] = $data['session3rd'];
            $regData['session_id'] = session_id();
            $this->ajaxReturn(['success' => TRUE, 'data' => $regData]);
        }
        $data['session_id'] = session_id();
        $data['type'] = 0;  //用户类型
        $this->ajaxReturn(['success' => TRUE, 'data' => $data]);
    }


    /*
     * 跟客户端保持check_3rdsession相同,,实际上就是检查是否登陆
     */
    public function check_3rdsession ()
    {
        $post_session3rd = I('rd3_session');
        $local_session3rd = session('session3rd');
        if (empty($local_session3rd)) {
            session('session3rd', NULL);
            $this->ajaxReturn(['success' => FALSE, 'data' => FALSE, 'message' => '服务端数据过期']);
        }
        if (!session('openid')) {
            $this->ajaxReturn(['success' => FALSE, 'data' => FALSE, 'message' => 'openid数据失效']);
        }
        if ($post_session3rd && $post_session3rd != $local_session3rd) {
            session('session3rd', NULL);
            $this->ajaxReturn(['success' => FALSE, 'data' => FALSE, 'message' => '客户端端数据过期']);
        }
        if ($post_session3rd && $post_session3rd == $local_session3rd) {
            $user_info_result = $this->get_user_info(session('openid'));
            if ($user_info_result['success'] === TRUE) {
                $data = $user_info_result['data'];
            } else {
                $data = [];
            }
            $this->ajaxReturn(['success' => TRUE, 'data' => $data, 'message' => '登陆成功']);
        } else {
            $this->ajaxReturn(['success' => FALSE, 'data' => FALSE, 'message' => '未知的错误']);
        }
    }

    /*
     * 获取用户手机号码
     */
    public function get_user_tel ()
    {
        $post_3rdsession = I('rd3_session');
        $encryptedData = I('encryptedData');
        $iv = I('iv');
        $wxHelper = NEW  \Weixin\Xiaochengxu\WXLoginHelper();
        $data = $wxHelper->getUserTel($post_3rdsession, $encryptedData, $iv);
        if ($data['success'] === FALSE) {
            $this->ajaxReturn(['success' => FALSE, 'message' => $data['message']]);
        }
        $this->ajaxReturn(['success' => TRUE, 'data' => $data]);
    }

    /*
     * 保存每个登陆访问的用户信息
     * @return true 保持功能 array 包含返回提示信息
     */
    private function save_weixin_user ($user_info = '')
    {
        if (!is_array($user_info) || !$user_info['openId']) {
            return ['success' => FALSE, 'message' => '无效的openid'];
        }
        $openid = $user_info['openId'];
        $is_register = $this->is_register($openid);
        if (FALSE === $is_register) {
            $data = ['openid' => $user_info['openId'], 'nickname' => $user_info['nickName'], 'gender' => intval($user_info['gender']), 'country' => $user_info['country'], 'province' => $user_info['province'], 'city' => $user_info['city'], 'avatar' => $user_info['avatarUrl'], 'gmt_create' => time(), 'gmt_modified' => time(),];
            M('Weixin')->add($data);
            return ['success' => TRUE];
        } elseif (is_array($is_register)) {
            return ['success' => FALSE, 'message' => $is_register['message']];
        }
    }

    /*
     * 判断是否注册
     * @ return true 注册 false 未注册 array 错误的原因
     */
    private function is_register ($openid = '')
    {
        if (!$openid) {
            return ['success' => FALSE, 'message' => '参数为空'];
        }
        $weixinService = new WeixinService();
        $weixin_user_result = $weixinService->getByOpenid($openid);
        if ($weixin_user_result['success'] === FALSE && $weixin_user_result['code'] === WeixinService::$ERROR_NO_REGISTER) {
            return FALSE;
        }
        if ($weixin_user_result['success'] === TRUE) {
            return TRUE;
        }
        return ['success' => FALSE, 'message' => '未知的原因'];;
    }

    /*
     * 获取用户信息
     */
    private function get_user_info ($openid = '')
    {
        if (!$openid) {
            return ['success' => FALSE, 'message' => '参数为空'];
        }
        $weixinService = new WeixinService();
        $weixin_user_result = $weixinService->getByOpenid($openid);
        if ($weixin_user_result['success'] === FALSE) {
            return ['success' => FALSE, 'message' => '获取信息失败'];;
        }

        // todo 如果是已注册用户，则返回真实姓名、班级、学号等信息。
        $weixin_user = $weixin_user_result['data'];
        if (in_array($weixin_user['type'], [WeixinModel::$USER_TYPE_STUDENT, WeixinModel::$USER_TYPE_TEACHER])) {
            if ($weixin_user['type'] == WeixinModel::$USER_TYPE_STUDENT) {
                $student_user = D('Student')->getById($weixin_user['uid']);
                if ($student_user['name']) {
                    $weixin_user['name'] = $student_user['name'];
                }
                if ($student_user['number']) {
                    $weixin_user['number'] = $student_user['number'];
                }
                if ($student_user['school']) {
                    $weixin_user['school'] = $student_user['school'];
                }
            } elseif ($weixin_user['type'] == WeixinModel::$USER_TYPE_TEACHER) {
                $teacher_user = D('Teacher')->getById($weixin_user['uid']);
                if ($teacher_user['name']) {
                    $weixin_user['name'] = $teacher_user['name'];
                }
                if ($teacher_user['school']) {
                    $weixin_user['school'] = $teacher_user['school'];
                }
            }
        }
        return ['success' => TRUE, 'data' => $weixin_user];
    }


}