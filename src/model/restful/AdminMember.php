<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/26
 * Time: 下午3:59
 */

namespace Yirius\Admin\model\restful;

use think\Request;
use Yirius\Admin\Admin;
use Yirius\Admin\model\AdminRestful;
use Yirius\Admin\model\table\AdminRoleAccess;

class AdminMember extends AdminRestful
{
    /**
     * @var \Yirius\Admin\model\table\AdminMember
     */
    protected $restfulTable = \Yirius\Admin\model\table\AdminMember::class;

    /**
     * @var array
     */
    protected $tableCanEditField = ['status'];

    /**
     * @var string
     */
    protected $tableEditMsg = "编辑后台用户成功";

    /**
     * @var string
     */
    protected $tableSaveMsg = "新增后台用户成功";

    /**
     * @title index
     * @description
     * @createtime 2019/3/4 下午3:33
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
                ["username", "like", "%_var%"],
                ["phone", "like", "%_var%"],
            ])
            ->getResult());
    }

    /**
     * @title save
     * @description
     * @createtime 2019/3/4 下午3:25
     * @param Request $request
     * @param array $updateWhere
     * @return mixed|void
     * @throws \Exception
     */
    public function save(Request $request, $updateWhere = [])
    {
        $adminTools = Admin::tools();

        $addData = $request->param();
        $addData['status'] = $request->param('status', 0);
        //if set password
        if(!empty($addData['password'])){
            $addData['salt'] = $adminTools->rand();
            $addData['password'] = sha1($addData['password'] . $addData['salt']);
        }else{
            //if empty where, then operate is add a new user
            if(empty($updateWhere)){
                $adminTools->jsonSend([], 0, "新增后台用户，必须填写密码");
            }
            unset($addData['password']);
        }

        $this->defaultSave($addData, [[
            'username' => "require",
            'phone' => "require|mobile",
            'realname' => "require"
        ], [
            'username.require' => "登录账号必须填写",
            'phone.require' => "登录手机号必须填写",
            'phone.mobile' => "登录手机号必须填写手机格式，如139XXXXXXXX",
            'realname.require' => "真实姓名必须填写"
        ]], $updateWhere, function($isAdd) use($addData){
            //check and update access
            if(!empty($addData['groups']) && is_array($addData['groups'])){
                (new AdminRoleAccess())->checkOrUpdateAccess($addData['groups'], $isAdd->id);
            }
        });
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
     * @createtime 2019/2/26 下午4:11
     * @param $id
     * @param Request $request
     * @return mixed
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
     * @createtime 2019/2/26 下午4:11
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        $this->checkLoginPwd();

        $this->defaultDelete($id, [1], function($notDelete) use($id){
            (new AdminRoleAccess())->checkOrUpdateAccess([], $id);
        });
    }

    /**
     * @title deleteall
     * @description
     * @createtime 2019/2/27 上午1:47
     * @param Request $request
     * @return mixed
     */
    public function deleteall(Request $request)
    {
        $this->checkLoginPwd();

        $data = json_decode($request->param("data"), true);
        $deleteIds = [];
        foreach($data as $i => $v){
            $deleteIds[] = $v['id'];
        }
        $this->defaultDelete($deleteIds, [1], function($notDelete) use($deleteIds){
            $deletes = array_diff($deleteIds, $notDelete);
            foreach($deletes as $j => $val){
                (new AdminRoleAccess())->checkOrUpdateAccess([], $val);
            }
        });
    }
}