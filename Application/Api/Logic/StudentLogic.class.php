<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/1/27
 * Time: 下午4:05
 */

namespace Api\Logic;


use Api\Service\StudentService;
use Api\Service\WeixinService;

class StudentLogic extends BaseLogic {

    public $OP_TYPE = ['get', 'set'];

    public function __construct ()
    {
    }

    /*
     * 学生注册绑定
     */
    public function bind ()
    {
        $name = I('name');
        $tel = intval(I('tel'));
        $school = I('school');
        $user_type = I('user_type');
        $number = intval(I('num'));
        $enter_year = intval(I('enter_year'));

        // 参数校验
        if ($user_type !== 'student') {
            $this->setError('用户类型错误');
        }
        if (!$name || $tel < 0 || !$school || $number < 0 || !$enter_year) {
            $this->setError('参数不能为空');
        }

        // 添加头像url 性别等参数 todo 使用crul抓取图像存到本地服务器
        $weixinService = new WeixinService();
        $weixin_user_result = $weixinService->getByOpenid(session('openid'));
        if (FALSE === $weixin_user_result) {
            $this->setError($weixin_user_result['message']);
        }
        $weixin_user = $weixin_user_result['data'];
        $head_img = $weixin_user['avatar'];
        $sex = $weixin_user['gender'];
        $studentService = new StudentService();
        $result = $studentService->bind($name, $tel, $school, $number, $enter_year, $head_img, $sex);
        if (TRUE === $result) {
            return $this->setSuccess([], '绑定成功');
        } else {
            return $this->setError($result['message']);
        }
    }

    public function info ()
    {
        $uid = session('uid');
        if (!$uid) {
            $this->setError('无效的openid');
        }
        $info = D('Student')->getById($uid);
        return $this->setSuccess($info, '学生用户信息');
    }

    /*
     * 默认update name
     */
    public function name ()
    {
        $name = I('name');
        if (!$name) {
            return $this->setError('不能设置为空');
        }
        $uid = intval(session('uid'));
        $Student = D('Student');
        $data = ['id' => $uid, 'name' => $name,];
        if (!$Student->save($data)) {
            return $this->setError(NULL, $Student->getError());
        } else {
            return $this->setSuccess(NULL, '修改成功');
        }
    }

    /*
     * set number
     */
    public function numberSet ()
    {
        $number = intval(I('number'));
        if ($number <= 0) {
            return $this->setError('number参数错误');
        }
        $uid = intval(session('uid'));
        $Student = D('Student');
        $data = ['id' => $uid, 'number' => $number,];
        if (!$Student->save($data)) {
            return $this->setError(NULL, $Student->getError());
        } else {
            return $this->setSuccess(NULL, '修改成功');
        }
    }

    /*
     * set number
     */
    public function sexSet ()
    {
        $sex = I('sex');

        $uid = intval(session('uid'));
        $Student = D('Student');
        $data = ['id' => $uid, 'sex' => 2,  //todo 使用model中文映射
        ];
        if (!$Student->save($data)) {
            return $this->setError(NULL, $Student->getError());
        } else {
            return $this->setSuccess(NULL, '修改成功');
        }
    }

}