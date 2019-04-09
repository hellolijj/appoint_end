<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/4/21
 * Time: 上午11:27
 */

namespace Admin\Controller;

use Api\Model\UserModel;
use Api\Service\UserService;
use Think\Page;
use Think\Upload;

class UserController extends BaseApiController {

    /**
     * 查看系统所有的用户
     */
    public function all() {
        $this->assign('title', '注册绑定用户');


        // 处理where搜索条件
        $phone = I('phone');
        $passport = I('passport');
        $date = I('date');
        $where = [];
        if ($phone) {
            $where['tel'] = $phone;
        }
        if ($passport) {
            $where['passport'] = ['like', '%' . $passport . '%'];
        }
        if ($date) {
            $date_arr = explode('-', $date);
            $start = strtotime($date_arr[0]);
            $end = strtotime($date_arr[1]);
            $where['gmt_create'] = [['gt', $start], ['lt', $end]];
        }


        $p = $_GET['p'];
        $users = D('Api/User')->listByPageWhere($where, $p, 10, 'id desc');

        $users_uid_arr = result_to_array($users, 'id');
        $user_service = new UserService();
        $users_info_list = $user_service->list_more_info_by_uids($users_uid_arr);


        $this->assign('list',$users_info_list);// 赋值数据集
        $count      = D('Api/User')->countByWhere($where);// 查询满足要求的总记录数
        $Page       = new Page($count, 10);
        $page       = $Page->show();// 分页显示输出
        $this->assign('page_content',$page);// 赋值分页输出

        $this->display();
    }

    public function t() {
        $a = S();
        var_dump($a);
    }


    /**
     * 这是所有的储存用户，信息存与user_back表中
     */
    public function back() {
        $this->assign('title', '所有用户管理');

        // 处理where搜索条件
        $name = I('name');
        $passport = I('passport');
        $number = I('number');
        $where = [];
        if ($name) {
            $where['name'] = $name;
        }
        if ($passport) {
            $where['passport'] = $passport;
        }
        if ($number) {
            $where['number'] = $number;
        }

        $p = $_GET['p'];
        $user_backs = D('Api/UserBack')->listByPageWhere($where, $p, 20, 'id desc');

        $this->assign('list',$user_backs);// 赋值数据集
        $count      = D('Api/UserBack')->countByWhere($where);// 查询满足要求的总记录数
        $Page       = new Page($count, 20);
        $page       = $Page->show();// 分页显示输出

        $this->assign('page_content',$page);// 赋值分页输出

        $this->display(); // 输出模板
    }

    public function edit() {
        $id = intval(I('id'));

        // 非POST请求, 获取数据并显示表单页面
        if (!IS_POST) {
            $info = D('Api/UserBack')->where(['id'=>$id])->find();
            $this->assign('info', $info);

            $view = $this->fetch('form');
            $this->ajaxReturn($view);
        }


        $data = I();
        unset($data['id']);

        if (count($data) != 0) {
            M('User_back')->where(['id' => $id])->save($data);
        }

        return $this->success('操作成功');
    }


    /**
     * 处理删除业务
     */
    public function user_del() {

        $id = intval(I('id'));
        if (!IS_POST || !$id) {

            return $this->error('你访问的页面不存在');
        }

        $user_service = new UserService();
        $user_service->del($id);

        $this->success('操作成功');
    }

    /**
     * 导入新用户
     */
    public function load() {
        $this->assign('title', '导入用户');
        $this->display();
    }

    /**
     * 将上传的excel文件保存起来
     */
    public function upload() {
        if (!IS_POST) {
            return $this->error('你访问的页面不存在');
        } else {
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
                return $this->ajaxReturn(['success' => FALSE, 'message' => $upload->getError()]);
            } else {
                $this->read_excel(C('UPLOAD_DIR') . $filename, $exts);
            }
        }
    }

    /**
     * 读取保存的excel文件，写日志，并且将信息导入user库
     */
    protected function read_excel($file_name, $exts) {
//        echo $file_name . $exts;
        if (!$file_name || !$exts) {
            return FALSE;
        }

        $excel_data = $this->getExcelData($file_name, $exts);
        list($title, $fields, $contents) = $excel_data;

        // 检查字段
        foreach ($fields as $field) {
            if (!in_array($field, UserModel::$filed)) {
                $this->error('字段不对应');
            }
        }

        $OLD_USER = M('user_back');

        foreach ($contents as &$content) {
            // A 字段
            unset($content['A']);

            // B 字段 passport
            if (!$content['B']) {
                $this->error('题干不能为空');
            }
            if (is_null($content['B'])) $content[$fields['B']] = ''; else $content[$fields['B']] = strtoupper(trim($content['B'])); unset($content['B']);
            if (is_null($content['C'])) $content[$fields['C']] = ''; else $content[$fields['C']] = strtoupper(trim($content['C'])); unset($content['C']);
            if (is_null($content['D'])) $content[$fields['D']] = ''; else $content[$fields['D']] = strtoupper(trim($content['D'])); unset($content['D']);
            if (is_null($content['E'])) $content[$fields['E']] = ''; else $content[$fields['E']] = strtoupper(trim($content['E'])); unset($content['E']);
            if (is_null($content['F'])) $content[$fields['F']] = ''; else $content[$fields['F']] = strtoupper(trim($content['F'])); unset($content['F']);
            if (is_null($content['G'])) $content[$fields['G']] = ''; else $content[$fields['G']] = strtoupper(trim($content['G'])); unset($content['G']);
            if (is_null($content['H'])) $content[$fields['H']] = ''; else $content[$fields['H']] = strtoupper(trim($content['H'])); unset($content['H']);
            if (is_null($content['I'])) $content[$fields['I']] = ''; else $content[$fields['I']] = strtoupper(trim($content['I'])); unset($content['I']);
            if (is_null($content['J'])) $content[$fields['J']] = ''; else $content[$fields['J']] = strtoupper(trim($content['J'])); unset($content['J']);
            if (is_null($content['K'])) $content[$fields['K']] = 0; else $content[$fields['K']] = strtoupper(trim($content['K'])); unset($content['K']);
            if (is_null($content['L'])) $content[$fields['L']] = 0; else $content[$fields['L']] = strtoupper(trim($content['L'])); unset($content['L']);

            // 加入数据库

            if ($OLD_USER->where(['passport' => $content['passport']])->find()) {
                continue;
            }
            $OLD_USER->add($content);
        }

        return $this->success('导入成功');

    }

    //excel
    protected function getExcelData ($filename, $exts) {
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