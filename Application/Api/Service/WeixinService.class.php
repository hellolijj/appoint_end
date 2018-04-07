<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/1/28
 * Time: 上午10:37
 */

namespace Api\Service;

use Api\Model\WeixinModel;

class WeixinService{


    /*
     * 判断用户是否存在打开过，是否
     */
    public function is_register($openid) {
        if (!$openid) {
            return FALSE;
        }

        $weixin_user = D('Weixin')->getByOpenid($openid);
        if (empty($weixin_user)) {
            return FALSE;
        }
        $user_type = $weixin_user['type'];
        if ($user_type <= WeixinModel::$USER_TYPE_PASSER) {
            return FALSE;
        }
        return TRUE;
    }

    /**
     * @param $openid 用户openid
     * @return true false
     * 判断openid用户是否是路人
     */
    public function is_passer($openid) {
        if (!$openid) {
            return ['status' => 1, 'data' => '参数错误'];
        }

        $weixin_user = D('Weixin')->getByOpenid($openid);
        if (empty($weixin_user)) {
            return FALSE;
        }
        return TRUE;
    }




}