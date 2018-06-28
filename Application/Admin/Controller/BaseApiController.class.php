<?php
/**
 * Created by PhpStorm.
 * User: lijunjun
 * Date: 2018/4/19
 * Time: 下午9:25
 */

namespace Admin\Controller;


class BaseApiController extends BaseController {


    /**
     * 页面标题
     * @var string
     */
    public $title;

    /**
     * 默认操作数据表
     * @var string
     */
    public $id = 0;
    public $type = '';
    public $table;
    public $sort;
    public $order;
    public $list_relation = false;
    public $list_together = false;
    public $list_where = [];

    protected function _initialize(){

        $this->type = trim(I('type'));
        $this->assign('type',$this->type);
        $this->id = intval(I('id'));
        $this->assign('id',$this->id);

        if (empty(session('user'))) {
            $this->redirect('Login/login');
        }
    }

    /**
     * 表单默认操作
     * @param Query $dbQuery 数据库查询对象
     * @param string $tplFile 显示模板名字
     * @param string $pkField 更新主键规则
     * @param array $where 查询规则
     * @param array $extendData 扩展数据
     * @return array|string
     */
    protected function _form($dbQuery = null, $tplFile = '', $pkField = '', $where = [], $extendData = [])    {
        $db = is_null($dbQuery) ?  D($this->table) : (is_string($dbQuery) ? D($dbQuery) : $dbQuery);

        $pk = empty($pkField) ? ($db->getPk() ? $db->getPk() : 'id') : $pkField;

        $pkValue = I($pk, isset($where[$pk]) ? $where[$pk] : (isset($extendData[$pk]) ? $extendData[$pk] : null));

        // 非POST请求, 获取数据并显示表单页面
        if (!IS_POST) {

             $this->list_relation && $db->relation($this->list_relation);
             $vo = ($pkValue !== null) ? array_merge((array)$db->where($pk, $pkValue)->where($where)->find(), $extendData) : $extendData;
//            if (false !== $this->_callback('_before', $vo)) {
//                empty($this->title) || $this->assign('title', $this->title);
//                return $this->fetch($tplFile, ['info' => $vo]);
//            }
//            return $vo;

            $view = $this->fetch('form', ['info' => $vo]);

            return $view;
        }
        // POST请求, 数据自动存库
        $data = array_merge(I(), $extendData);
        var_dump($data);die;

        if (false !== $this->_callback('_before', $data)) {
//            $result = DataService::save($db, $data, $pk, $where);
//            if (false !== $this->_callback('_after', $result)) {
//                if ($result !== false) {
//                    $this->success('恭喜, 数据保存成功!', '');
//                }
//                $this->error('数据保存失败, 请稍候再试!');
//            }
        }
    }

    /**
     * 列表集成处理方法
     * @param Query $dbQuery 数据库查询对象
     * @param bool $isPage 是启用分页
     * @param bool $isDisplay 是否直接输出显示
     * @param bool $total 总记录数
     * @param array $result
     * @return array|string
     */
    protected function _list($dbQuery = null, $isPage = true, $isDisplay = true, $total = false, $result = [])    {

        $db = is_null($dbQuery) ?  model($this->table) : (is_string($dbQuery) ? model($dbQuery) : $dbQuery);
        $mod_pk = $db->getPk();

        if ($this->request->get("sort",'','trim')) {
            $sort = $this->request->get("sort",'', 'trim');
        } else if (!empty($sort_by)) {
            $sort = $sort_by;
        } else if ($this->sort) {
            $sort = $this->sort;
        } else {
            $sort = $mod_pk;
        }
        if ($this->request->get("order",'', 'trim')) {
            $order = $this->request->get("order",'', 'trim');
        } else if (!empty($order_by)) {
            $order = $order_by;
        } else if ($this->order) {
            $order = $this->order;
        } else {
            $order = 'DESC';
        }
        if(in_array($sort,$db->getTableFields())){
            $db->order($sort.' '.$order);
        }

        // 关联查询
        $this->list_relation && $db->relation($this->list_relation);
        if($map = $this->_search()){
            $this->list_where = array_merge($map, $this->list_where);
        }

        $db->where($this->list_where);
        if ($isPage) {
            $rows = intval($this->request->get('rows', cookie('rows')));
            cookie('rows', $rows >= 10 ? $rows : 20);
            $page = $db->paginate($rows, $total, ['query' => $this->request->get('', '', 'urlencode')]);

            // p( $db->getLastSql());
            list($pattern, $replacement) = [
                ['|href="(.*?)"|', '|pagination|'],
                ['data-open="$1"  href="javascript:void(0);"', 'pagination pull-right']
            ];
            list($result['list'], $result['page']) = [$page->all(), preg_replace($pattern, $replacement, $page->render())];
        } else {
            $result['list'] = $db->select()->toArray();
        }

        if (false !== $this->_callback('_after_list', $result['list']) && $isDisplay) {
            !empty($this->title) && $this->assign('title', $this->title);
            return $this->fetch('', $result);
        }
        return $result;
    }

