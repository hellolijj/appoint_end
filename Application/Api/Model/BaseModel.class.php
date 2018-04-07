<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/1/27
 * Time: 上午12:07
 */

namespace Api\Model;

use Think\Model;

class BaseModel extends Model {

    /**
     * 自动验证规则
     */
    protected $_validate = array();

    /**
     * 自动完成规则
     */
    protected $_auto = array();

    /**
     * 构造函数，用于这是Logic层的表前缀
     * @param string $name 模型名称
     * @param string $tablePrefix 表前缀
     * @param mixed  $connection 数据库连接信息
     */
    public function __construct ($name = '', $tablePrefix = '', $connection = '')
    {
        /* 执行构造方法 */
        parent::__construct($name, $tablePrefix, $connection);
    }

    public function getById ($id, $field = '*')
    {
        if (empty($id) || !is_numeric($id)) {
            return FALSE;
        }
        $id = intval($id);

        return $this->field($field)->where(array('id' => $id))->find();
    }

    public function getByIds (array $id_arr, $field = '*')
    {
        if (empty($id_arr) || !is_array($id_arr) || check_num_ids($id_arr)) {
            return NULL;
        }

        if (count($id_arr) === 1) {
            $id = intval(current($id_arr));
            $result = $this->getById($id, $field);
        } else {
            $ids = implode(',', $id_arr);
            $result = $this->field($field)->where(array('id' => array('IN', $ids)))->select();
        }

        return $result;
    }

    public function listById ($id, $page = 1, $page_size = 20)
    {
        if (empty($id) || !is_numeric($id)) {
            return FALSE;
        }
        $id = intval($id);

        return $this->where(array('id' => $id))->page($page)->limit($page_size)->select();
    }

    /*public function listByIds ($id_arr, $field = '*')
    {
        if (empty($id_arr) || !is_array($id_arr) || check_num_ids($id_arr)) {
            return NULL;
        }

        if (count($id_arr) === 1) {
            $id = intval(current($id_arr));
            $result = $this->listById($id, $field);
        } else {
            $ids = implode(',', $id_arr);
            $result = $this->field($field)->where(array('id' => array('IN' => $ids)))->select();
        }

        return $result;
    }*/

    public function listByPageWhere ($where, $page_num, $page_size = 20, $order_by = 'id DESC')
    {

        return $this->where($where)->order($order_by)->limit($page_size)->page($page_num)->select();

    }

    public function countByWhere (array $where)
    {
        $count = $this->where($where)->count();

        if (!$count) {
            return 0;
        } else {
            return (int)$count;
        }
    }

    final protected function getCompleteTableName ()
    {
        $table_name = $this->getTableName();
        if (empty($table_name)) {
            throw new \Exception('table name can not be empty!');
        }
        return '`' . $table_name . '`';
    }


}