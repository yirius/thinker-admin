<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/27
 * Time: 下午2:16
 */

namespace Yirius\Admin\model\restful;


use think\facade\Cache;
use think\Request;
use Yirius\Admin\Admin;
use Yirius\Admin\model\AdminRestful;

class AdminRole extends AdminRestful
{
    /**
     * @var \Yirius\Admin\model\table\AdminRole
     */
    protected $restfulTable = \Yirius\Admin\model\table\AdminRole::class;

    protected $tableCanEditField = ['status'];

    protected $tableEditMsg = "编辑后台角色成功";

    protected $tableSaveMsg = "新增后台角色成功";

    /**
     * @title index
     * @description
     * @createtime 2019/3/4 下午3:36
     * @param Request $request
     * @return mixed|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index(Request $request)
    {
        $this->send(($this->restfulTable)::adminList()
            ->setWhere([
                ['title', 'like', '%_var%'],
                'status'
            ])
            ->getResult());
    }

    /**
     * @title save
     * @description
     * @createtime 2019/3/4 下午3:30
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
            'title' => "require",
            'rules' => "require",
        ], [
            'title.require' => "角色名称必须填写",
            'rules.require' => "角色对应规则必须选择",
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
        $this->checkLoginPwd();
        
        Cache::clear("thinker_admin_auth");
        Admin::tools()->jsonSend();
    }

    /**
     * @title update
     * @description
     * @createtime 2019/3/4 下午3:31
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
     * @createtime 2019/3/4 下午3:31
     * @param $id
     * @return mixed|void
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function delete($id)
    {
        $this->checkLoginPwd();

        $this->defaultDelete($id, [1]);
    }

    /**
     * @title deleteall
     * @description
     * @createtime 2019/3/4 下午3:31
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
        $this->defaultDelete($deleteIds, [1]);
    }

}