    /**
     * 获取请求参数生成条件数组
     */
    protected function _search($dbQuery = null) {
        //生成查询条件
        $mod = is_null($dbQuery) ?  model($this->table) : (is_string($dbQuery) ? model($dbQuery) : $dbQuery);
        $map = $search = array();
        $data = [];
        foreach($this->request->param() as $k => $v){
            if(substr($k, 0, 1) == '_' && $v!=''){
                $data[ substr($k,1) ] = ['rule'=>'like','value' => $v];
            }else if( $v != '' ){
                $data[$k] = $v;
            }
        }
        $this->list_relation && $mod->relation($this->list_relation);

        $tableFields = array_intersect($mod->getTableFields(),array_keys($data));
        foreach ($tableFields as $key => $val) {
            if (substr($key, 0, 1) == '_') {
                continue;
            }
            if ($data[$val] != '') {
                if(is_array($data[$val])){
                    $map[$val] = array($data[$val]['rule'],"%{$data[$val]['value']}%");
                    $search[$val] = $data[$val]['value'];
                }else{
                    $map[$val] = $data[$val];
                    $search[$val] = $data[$val];
                }
                $search[$val] = $data[$val];
            }
        }

        $this->assign('search', $search);
        $this->_callback('_after_search', $map);

        return $map;
    }
    /**
     * 当前对象回调成员方法
     * @param string $method
     * @param array|bool $data
     * @return bool
     */
    protected function _callback($method, &$data) {
        foreach ([$method, "{$method}_" . $this->request->action()] as $_method) {
            if (method_exists($this, $_method) && false === $this->$_method($data)) {
                return false;
            }
        }
        return true;
    }

    //异步 修改字段
    protected function _ajax_edit($dbQuery){
        $db = is_null($dbQuery) ?  model($this->table) : (is_string($dbQuery) ? model($dbQuery) : $dbQuery);
        $data =$this->request->post();
        if(isset($data) && is_array($data) && !empty($data) && $data['id'] && $data['field']){
            $pk = $db->getPk();
            $db->where([$pk => $data['id']])->setField($data['field'],$data['val']);
        }
        return json(['code' => 1 ,'msg' => '操作成功']);
    }

    /**
     * 删除
     */
    protected function _delete($dbQuery){
        //$db = Db::name($this->table);
        $db = is_null($dbQuery) ?  model($this->table) : (is_string($dbQuery) ? model($dbQuery) : $dbQuery);
        $pk = $db->getPk();
        $data =$this->request->post();
        $ids = explode(',', $data['id']);
        $where[empty($pk) ? 'id' : $pk] = ['in', $ids];
        if(false !== $db->where($where)->delete()){
            return json(['code' => 1 ,'msg' => '操作成功']);
        }else{
            return json(['code' => 0 ,'msg' => '操作失败']);
        }
    }

    /***
     * 公共上传方法
     */
    protected function _upload(){
        $up = new UpService();
        if($up->type == 'ueditor' && $up->action == 'config'){
            return $up->ueditor_config();
        }else if($up->type == 'ueditor'){
            return $up->ueditor_upload();
        }else{
            return $up->upload();
        }

    }



}