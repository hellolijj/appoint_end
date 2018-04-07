<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/1/27
 * Time: 上午11:45
 */

namespace Api\Model;
class TeacherModel extends BaseModel {

    protected $fields = array('id', 'name', 'school', 'tel', 'avater', 'sex', 'status', 'gmt_create', 'gmt_modified', '_type' => array('id' => 'int', 'name' => 'varchar', 'school' => 'varchar', 'tel' => 'bigint', 'sex' => 'tinyint', 'avater' => 'varchar', 'status' => 'tinyint', 'gmt_create' => 'bigint', 'gmt_modified' => 'bigint',));

    protected $_validate = array(array('name', 'require', '姓名不能为空！'), //默认情况下用正则进行验证
        array('school', 'require', '学校不能为空'), array('tel', 'number', '手机号码不能为空'), array('tel', '/^0{0,1}(13[0-9]|15[7-9]|153|156|18[7-9])[0-9]{8}$/', '手机号码格式不对'), array('avater', 'url', '头像字段必须为url地址', self::EXISTS_VALIDATE), array('sex', array(0, 1, 2), '性别只能选 0, 1, 2！', self::EXISTS_VALIDATE, 'in'),);

    protected $_auto = array(array('gmt_create', 'time', self::MODEL_INSERT, 'function'), array('gmt_modified', 'gmt_create', self::MODEL_INSERT, 'field'), array('gmt_modified', 'time', self::MODEL_UPDATE, 'function'), array('status', 1, self::MODEL_INSERT),);

    public function getByIds ($tids)
    {
        if (!check_num_ids($tids)) {
            return ['success' => FALSE, 'message' => 'tids参数错误'];
        }
        $where['id'] = ['in', implode(',', $tids)];
        $teachers = $this->where($where)->select();
        return $teachers;
    }

    public function getById ($id, $field = '*')
    {
        if (!$id) {
            return FALSE;
        }
        $cache_key = 'pingshifen_teacher_by_id_' . $id;
        $cache_value = S($cache_key);
        if ($cache_value) {
            return json_decode(S($cache_key), TRUE);
        }

        $teacher_item = $this->find($id);
        if ($teacher_item) {
            S($cache_key, json_encode($teacher_item), 3600);
        }
        return $teacher_item;
    }


}