<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/2/26
 * Time: 上午11:58
 */

namespace Api\Service;

class TempMsgService extends BaseService {

    protected $appid;
    protected $secrect;
    protected $accessToken;

    function __construct ()
    {
        $this->appid = C('APP_ID');
        $this->secrect = C('APP_SECRET');
        $this->accessToken = $this->getToken($this->appid, $this->secrect);
    }

    /**
     * 发送自定义的模板消息
     * @param        $touser
     * @param        $template_id
     * @param        $url
     * @param        $data
     * @param string $topcolor
     * @return bool
     */
    public function doSend ($touser, $template_id, $form_id, $data, $page = '', $topcolor = '', $emphasis_keyword = '')
    {
        $template = array('touser' => $touser, 'template_id' => $template_id, 'page' => $page, 'form_id' => $form_id, 'data' => $data, 'topcolor' => $topcolor, 'emphasis_keyword' => $emphasis_keyword,);
        $json_template = json_encode($template);
        $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=" . $this->accessToken;
        $dataRes = request_post($url, urldecode($json_template));
        if ($dataRes['errcode'] == 0) {
            return ['success' => TRUE];
        } else {
            return ['success' => FALSE, 'message' => $dataRes['errmsg']];
        }
    }

    protected function getToken ($appid, $appsecret)
    {
        if (S($appid)) {
            $access_token = S($appid);
        } else {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $appid . "&secret=" . $appsecret;
            $token = request_get($url);
            $token = json_decode(stripslashes($token));
            $arr = json_decode(json_encode($token), TRUE);
            $access_token = $arr['access_token'];
            S($appid, $access_token, 7000);
        }
        return $access_token;
    }

}