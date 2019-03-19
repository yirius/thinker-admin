<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/3/13
 * Time: 下午5:12
 */

namespace Yirius\Admin\model\restful;


use think\Request;
use Yirius\Admin\model\AdminRestful;

class CmsModelsField extends AdminRestful
{
    /**
     * @var \Yirius\Admin\model\table\CmsModelsField
     */
    protected $restfulTable = \Yirius\Admin\model\table\CmsModelsField::class;

    protected $tableEditMsg = "修改Cms模型成功";

    protected $tableSaveMsg = "新增Cms模型成功";

    protected $tableCanEditField = ['status'];

    /**
     * @title index
     * @description
     * @createtime 2019/3/13 下午5:19
     * @param Request $request
     * @return mixed|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index(Request $request)
    {
        $this->send(($this->restfulTable)::adminList()->setWhere([
            ['modelid', '=', $request->param('modelid')]
        ])->getResult());
    }

    /**
     * @title save
     * @description
     * @createtime 2019/3/19 下午6:59
     * @param Request $request
     * @param array $updateWhere
     * @return mixed|void
     * @throws \Exception
     */
    public function save(Request $request, $updateWhere = [])
    {
        $addData = $request->param();

        if(in_array($addData['type'], ['checkbox','radio','select','selectplus','tree']) &&
            empty($addData['values']))
        {
            $this->sendError("您当前选择字段类型需要填写可选择值", 0);
        }

        $this->defaultSave($addData, [[
            'modelid' => "require",
            'title' => "require",
            'name' => "require|alphaDash",
            'type' => "require",
            'list_order' => "require"
        ], [
            'modelid.require' => "出现异常，暂未携带模型编号",
            'name.require' => "字段名称必须填写",
            'name.alphaDash' => "字段名称必须填写字母/数字/下划线",
            'title.require' => "字段标题必须填写",
            'type.require' => "字段类型必须填写",
            'list_order.require' => "字段排序必须填写"
        ]], $updateWhere);
    }

    /**
     * @title read
     * @description get a line use id
     * @createtime 2019/2/26 下午4:10
     * @param $id
     * @return mixed
     */
    public function read($id)
    {
        $fields = ($this->restfulTable)::get(['id' => $id])->getData("fields");

        if(empty($fields)){
            $this->send(0, []);
        }else{
            $fields = json_decode($fields, true);
            $this->send(count($fields), $fields);
        }
    }

    /**
     * @title update
     * @description
     * @createtime 2019/3/13 下午5:55
     * @param $id
     * @param Request $request
     * @return mixed|void
     * @throws \Exception
     */
    public function update($id, Request $request)
    {
        if($request->param("__type") == "field"){
            $this->defaultUpdate($id, $request->param("field"), $request->param("value"));
        }else{
            $this->save($request, [
                ['id', '=', $id]
            ]);
        }
    }

    /**
     * @title delete
     * @description
     * @createtime 2019/3/13 下午5:58
     * @param $id
     * @return mixed|void
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function delete($id)
    {
        $this->checkLoginPwd();

        $this->defaultDelete($id, [1,2,3,4,5,6]);
    }

    /**
     * @title deleteall
     * @description
     * @createtime 2019/3/13 下午5:58
     * @param Request $request
     * @return mixed|void
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function deleteall(Request $request)
    {
        $this->checkLoginPwd();

        $data = json_decode($request->param("data"), true);
        $deleteIds = [];
        foreach($data as $i => $v){
            $deleteIds[] = $v['id'];
        }
        $this->defaultDelete($deleteIds, [1,2,3,4,5,6]);
    }
}