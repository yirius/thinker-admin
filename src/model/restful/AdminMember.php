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
     * @title index
     * @description
     * @createtime 2019/2/28 上午11:47
     * @param Request $request
     * @return mixed|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index(Request $request)
    {
        $result = \Yirius\Admin\model\table\AdminMember::adminList()->getResult();

        $this->send($result);
    }

    /**
     * @title save
     * @description add a new line
     * @createtime 2019/2/26 下午4:10
     * @param Request $request
     * @param array $where
     * @return mixed|void
     */
    public function save(Request $request, $where = [])
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
            if(empty($where)){
                $adminTools->jsonSend([], 0, "新增后台用户，必须填写密码");
            }
            unset($addData['password']);
        }

        $adminSaveModel = \Yirius\Admin\model\table\AdminMember::adminSave();
        $isAdd = $adminSaveModel
            ->setValidate([
                'username' => "require",
                'phone' => "require|mobile",
                'realname' => "require"
            ], [
                'username.require' => "登录账号必须填写",
                'phone.require' => "登录手机号必须填写",
                'phone.mobile' => "登录手机号必须填写手机格式，如139XXXXXXXX",
                'realname.require' => "真实姓名必须填写"
            ])
            ->setAdd($addData)
            ->setWhere($where)
            ->getResult();

        if($isAdd === false){
            $adminTools->jsonSend([], 0, $adminSaveModel->getError());
        }else{

            //check and update access
            if(!empty($addData['groups']) && is_array($addData['groups'])){
                (new AdminRoleAccess())->checkOrUpdateAccess($addData['groups'], $isAdd->id);
            }

            $adminTools->jsonSend([], 1, (empty($where) ? "新增" : "修改") ."后台管理员成功");
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
     * @createtime 2019/2/26 下午4:11
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function update($id, Request $request)
    {
        //判断是否是修改字段
        if($request->param("__type") == "field"){
            $field = $request->param("field");
            if(in_array($field, ['status'])){
                $adminSaveModel = \Yirius\Admin\model\table\AdminMember::adminSave();
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
                    Admin::tools()->jsonSend([], 1, "修改后台管理员成功");
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
     * @createtime 2019/2/27 上午1:47
     * @param Request $request
     * @return mixed
     */
    public function deleteall(Request $request)
    {
        // TODO: Implement deleteall() method.
    }
}