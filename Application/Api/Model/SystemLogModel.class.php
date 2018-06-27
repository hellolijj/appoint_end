<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/6/24
 * Time: 下午9:46
 */


namespace Api\Model;

class SystemLogModel extends BaseModel {


    public function addLog($uid, $operate, $remark = '') {

        if (!$uid || !$operate) {
            return FALSE;
        }

        $data = [
            'uid' => $uid,
            'operate' => $operate,
            'remark' => $remark,
            'status' => 1,
            'gmt_create' => time(),
            'gmt_modified' => time(),
        ];

        $this->add($data);

    }

}

