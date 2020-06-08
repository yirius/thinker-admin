<?php


namespace Yirius\Admin\extend;


use think\facade\Cache;
use think\Model;
use Yirius\Admin\config\ConsConfig;
use Yirius\Admin\extend\model\Lists;
use Yirius\Admin\ThinkerAdmin;
use Yirius\Admin\utils\JsonUtil;

class ThinkerRestful extends ThinkerController
{
    /**
     * @var ThinkerModel
     */
    protected $_UseTable = null;

    /**
     * @title      getUseTable
     * @description
     * @createtime 2020/5/27 10:57 下午
     * @return Model
     * @author     yangyuance
     */
    public function getUseTable() {
        return new $this->_UseTable();
    }

    protected $_Where = [];

    protected $_Alias = null;

    protected $_Field = "*";

    protected $_With = null;

    /**
     * @var callable
     */
    protected $eachClosure = null;

    /**
     * @var callable
     */
    protected $parseQuery = null;

    /**
     * @title      index
     * @description GET列表
     * @createtime 2020/5/27 11:02 下午
     * @author     yangyuance
     */
    public function index()
    {
        try {
            $result = (new Lists($this->getUseTable()))
                ->setWhere($this->_Where)
                ->setAlias($this->_Alias)
                ->setWith($this->_With)
                ->setFields($this->_Field)
                ->setEachClosure($this->eachClosure)
                ->setParseQuery($this->parseQuery)
                ->getResult();

            ThinkerAdmin::response()->lists($result)->success();
        }catch (\Exception $exception){
            thinker_error($exception);
            ThinkerAdmin::response()->msg($exception->getMessage())->fail();
        }
    }

    protected $_SavedMsg = "保存信息成功";

    protected $_EditedMsg = "编辑信息成功";

    protected $_Validate = [];

    public function save($entity = [])
    {
        //如果不存在实体，需要去查找
        if(empty($entity)) {
            $entity = $this->fillEntity(input("param."));
        }

        if(empty($entity)) {
            ThinkerAdmin::response()->msg("无数据提交，无法保存或修改")->fail();
        }

        //前置执行
        $saveEntity = $this->_beforeSave($entity);
        if(!empty($saveEntity)) $entity = $saveEntity;

        $isUpdate = [];
        if(!empty($entity[ConsConfig::$JWT_KEY])) {
            $isUpdate[ConsConfig::$JWT_KEY] = $entity[ConsConfig::$JWT_KEY];
        }

        $model = $this->getUseTable();
        if($model->save($entity, $isUpdate)) {
            $this->_afterSave($model->getData(), $isUpdate);

            ThinkerAdmin::response()
                ->msg(empty($isUpdate) ? $this->_SavedMsg : $this->_EditedMsg)
                ->success();
        }

        ThinkerAdmin::response()->msg("未知错误，保存数据失败，请您重试")->fail();
    }

    protected function _beforeSave(array &$entity) {
        return $entity;
    }

    protected function _afterSave(array $entity, $isUpdate = []) {

    }

    /**
     * @title      read
     * @description 获取数据
     * @createtime 2020/5/27 11:18 下午
     * @param $id
     * @author     yangyuance
     */
    public function read($id)
    {
        $entity = $this->getUseTable()->get(['id' => intval($id)])->toArray();

        if(!empty($entity)) {
            $entity = $this->_beforeRead($entity);
        }

        ThinkerAdmin::response()->data($entity)->success();
    }

    protected function _beforeRead(array $entity) {
        return $entity;
    }

    /** 相关JSON字段的读取和添加
     * @var array
     */
    protected $jsonObjectFields = [];

    public function json($id, $field) {
        $fieldData = $this->getUseTable()->get(intval($id))->getData($field);
        if(isset($this->jsonObjectFields[$field])) {
            $retult = JsonUtil::fieldToObject($fieldData);
        } else {
            $retult = JsonUtil::fieldToArray($fieldData);
        }

        ThinkerAdmin::response()->data($retult)->success();
    }

    protected $_CanEditFields = ['status'];

