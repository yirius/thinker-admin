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

class AdminMenu extends AdminRestful
{
    /**
     * @var \Yirius\Admin\model\table\AdminMenu
     */
    protected $restfulTable = \Yirius\Admin\model\table\AdminMenu::class;

    protected $tableCanEditField = ['status'];

    protected $tableEditMsg = "编辑后台菜单成功";

    protected $tableSaveMsg = "新增后台菜单成功";

    /**
     * @title index
     * @description get table's list
     * @createtime 2019/2/26 下午4:09
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $allMenus = ($this->restfulTable)::all()->toArray();

        $this->send(count($allMenus), $this->getMenusTree(Admin::tools()->tree($allMenus)));
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
     * @createtime 2019/3/4 下午4:02
     * @param Request $request
     * @param array $updateWhere
     * @return mixed|void
     * @throws \Exception
     */
    public function save(Request $request, $updateWhere = [])
    {
        $addData = $request->param();
        $addData['sort'] = $request->param('sort', 0);

        $this->defaultSave($addData, [
            [
                'name' => "require",
                'title' => "require",
                'jump' => "require",
                'pid' => "require|number"
            ], [
                'name.require' => "英文名称必须填写",
                'title.require' => "中文名称必须填写",
                'jump.require' => "跳转地址必须填写",
                'pid.require' => "上级编号必须填写",
                'pid.number' => "上级编号必须填写数字编号"
            ]
        ], $updateWhere);
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
     * @createtime 2019/3/4 下午4:02
     * @param $id
     * @param Request $request
     * @return mixed|void
     * @throws \Exception
     */
    public function update($id, Request $request)
    {
        //判断是否是修改字段
        if($request->param("__type") == "field"){
            $this->defaultUpdate($id, $request->param("field"), $request->param("value"));
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
     * @createtime 2019/3/4 下午4:03
     * @param $id
     * @return mixed|void
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function delete($id)
    {
        $this->checkLoginPwd();

        $this->defaultDelete($id, [1,2,3,4,5]);
    }

    /**
     * @title deleteall
     * @description
     * @createtime 2019/3/4 下午4:03
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
        $this->defaultDelete($deleteIds, [1,2,3,4,5]);
    }
}