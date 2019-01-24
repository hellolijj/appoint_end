<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2019/1/24
 * Time: 下午10:12
 */

namespace Api\Model;

class DocumentModel extends BaseModel {

    public static  $DOC_DESC = [
        'stu_cert' => '学习证明',
        'transcript' => '成绩单',
        'attendance' => '到课率',
        'transfer_letter' => '转学证明',
        'stu_id_book' => '学生证'
    ];

    /**
     * @param $appointid
     * @param $uid
     * @return bool|mixed
     */
    public function getByAppointidAndUid($appointid, $uid) {

        if (!$appointid || !$uid) {
            return FALSE;
        }

        $document_list = $this->where(['appoint_id' => $appointid, 'uid' => $uid])->find();
        return $document_list;
    }

    /**
     * @param $appoint_id_arr array
     * @return array
     */
    public function listByAppoints($appoint_id_arr) {

        // todo to finish the code.
        if (count($appoint_id_arr) == 0) {
            return FALSE;
        }

        $where['appoint_id'] = ['in', implode(',', $appoint_id_arr)];
        $document_list = $this->where($where)->select();

        return $document_list;

    }
}