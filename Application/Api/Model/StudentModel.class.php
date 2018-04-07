<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/1/27
 * Time: 上午12:28
 */

namespace Api\Model;


class StudentModel extends BaseModel {

    //    protected $fields = array('id', 'name', 'school', 'number', 'entran_year', 'tel', 'sex', 'avater', 'status', 'gmt_create', 'gmt_modified', '_type' => array('id' => 'int', 'name' => 'varchar', 'school' => 'varchar', 'number' => 'bigint', 'entran_year' => 'year', 'tel' => 'bigint', 'sex' => 'tinyint', 'avater' => 'varchar', 'status' => 'tinyint', 'gmt_create' => 'bigint', 'gmt_modified' => 'bigint',));


    // protected $_validate = array(array('name', 'require', '姓名不能为空！'), //默认情况下用正则进行验证
    //  array('school', 'require', '学校不能为空'), array('number', 'require', '学号不能为空'), array('number', 'number', '学号必须为数字'), array('entran_year', 'require', '入学年份不能为空'), array('entran_year', 'number', '入学年份必须为数字'), array('tel', 'number', '手机号码不能为空'), array('tel', '/^0{0,1}(13[0-9]|15[7-9]|153|156|18[7-9])[0-9]{8}$/', '手机号码格式不对'), array('avater', 'url', '头像字段必须为url地址', self::EXISTS_VALIDATE), array('sex', array(0, 1, 2), '性别必须选男或女或保密！', self::EXISTS_VALIDATE, 'in'),);

    protected $_auto = array(array('gmt_create', 'time', self::MODEL_INSERT, 'function'), array('gmt_modified', 'gmt_create', self::MODEL_INSERT, 'field'), array('gmt_modified', 'time', self::MODEL_UPDATE, 'function'), array('status', 1, self::MODEL_INSERT),);

    public static $SEX_MALE = 1;
    public static $SEX_FEMALE = 2;
    public static $sex_UN_KNOW = 0;

    public static $SEX_DESC = [0 => '保密', 1 => '男', 2 => '女',];

    public function getById ($id)
    {
        if (!$id) {
            return FALSE;
        }
        $cache_key = 'pingshifen_student_by_id_' . $id;
        $cache_value = S($cache_key);
        if ($cache_value) {
            return json_decode(S($cache_key), TRUE);
        }
        $student_item = $this->find($id);
        if ($student_item) {
            S($cache_key, json_encode($student_item), 3600);
        }
        return $student_item;
    }


}