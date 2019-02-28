<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/27
 * Time: 下午2:16
 */

namespace Yirius\Admin\model\restful;


use think\Request;
use Yirius\Admin\Admin;
use Yirius\Admin\model\AdminRestful;
use \Yirius\Admin\model\table as table;

class AdminRole extends AdminRestful
{
    /**
     * @title index
     * @description
     * @createtime 2019/2/28 下午4:30
     * @param Request $request
     * @return mixed|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index(Request $request)
    {
        $this->send(table\AdminRole::adminList()->getResult());
    }

    /**
     * @title save
     * @description
     * @createtime 2019/2/28 下午2:10
     * @param Request $request
     * @param array $where
     * @return mixed|void
     */
    public function save(Request $request, $where = [])
    {
        $addData = $request->param();
        $addData['status'] = $request->param('status', 0);

        $adminSaveModel = table\AdminRole::adminSave();
        $isAdd = $adminSaveModel
            ->setValidate([
                'name' => "require",
                'title' => "require",
                'mid' => "require|number"
            ], [
                'name.require' => "规则名称必须填写",
                'title.require' => "中文名称必须填写",
                'mid.require' => "上级编号必须填写",
                'mid.number' => "上级编号必须填写数字编号"
            ])
            ->setAdd($addData)
            ->setWhere($where)
            ->getResult();

        if($isAdd === false){
            Admin::tools()->jsonSend([], 0, $adminSaveModel->getError());
        }else{
            Admin::tools()->jsonSend([], 1, (empty($where) ? "新增" : "修改") ."规则成功");
        }
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
     * @createtime 2019/2/28 上午11:46
     * @param $id
     * @param Request $request
     * @return mixed|void
     */
    public function update($id, Request $request)
    {
        //判断是否是修改字段
        if($request->param("__type") == "field"){
            $field = $request->param("field");
            if(in_array($field, ['status'])){
                $adminSaveModel = table\AdminRole::adminSave();
                $isAdd = $adminSaveModel
                    ->setAdd([
                        $field => $request->param("value")
                    ])
                    ->setWhere([
                        ['id', '=', $id]
                    ])
                    ->getResult();

                if($isAdd === false){
                    Admin::tools()->jsonSend([], 0, $adminSaveModel->getError());
                }else{
                    Admin::tools()->jsonSend([], 1, "修改规则成功");
                }
            }else{
                Admin::tools()->jsonSend([], 0, "该字段不可修改");
            }
        }else{
            //执行整体更改
            $this->save($request, [
                ['id', '=', $id]
            ]);
        }
    }

    /**
     * @title delete
     * @description
     * @createtime 2019/2/26 下午4:11
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        // TODO: Implement delete() method.
    }

    /**
     * @title deleteall
     * @description
     * @createtime 2019/2/28 上午11:46
     * @param Request $request
     * @return mixed|void
     */
    public function deleteall(Request $request)
    {
        // TODO: Implement deleteall() method.
    }

}