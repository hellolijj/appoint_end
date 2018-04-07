<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/3/2
 * Time: 下午7:02
 */

namespace Admin\Controller;

use Admin\Service\QuestionUploadService;
use Think\Controller;
use Think\Upload;


/*
 * 用于处理用户前端上传题目
 */

class QuestionController extends Controller {
    public function index ()
    {
        if (IS_POST) {
            header("Content-Type:text/html;charset=utf-8");
            $upload = new Upload(); // 实例化上传类
            $upload->maxSize = 3145728; // 设置附件上传大小
            //文件真正类型判断可参考http://blog.csdn.net/qq_21386275/article/details/69987371
            $upload->exts = array('xls', 'xlsx'); // 设置附件上传后缀
            $upload->savePath = '/xls/'; // 设置附件上传目录
            // 上传文件
            $info = $upload->uploadOne($_FILES['file']);
            $filename = $info['savepath'] . $info['savename'];
            $exts = $info['ext'];
            if (!$info) {// 上传错误提示错误信息
                $this->ajaxReturn(['success' => FALSE, 'message' => $upload->getError()]);
            }

            $new_filename = C('UPLOAD_DIR') . $filename;

            $data = $this->getExcelData($new_filename, $exts);
            list($title, $fields, $contents) = $data;

            $chapter_arr = [];
            $chapter_arr_count = [];
            $questionUploadService = new QuestionUploadService();

            // 数据库字段映射
            foreach ($fields as $field) {
                if (!in_array($field, array_keys(QuestionUploadService::$OPTION_CONVERT))) {
                    $this->ajaxReturn(['success' => FALSE, 'message' => '字段不对应', 'field' => $field]);
                }
            }

            // 处理各个字段
            foreach ($contents as &$content) {

                // A 字段 set_title
                if (!in_array($content['A'], $chapter_arr)) {
                    $chapter_arr[] = $content['A'];
                    $chapter_arr_count[$content['A']] = 1;
                } else {
                    $chapter_arr_count[$content['A']]++;
                }

                // B 字段 题目类型
                if (!in_array($content['B'], array_keys(QuestionUploadService::$QUESTION_TYPE))) {
                    $this->ajaxReturn(['success' => FALSE, 'message' => '题目类型错误']);
                }
                $content['type'] = QuestionUploadService::$QUESTION_TYPE[$content['B']];
                unset($content['B']);

                // C 字段 题干
                if ($fields['C'] != '题干') {
                    return FALSE;
                    $this->ajaxReturn(['success' => FALSE, 'message' => '题目类型错误']);
                }
                if (!$content['C']) {
                    $this->ajaxReturn(['success' => FALSE, 'message' => '题干不能为空']);
                }
                $content['content'] = $content['C'];
                unset($content['C']);

                // D E F G 字段 题干
                $content['option_a'] = $content['D'];
                unset($content['D']);
                $content['option_b'] = $content['E'];
                unset($content['E']);
                $content['option_c'] = $content['F'];
                unset($content['F']);
                $content['option_d'] = $content['G'];
                unset($content['G']);
                // $option 可以为空
                if (is_null($content['option_a'])) {
                    $content['option_a'] = '';
                }
                if (is_null($content['option_b'])) {
                    $content['option_b'] = '';
                }
                if (is_null($content['option_c'])) {
                    $content['option_c'] = '';
                }
                if (is_null($content['option_d'])) {
                    $content['option_d'] = '';
                }

                // H 正确答案
                if (!$content['H']) {

                    $this->ajaxReturn(['success' => FALSE, 'message' => '正确答案不能为空']);
                }
                $content['answer'] = $questionUploadService->option_convert(trim($content['H']));
                unset($content['H']);


                $content['analysis'] = $content['I'];
                if (is_null($content['analysis'])) {
                    $content['analysis'] = '';
                }
                unset($content['I']);
            }

            // 添加set chapter 处理chapter set time
            $set_id = $questionUploadService->add_set($title, 145, count($contents));
            if (!$set_id) {
                die('添加set_id失败');
            }
            $chapter_arr_id = [];
            foreach ($chapter_arr as &$chapter) {
                $chapter_id = $questionUploadService->add_chapter($set_id, $chapter, $chapter_arr_count[$chapter]);
                if (!$chapter_id) {
                    die('添加题目章节失败');
                }
                $chapter_arr_id[$chapter] = $chapter_id;
            }


            foreach ($contents as &$content) {
                $content['gmt_create'] = time();
                $content['gmt_modified'] = time();
                $content['set_id'] = $set_id;
                $content['chapter_id'] = $chapter_arr_id[$content['A']];
                unset($content['A']);
                $add = M('question_bank')->add($content);
                if (!$add) {
                    $this->ajaxReturn(['success' => FALSE, 'message' => '添加失败', 'str' => json_decode($content)]);
                    die;
                }
            }
            $this->ajaxReturn(['success' => TRUE, 'message' => '添加成功']);
        } else {
            $this->display();
        }
    }


    //excel
    protected function getExcelData ($filename, $exts)
    {
        //导入PHPExcel类库
        import("Org.Util.PHPExcel");
        //不同类型的文件导入不同的类
        if ($exts == 'xls') {
            import("Org.Util.PHPExcel.Reader.Excel5");
            $PHPReader = new \PHPExcel_Reader_Excel5();
        } elseif ($exts == 'xlsx') {
            import("Org.Util.PHPExcel.Reader.Excel2007");
            $PHPReader = new \PHPExcel_Reader_Excel2007();
        }

        //载入文件
        $PHPExcel = $PHPReader->load($filename);
        //获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
        $currentSheet = $PHPExcel->getSheet(0);

        //获取总列数
        $allColumn = $currentSheet->getHighestColumn();
        //获取总行数
        $allRow = $currentSheet->getHighestRow();

        //循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始

        $contents = [];
        $title = $currentSheet->getTitle();
        $fields = [];
        for ($currentRow = 1; $currentRow <= $allRow; $currentRow++) {
            //从哪列开始，A表示第一列
            for ($currentColumn = 'A'; $currentColumn <= $allColumn; $currentColumn++) {
                //数据坐标
                $address = $currentColumn . $currentRow;
                //读取到的数据，保存到数组$arr中
                $cell = $currentSheet->getCell($address)->getValue();
                if ($cell instanceof \PHPExcel_RichText) {
                    $contents[$currentRow][$currentColumn] = $cell->__toString();
                } else {
                    $contents[$currentRow][$currentColumn] = $cell;
                }

            }
            // 第一行内容复制给field
            if ($currentRow == 1) {
                $fields = $contents[1];
                unset($contents[1]);
            }
        }

        return [$title, $fields, $contents];
    }


}