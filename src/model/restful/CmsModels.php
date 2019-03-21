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

class CmsModels extends AdminRestful
{
    /**
     * @var \Yirius\Admin\model\table\CmsModels
     */
    protected $restfulTable = \Yirius\Admin\model\table\CmsModels::class;

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
        $this->send(($this->restfulTable)::adminList()->getResult());
    }

    /**
     * @title save
     * @description
     * @createtime 2019/3/3 下午10:40
     * @param Request $request
     * @param array $updateWhere
     * @return mixed
     */
    public function save(Request $request, $updateWhere = [])
    {
        $addData = $request->param();

        $this->defaultSave($addData, [[
            'nid' => "require|alpha",
            'title' => "require",
            'stitle' => "require",
            'table' => "alphaDash",
            'list_order' => "require"
        ], [
            'nid.require' => "模型标识必须填写",
            'nid.alpha' => "模型标识必须填写纯字母",
            'title.require' => "模型名称必须填写",
            'stitle.require' => "模型简称必须填写",
//            'table.require' => "模型对应数据库表必须填写",
            'table.alphaDash' => "模型对应数据库表只能含有填写字母/数字/下划线",
            'list_order.require' => "模型排序必须填写"
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
        // TODO: Implement read() method.
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