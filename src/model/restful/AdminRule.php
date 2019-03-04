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

class AdminRule extends AdminRestful
{
    /**
     * @var \Yirius\Admin\model\table\AdminRule
     */
    protected $restfulTable = \Yirius\Admin\model\table\AdminRule::class;

    protected $tableCanEditField = ['status'];

    protected $tableEditMsg = "编辑后台规则成功";

    protected $tableSaveMsg = "新增后台规则成功";

    /**
     * @title index
     * @description
     * @createtime 2019/3/4 下午3:52
     * @param Request $request
     * @return mixed|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index(Request $request)
    {
        $menus = ($this->restfulTable)::adminList()
            ->setWhere([
                'type'
            ])
            ->getResult();

        $menus['result'] = $this->getMenusTree(Admin::tools()->tree($menus['result'], null, [
            'parentid' => "mid"
        ]));

        $this->send($menus);
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
     * @createtime 2019/3/4 下午3:58
     * @param Request $request
     * @param array $updateWhere
     * @return mixed|void
     * @throws \Exception
     */
    public function save(Request $request, $updateWhere = [])
    {
        $addData = $request->param();
        $addData['status'] = $request->param('status', 0);

        $this->defaultSave($addData, [[
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
     * @createtime 2019/3/4 下午3:59
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
     * @createtime 2019/3/4 下午3:59
     * @param $id
     * @return mixed|void
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function delete($id)
    {
        $this->checkLoginPwd();

        $this->defaultDelete($id, [1,2,3,4,5,6,7,8,9,10,11,12,13]);
    }

    /**
     * @title deleteall
     * @description
     * @createtime 2019/3/4 下午3:59
     * @param Request $request
     * @return mixed|void
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function deleteall(Request $request)
    {
        $this->checkLoginPwd();

        $data = $request->param("data");
        $deleteIds = [];
        foreach($data as $i => $v){
            $deleteIds[] = $v['id'];
        }
        $this->defaultDelete($deleteIds, [1,2,3,4,5,6,7,8,9,10,11,12,13]);
    }

}