    /**
     * @title      update
     * @description
     * @createtime 2020/5/27 11:27 下午
     * @param        $id
     * @param null   $__type
     * @param string $field
     * @param string $value
     * @author     yangyuance
     */
    public function update($id, $__type = null, $field = "", $value = "")
    {
        if(!empty($__type) && $__type == "field") {
            if(in_array($field, $this->_CanEditFields)){
                $entity = $this->getUseTable()->get(['id' => intval($id)])->toArray();
                $entity[addslashes($field)] = addslashes($value);

                $entity = $this->_beforeUpdate($entity);

                if($this->getUseTable()->save($entity, ['id' => intval($id)])) {
                    $this->_afterUpdate($entity);

                    ThinkerAdmin::response()->msg($this->_EditedMsg)->success();
                }

                ThinkerAdmin::response()->msg("未知错误，更新失败")->fail();
            } else {
                ThinkerAdmin::response()->msg("当前数据不可更新")->fail();
            }
        } else {
            $entity = $this->fillEntity(input("param."));

            unset($entity['id']);
            if(empty($entity)) {
                ThinkerAdmin::response()->msg("无数据提交，无法保存或修改")->fail();
            }
            $entity['id'] = intval($id);

            $this->save($entity);
        }
    }

    protected function _beforeUpdate(array $entity)
    {
        return $entity;
    }

    /**
     * @title      _afterUpdate
     * @description
     * @createtime 2020/5/27 11:25 下午
     * @param array $entity
     * @author     yangyuance
     */
    protected function _afterUpdate(array $entity)
    {

    }

    protected $_NotDelete = [];

    public function delete($id)
    {
        $this->verifyPassword(input('param.password'));

        $id = intval($id);

        if(empty($id) || in_array($id, $this->_NotDelete)) {
            ThinkerAdmin::response()->msg("无法删除当前数据")->fail();
        }

        $this->__defaultDelete([$id]);
    }

    /**
     * @title      deleteall
     * @description
     * @createtime 2020/5/27 11:32 下午
     * @param string $data
     * @author     yangyuance
     */
    public function deleteall($data = "")
    {
        $this->verifyPassword(input('param.password'));

        if(empty($data)) {
            ThinkerAdmin::response()->msg("暂未提交删除数据")->fail();
        }

        $prevdelData = json_decode($data, true);
        $delData = [];
        foreach($prevdelData as $i => $v){
            $delData[] = $v[ConsConfig::$JWT_KEY];
        }

        $this->__defaultDelete($delData);
    }

    protected function __defaultDelete(array $delIds) {
        $delIds = $this->_beforeDelete($delIds);

        if(empty($delIds)) {
            ThinkerAdmin::response()->msg("当前存在不可删除数据")->fail();
        }

        $tList = $this->getUseTable()->whereIn("id", $delIds)->select()->toArray();

        if($this->getUseTable()->whereIn("id", $delIds)->delete()){
            $this->_afterDelete($tList);

            ThinkerAdmin::response()->msg(lang("delete success"))->success();
        }

        ThinkerAdmin::response()->msg(lang("not delete all"))->fail();
    }

    /**
     * @title      _beforeDelete
     * @description
     * @createtime 2020/5/27 11:33 下午
     * @param array $ids
     * @return array
     * @author     yangyuance
     */
    protected function _beforeDelete(array $ids)
    {
        return $ids;
    }

    /**
     * @title      _afterDelete
     * @description
     * @createtime 2019/11/16 8:56 下午
     * @param array $deledArr
     * @author     yangyuance
     */
    protected function _afterDelete(array $deledArr)
    {

    }

    /**
     * @title      fillEntity
     * @description
     * @createtime 2020/5/29 2:48 下午
     * @param array $param
     * @return array
     * @author     yangyuance
     */
    protected function fillEntity(array $param, $needValidate = true) {

        if(!empty($this->_Validate) && $needValidate){
            $param = ThinkerAdmin::validate()->make(
                $param, $this->_Validate[0], $this->_Validate[1]
            );
        }

        $entity = [];
        foreach($this->getModelFields() as $field) {
            if(isset($param[$field])) {
                if(is_array($param[$field])) {
                    $param[$field] = join(",", array_filter($param[$field]));
                }
                $entity[$field] = addslashes($param[$field]);
            }
        }
        return $entity;
    }

    /**
     * @title      getModelFields
     * @description
     * @createtime 2020/5/27 11:06 下午
     * @return array|mixed
     * @author     yangyuance
     */
    protected function getModelFields() {
        $modelFields = Cache::get("table_" . $this->getUseTable()->getName(), null);
        if(empty($modelFields)) {
            $modelFields = $this->getUseTable()->getTableFields();
            Cache::tag("table_fields")->set("table_" . $this->getUseTable()->getName(), $modelFields);
        }
        return $modelFields;
    }
}