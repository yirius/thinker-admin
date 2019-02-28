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

class AdminRule extends AdminRestful
{
    /**
     * @title index
     * @description
     * @createtime 2019/2/28 上午11:46
     * @param Request $request
     * @return mixed|void
     */
    public function index(Request $request)
    {

        $allMenus = \Yirius\Admin\model\table\AdminRule::all()->toArray();

        $result = Admin::tools()->tree($allMenus, null, [
            'parentid' => "mid"
        ]);

        $this->send(count($allMenus), $this->getMenusTree($result));
    }

    /**
     * @title getMenusTree
     * @description
     * @createtime 2019/2/27 下午5:10
     * @param $trees
     * @param string $prev
     * @return array
     */
    protected function getMenusTree($trees, $prev = '')
    {
        $result = [];

        foreach($trees as $i => $tree){
            $tree['title'] = $prev . $tree['title'];
            if(!empty($tree['list'])){
                $list = $tree['list'];
                $tree['list'] = [];
                $result[] = $tree;
                $result = array_merge($result, $this->getMenusTree($list, empty($prev) ? "|--" : $prev . "--"));
            }else{
                $result[] = $tree;
            }
        }

        return $result;
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

        $adminSaveModel = table\AdminRule::adminSave();
        $isAdd = $adminSaveModel
            ->setValidate([
                'name' => "require",
                'title' => "require",
                'mid' => "require|number",
                'type' => "require|number"
            ], [
                'name.require' => "规则名称必须填写",
                'title.require' => "中文名称必须填写",
                'mid.require' => "上级编号必须填写",
                'mid.number' => "上级编号必须填写数字编号",
                'type.require' => "规则类型必须填写",
                'type.number' => "规则类型必须填写数字编号"
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
                $adminSaveModel = table\AdminRule::adminSave();
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