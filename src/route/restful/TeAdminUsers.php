<?php


namespace Yirius\Admin\route\restful;


use think\db\Query;
use think\Model;
use Yirius\Admin\extend\ThinkerRestful;
use Yirius\Admin\route\model\TeAdminRolesAccess;
use Yirius\Admin\ThinkerAdmin;

class TeAdminUsers extends ThinkerRestful
{
    protected $_UseTable = \Yirius\Admin\route\model\TeAdminUsers::class;

    protected $_Where = [
        "id", "status",
        "username" => ['username', 'like', '%_var%'],
        "phone"    => ['phone', 'like', '%_var%'],
        "realname" => ['realname', 'like', '%_var%']
    ];

    protected $_Validate = [[
        'username'  => "require",
        'phone'     => "require|mobile",
        'realname'  => "require",
        'groups'    => "require"
    ], [
        'username.require'  => "登录用户名必须填写",
        'phone.require'     => "对应手机号必须填写",
        'phone.mobile'      => "对应手机号必须填写正确格式",
        'realname.require'  => "展示姓名必须填写",
        'groups.require'    => "所属用户组必须选择",
    ]];

    /**
     * @title      _beforeSave
     * @description
     * @createtime 2019/11/25 6:19 下午
     * @param array $params
     * @param       $updateWhere
     * @return array|mixed
     * @author     yangyuance
     */
    protected function _beforeSave(array $params, $updateWhere)
    {
        if(empty($updateWhere) && empty($params['password'])){
            ThinkerAdmin::Send()->json([], 0, "您当前是添加用户，必须填写密码");
        }

        if(!empty($params['password'])){
            $params['salt'] = ThinkerAdmin::Tools()->rand();
            $params['password'] = sha1($params['password'] . $params['salt']);
        }else{
            unset($params['password']);
        }

        return $params;
    }

    /**
     * @title      _afterSave
     * @description
     * @createtime 2019/11/25 6:20 下午
     * @param       $isUpdate
     * @param array $saveData
     * @param Model $model
     * @author     yangyuance
     */
    protected function _afterSave($isUpdate, array $saveData, Model $model)
    {
        $groups = input('param.groups');

        if(!empty($groups)){
            $groups = explode(",", $groups);
            TeAdminRolesAccess::setAccess($groups, $model->getLastInsID());
        }

        thinker_log($this->tokenInfo, $isUpdate ? "编辑用户信息" : "新增用户信息");
    }

    protected function _afterUpdate($id, $field, array $saveData, Model $model)
    {
        thinker_log($this->tokenInfo, "编辑字段:".$field);
    }

    protected $_NotDelete = [1];
}