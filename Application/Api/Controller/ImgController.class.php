<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/2/16
 * Time: 下午4:22
 */

namespace Api\Controller;

use Think\Controller;
use Think\Upload;

/*
 * 处理图片服务
 */

class ImgController extends Controller {

    /*
     * upload 图片上传接口
     * 返回图片的url
     */
    public function uploadOne ()
    {
        $upload = new Upload();// 实例化上传类
        $upload->maxSize = 3145728;// 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath = C('UPLOAD_DIR'); // 设置附件上传根目录
        // 上传文件
        $info = $upload->upload();
        if (!$info) {
            $data["message"] = $upload->getError();
            $data['success'] = FALSE;
        } else {
            $data['success'] = TRUE;
            $img_path = $info['file']['savepath'] . $info['file']['savename'];
            $data['data'] = C('UPLOAD_ROOT') . $img_path;
        }
        $this->ajaxReturn($data, 'json');
    }

}