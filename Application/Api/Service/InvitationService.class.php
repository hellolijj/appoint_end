<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/2/14
 * Time: 上午8:55
 */

namespace Api\Service;


class InvitationService extends BaseService {

    public static $SYSTEM_INVITION_CODE = ['ZJGSU', 'IEEE', '20180205', '20180318'];

    /*
     * 根据邀请码，获取邀请人。如果获取不到，则说明无效的邀请码
     * @return 校验成功 获取 uid or return false
     */
    public function getInvitor ($invitation_code)
    {
        $invitation_code_item = D('InvitationCode')->getByCode($invitation_code);
        if ($invitation_code_item) {
            return $invitation_code_item['uid'];
        } elseif (in_array($invitation_code, self::$SYSTEM_INVITION_CODE)) {
            return BaseService::$SYSTEM_UID;
        } elseif ($invitation_code == date('Ymd', time())) {
            return BaseService::$SYSTEM_UID;
        } else {
            return FALSE;
        }
    }

    /*
     * 添加邀请记录
     */
    public function add ($uid, $invitation_code, $invitor_uid)
    {
        $data = ['uid' => $uid, 'invitation_code' => $invitation_code, 'invitor_uid' => $invitor_uid, 'gmt_create' => time(), 'gmt_modified' => time(),];
        M('invitation')->add($data);
    }

    /*
     * 获取邀请码
     * 如果存在邀请码则 查取，如果不存在则 先添加，再查取
     */
    public function get_invitation_code ($uid)
    {
        if (!$uid) {
            return ['success' => FALSE];
        }
        $InvitationCode = D('InvitationCode');
        $invitation_code_item = $InvitationCode->getByUid($uid);
        if (empty($invitation_code_item)) {
            // 随机生成邀请码 不能重复
            while (TRUE) {
                $invitation_code = mt_rand(100000, 999999);
                $invitation_code_item = $InvitationCode->getByCode($invitation_code);
                if (!$invitation_code_item) {
                    break;
                }
            }
            $data = ['uid' => $uid, 'code' => $invitation_code, 'gmt_create' => time(), 'gmt_modified' => time(),];
            D('InvitationCode')->add($data);
        } else {
            $invitation_code = $invitation_code_item['code'];
        }
        return ['success' => TRUE, 'data' => $invitation_code];
    }
}