<?php


namespace Yirius\Admin\extend;


use think\db\Query;
use think\Model;
use think\Request;
use Yirius\Admin\ThinkerAdmin;

class ThinkerRestful extends ThinkerController
{
    /**
     * @var ThinkerModel
     */
    protected $_UseTable = null;

    /**
     * @var array
     */
    protected $_Where = [];

    /**
     * @var null
     */
    protected $_Alias = null;

    /**
     * @var null
     */
    protected $_With = null;

    /**
     * @var bool
     */
    protected $_UseEachItem = false;

    /**
     * @var bool
     */
    protected $_UseQuery = false;

    /**
     * @title      index
     * @description 获取列表信息
     * @createtime 2019/11/15 7:10 下午
     * @author     yangyuance
     */
    public function index()
    {
        try {
            ThinkerAdmin::Send()->table(
                ($this->_UseTable)::adminList()
                    ->setWhere($this->_Where)
                    ->setAlias($this->_Alias)
                    ->setWith($this->_With)
                    ->getResult($this->_UseEachItem ? function($item){
                        $this->_indexEach($item);
                    } : null, $this->_UseQuery ? function(Query $query){
                        $this->_indexQuery($query);
                    } : null)
            );
        }catch (\Exception $exception){
            ThinkerAdmin::Send()->table([], 0, $exception->getMessage());
        }
    }

    /**
     * @title      indexEach
     * @description
     * @createtime 2019/11/16 6:44 下午
     * @param $item
     * @return mixed
     * @author     yangyuance
     */
    protected function _indexEach($item)
    {

    }

    /**
     * @title      indexQuery
     * @description
     * @createtime 2019/11/16 6:46 下午
     * @param Query $query
     * @return Query
     * @author     yangyuance
     */
    protected function _indexQuery(Query $query)
    {
        return $query;
    }

    /**
     * @var array
     */
    protected $_CanEditFields = [];

    /**
     * @var string
     */
    protected $_SavedMsg = "保存信息成功";

    /**
     * @var string
     */
    protected $_EditedMsg = "编辑信息成功";

    /**
     * @var array
     */
    protected $_Validate = [];

    /**
     * @title      save
     * @description
     * @createtime 2019/11/16 8:18 下午
     * @param Request $request
     * @param array   $updateWhere
     * @author     yangyuance
     */
    public function save(Request $request, $updateWhere = [])
    {
        $param = $request->param();

        if(!empty($this->_Validate)){
            $param = ThinkerAdmin::Validate()->make($param, $this->_Validate[0], $this->_Validate[1]);
        }

        $param = $this->_beforeSave($param);

        $saveResult = $this->__defaultSave($param, $updateWhere);

        //触发完成任务
        $this->_afterSave(
            empty($updateWhere) ? false : true,
            $saveResult['saveData'],
            $saveResult['result']
        );

        //发送json
        ThinkerAdmin::Send()->json(
            $saveResult['saveData'],
            0,
            (empty($updateWhere) ? $this->_SavedMsg : $this->_EditedMsg)
        );
    }

    /**
     * @title      beforeSave
     * @description
     * @createtime 2019/11/16 7:56 下午
     * @param array $params
     * @return mixed
     * @author     yangyuance
     */
    protected function _beforeSave(array $params)
    {
        return $params;
    }

    /**
     * @title      afterSave
     * @description
     * @createtime 2019/11/16 8:22 下午
     * @param       $isUpdate
     * @param array $saveData
     * @param Model $model
     * @author     yangyuance
     */
    protected function _afterSave($isUpdate, array $saveData, Model $model)
    {

    }

    /**
     * @title      read
     * @description
     * @createtime 2019/11/16 8:24 下午
     * @param $id
     * @author     yangyuance
     */
    public function read($id)
    {
        ThinkerAdmin::Send()->json(
            ($this->_UseTable)::get(['id' => intval($id)])->toArray()
        );
    }

