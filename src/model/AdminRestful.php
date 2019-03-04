<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/26
 * Time: 下午3:59
 */

namespace Yirius\Admin\model;

use think\Request;
use Yirius\Admin\Admin;
use Yirius\Admin\controller\AdminController;

abstract class AdminRestful extends AdminController
{
    /**
     * @var AdminModel
     */
    protected $restfulTable = null;

    /**
     * @var array
     */
    protected $tableCanEditField = [];

    /**
     * @var string
     */
    protected $tableSaveMsg = "";

    /**
     * @var string
     */
    protected $tableEditMsg = "";

    /**
     * @var bool
     */
    protected $returnJsonError = true;

    /**
     * @title index
     * @description get table's list
     * @createtime 2019/2/26 下午4:09
     * @param Request $request
     * @return mixed
     */
    public abstract function index(Request $request);

    /**
     * @title save
     * @description
     * @createtime 2019/3/3 下午10:40
     * @param Request $request
     * @param array $updateWhere
     * @return mixed
     */
    public abstract function save(Request $request, $updateWhere = []);

    /**
     * @title read
     * @description get a line use id
     * @createtime 2019/2/26 下午4:10
     * @param $id
     * @return mixed
     */
    public abstract function read($id);

    /**
     * @title update
     * @description
     * @createtime 2019/2/26 下午4:11
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public abstract function update($id, Request $request);

    /**
     * @title delete
     * @description
     * @createtime 2019/2/26 下午4:11
     * @param $id
     * @return mixed
     */
    public abstract function delete($id);

    /**
     * @title deleteall
     * @description
     * @createtime 2019/2/27 上午1:47
     * @param Request $request
     * @return mixed
     */
    public abstract function deleteall(Request $request);

    /**
     * @title defaultSave
     * @description
     * @createtime 2019/3/3 下午10:24
     * @param $addData
     * @param null $validate
     * @param array $where
     * @throws \Exception
     */
    protected function defaultSave($addData, $validate = null, $where = [], \Closure $afterSave = null)
    {
        if(is_null($this->restfulTable)){
            throw new \Exception(lang("restful not config table"));
        }else{
            $adminSaveModel = ($this->restfulTable)::adminSave();

            if(!is_null($validate)){
                $adminSaveModel->setValidate(...$validate);
            }

            $isAdd = $adminSaveModel
                ->setAdd($addData)
                ->setWhere($where)
                ->getResult();

            if($isAdd === false){
                Admin::tools()->jsonSend([], 0, $adminSaveModel->getError());
            }else{
                if($afterSave instanceof \Closure) call($afterSave, [$isAdd]);
                Admin::tools()->jsonSend([], 1, (empty($where) ? $this->tableSaveMsg : $this->tableEditMsg));
            }
        }
    }

    /**
     * @title defaultUpdate
     * @description
     * @createtime 2019/3/3 下午10:33
     * @param $id
     * @param $field
     * @param $value
     * @throws \Exception
     */
    protected function defaultUpdate($id, $field, $value)
    {
        if(in_array($field, $this->tableCanEditField)){
            $this->defaultSave([
                $field => $value
            ], null, [
                ['id', '=', $id]
            ]);
        }else{
            Admin::tools()->jsonSend([], 0, "该字段不可修改");
        }
    }

    /**
     * @title defaultDelete
     * @description
     * @createtime 2019/3/4 下午2:43
     * @param $data
     * @param array $notDelete
     * @param \Closure|null $afterDelete
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    protected function defaultDelete($data, $notDelete = [], \Closure $afterDelete = null)
    {
        $flag = ($this->restfulTable)::adminDelete()
            ->delete(is_array($data) ? $data : [$data])
            ->notDelete($notDelete)
            ->getResult();

        if($flag === true){
            if($afterDelete instanceof \Closure) call($afterDelete, [[]]);
            Admin::tools()->jsonSend([], 1, lang("delete success"));
        }else if($flag === false){
            Admin::tools()->jsonSend([], 0, lang("delete error"));
        }else{
            if($afterDelete instanceof \Closure) call($afterDelete, [$flag]);
            Admin::tools()->jsonSend([], 0, lang("not delete all", ['arr' => join(",", $flag)]));
        }
    }

    /**
     * @title send
     * @description
     * @createtime 2019/2/26 下午4:15
     * @param $count
     * @param $data
     */
    public function send($count, array $data = null)
    {
        //judge if data is AdminModel
        if(is_array($count)){
            $data = $count['result'];
            $count = $count['count'];
        }
        //direct send
        response([
            'code' => 1,
            'count' => $count,
            'data' => $data,
            'msg' => "success"
        ], 200, [], "json")->send();

        exit;
    }
}