<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2019/1/24
 * Time: 下午2:29
 */

namespace Admin\Service;

use Api\Model\DocumentModel;
use Couchbase\Document;

class DocsService {

    /*
     * 给records 加上文档列表
     */
    public function add_docs(&$records) {

        $appoint_id_arr = result_to_array($records, 'id');
        $docs_lists = D('Api/Document')->listByAppoints($appoint_id_arr);
        $docs_items = result_to_map($docs_lists, 'appoint_id');

        foreach ($records as &$record) {

            $docs = "";

            if ($docs_items[$record['id']]['stu_cert']) $docs .= DocumentModel::$DOC_DESC['stu_cert'] . ';';
            if ($docs_items[$record['id']]['transcript']) $docs .= DocumentModel::$DOC_DESC['transcript'] . ';';
            if ($docs_items[$record['id']]['attendance']) $docs .= DocumentModel::$DOC_DESC['attendance'] . ';';
            if ($docs_items[$record['id']]['transfer_letter']) $docs .= DocumentModel::$DOC_DESC['transfer_letter'] . ';';
            if ($docs_items[$record['id']]['stu_id_book']) $docs .= DocumentModel::$DOC_DESC['stu_id_book'] . ';';

            if (count($docs) > 0) {
                $docs = substr($docs, 0, strlen($docs)-1);
            }

            $record['docs'] = $docs;
        }

    }


}