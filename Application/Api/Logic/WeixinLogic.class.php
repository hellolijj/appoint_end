<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/2/3
 * Time: 下午3:08
 */

namespace Api\Logic;

use Api\Model\WeixinModel;
use Api\Service\BaseService;
use Api\Service\UserService;
use Api\Service\WeixinService;
use Weixin\Xiaochengxu\WXBizDataCrypt;
use Weixin\Xiaochengxu\WXLoginHelper;

class WeixinLogic extends BaseLogic {

    public function __construct ()
    {
    }

    public function getOpenid ()
    {
        $openid = session('openid');
        if ($openid) {
            return $this->setSuccess(['openid' => $openid], '获取openid成功');
        } else {
            return $this->setError('获取openid失败');
        }
    }

    /**
     * 传输了session_key，要么传了一个code
     * 项目每次请求都带有一个session_key，项目通过session_key来获取openid，来识别个人信息。
     *
     * return is_lgoin 0 没有注册，会唤起wx_getuserinfo 操作
     * return  没有缓存的
     */
    public function check_login() {
        $code = I('code');
        $session_key = I('session_key');
        if (!$code || $session_key) {
            return ['data' => '', 'is_login' => 1, 'status' => 0];
        }
        if (!$code) {
            return ['data' => '缺少登陆code参数，请删除小程序，重新进入', 'is_login' => 1, 'status' => 1];
        }
        $wxHelper = NEW WXLoginHelper($code);
        $data_result = $wxHelper->checkLoginV2();

        if ($data_result['success'] === FALSE) {
            return ['data' => $data_result['message'], 'is_login' => 0, 'status' => 1,];
        }
        $openid = $data_result['openid'];
        $session_key = $data_result['session_key'];
        session('openid', $openid);
        session('session_key', $session_key);


        $weixinService = new WeixinService();
        $is_passer = $weixinService->is_passer($openid);
        if ($is_passer['code'] == 1) {
            return $is_passer;
        }

        if (FALSE === $is_passer) {
            D('Weixin')->addAsPasser($openid);
        }

        if (FALSE === $weixinService->is_register($openid)) {
            return ['data' => session_id(), 'is_login' => 0, 'status' => 0];
        }
        return ['data' => session_id(), 'is_login' => 1, 'status' => 0];
    }


    /*
     * 用户check_login
     *
     */
    public function login() {
        $nickname = I('nickname');
        $gender = intval(I('gender'));
        $city = I('city');
        $province = I('province');
        $country = I('country');
        $avater = I('avatarUrl');
        $openid = session('openid');

        $data = [
            'nickname' => $nickname,
            'gender' => $gender,
            'country' => $country,
            'province' => $province,
            'city' => $city,
            'avatar' => $avater,
            'type' => WeixinModel::$USER_TYPE_REGISTER,

        ];
        $WEIXIN = D('Weixin');
        $weixin_user = $WEIXIN->getByOpenid($openid);
        if ($weixin_user['type']  >= WeixinModel::$USER_TYPE_REGISTER) {
            return ['data' => '', 'is_login' => 1, 'status' => 0];
        }

        $update_result =  $WEIXIN->updateInfo($openid,$data);
        if (FALSE === $update_result) {
            return ['status' => 1, 'data' => '登陆失败'];
        }
        return ['data' => '', 'is_login' => 1, 'status' => 0];
    }

    public function get_user_info() {

        $openid = session('openid');

        $data = [

            'app_id' => 'mPxWOXi4p4',
            'balance' => 0.00,
            'can_use_integral' => 0,
            'company' => NULL,
            'contact_status' => NULL,
            'email' => NULL,
            'from_manual' => 0,
            'group' => 0,
            'integral' => 0,
            'is_deleted' => 0,
            'is_bind' => TRUE,
        ];


        $weixin_info = D('Weixin')->getByOpenid($openid);

        if (empty($weixin_info)) {
            $this->setError('请先完成绑定');
        }

        $weixin_info['id'] = $weixin_info['uid'];
        unset($weixin_info['uid']);

        $default_img = 'http://img.zhichiwangluo.com/zcimgdir/album/file_5ac5774ba3fc4.jpg';
        $weixin_info['cover_thumb'] = $weixin_info['avatar'] ? $weixin_info['avatar'] : $default_img;
        $weixin_info['add_time'] = $weixin_info['gmt_create'];
        $weixin_info['weixin_id'] = $weixin_info['openid'];
        $weixin_info['sex'] = $weixin_info['gender'] ? $weixin_info['gender'] -1 : $weixin_info['gender'];

        $userService = new UserService();

        // todo 判断是否绑定
        if ($weixin_info['id'] == 0) {
            $data = array_merge($weixin_info, $data);
            $data['is_bind'] == FALSE;
            return ['status' => 0, 'data' => $data];
        }

        $user_info = $userService->get_more_info($weixin_info['id']);
        if ($user_info['status'] == BaseService::$ERROR_CODE) {
            return $user_info;
        }

        $weixin_info['phone'] = $user_info['tel'] ? $user_info['tel'] : '';
        unset($user_info['tel']);

        if ($user_info['sex']) {
            $weixin_info['sex_name'] = $user_info['sex'];
            unset($user_info['sex']);
        }



        $data = array_merge($weixin_info, $user_info, $data);
        return ['status' => 0, 'data' => $data];
    }

    public function get_phone_number() {
        $encryptedData = I('encryptedData');
        $iv = I('iv');
        $session_key = session('session_key');
        if (!$encryptedData || !$iv || !$session_key) {
            return ['status' => 1, 'data' => '获取手机号有误，请删除小程序重新授权'];
        }
        $pc = new WXBizDataCrypt(C('APP_ID'), $session_key);
        $errCode = $pc->decryptData($encryptedData, $iv, $data);
        if ($errCode != 0) {
            return ['status' => 1, 'data' => '解密信息错误'];
        }
        $data = json_decode($data, TRUE);
        return ['status' => 0, 'data' => $data['purePhoneNumber']];
    }


}