    /**
     * @title      update
     * @description
     * @createtime 2019/11/16 8:24 下午
     * @param         $id
     * @param Request $request
     * @author     yangyuance
     */
    public function update($id, Request $request)
    {
        if($request->param("__type") == "field"){
            $field = $request->param("field");
            $value = $request->param("value");
            if(in_array($field, $this->_CanEditFields)){

                $value = $this->_beforeUpdate($field, $value);

                //执行保存方法
                $saveResult = $this->__defaultSave([
                    $field => $value
                ], [
                    ['id', '=', intval($id)]
                ]);

                //触发完成任务
                $this->_afterUpdate(
                    true,
                    $saveResult['saveData'],
                    $saveResult['result']
                );

                //发送json
                ThinkerAdmin::Send()->json(
                    $saveResult['saveData'],
                    0,
                    $this->_EditedMsg
                );

            }else{
                ThinkerAdmin::Send()->json([], 0, lang("field can not edit"));
            }
        }else{
            //前往修改指定的参数
            $this->save($request, [
                ['id', '=', intval($id)]
            ]);
        }
    }

    /**
     * @title      beforeUpdate
     * @description
     * @createtime 2019/11/16 8:29 下午
     * @param $field
     * @param $value
     * @return mixed
     * @author     yangyuance
     */
    protected function _beforeUpdate($field, $value)
    {
        return $value;
    }

    /**
     * @title      afterSave
     * @description
     * @createtime 2019/11/16 8:22 下午
     * @param       $isUpdate
     * @param array $saveData
     * @param Model $model
     * @author     yangyuance
     */
    protected function _afterUpdate($isUpdate, array $saveData, Model $model)
    {

    }

    /**
     * @title      _defaultSave
     * @description
     * @createtime 2019/11/16 8:32 下午
     * @param array $saveData
     * @param array $where
     * @return array
     * @author     yangyuance
     */
    protected function __defaultSave(array $saveData, array $where = [])
    {
        $adminModel = ($this->_UseTable)::adminSave();

        $result = $adminModel->setAdd($saveData)->setWhere($where)->getResult();

        if($result === false){
            ThinkerAdmin::Send()->json([], 0, $adminModel->getError());
        }else{
            return [
                'saveData' => $adminModel->getAddData(),
                'result' => $result
            ];
        }
    }

    /**
     * @var array
     */
    protected $_NotDelete = [];

    /**
     * @title      delete
     * @description
     * @createtime 2019/11/16 8:52 下午
     * @param $id
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @author     yangyuance
     */
    public function delete($id)
    {
        $this->checkLoginPwd();

        $this->_beforeDelete([intval($id)]);

        $this->__defaultDelete([intval($id)]);
    }

    /**
     * @title      deleteall
     * @description
     * @createtime 2019/11/16 8:52 下午
     * @param Request $request
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @author     yangyuance
     */
    public function deleteall(Request $request)
    {
        $this->checkLoginPwd();

        $prevdelData = json_decode(input('param.data'), true);
        $delData = [];
        foreach($prevdelData as $i => $v){
            $delData[] = $v['id'];
        }

        $this->_beforeDelete($delData);

        $this->__defaultDelete($delData);
    }

    /**
     * @title      _beforeDelete
     * @description
     * @createtime 2019/11/16 8:51 下午
     * @param array $ids
     * @author     yangyuance
     */
    protected function _beforeDelete(array $ids)
    {

    }

    /**
     * @title      _afterDelete
     * @description
     * @createtime 2019/11/16 8:56 下午
     * @param array $errorIds
     * @author     yangyuance
     */
    protected function _afterDelete(array $errorIds)
    {

    }

    /**
     * @title      __defaultDelete
     * @description
     * @createtime 2019/11/16 8:42 下午
     * @param $data
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @author     yangyuance
     */
    protected function __defaultDelete($data)
    {
        $flag = ($this->_UseTable)::adminDelete()
            ->delete(is_array($data) ? $data : [$data])
            ->notDelete($this->_NotDelete)
            ->getResult();

        if($flag === true){
            $this->_afterDelete([]);

            ThinkerAdmin::Send()->json([], 0, lang("delete success"));
        }else if($flag === false){
            ThinkerAdmin::Send()->json([], 0, lang("delete error"));
        }else{
            $this->_afterDelete($flag);

            ThinkerAdmin::Send()->json([], 0, lang("not delete all", [
                'arr' => join(",", $flag)
            ]));
        }
    }
}