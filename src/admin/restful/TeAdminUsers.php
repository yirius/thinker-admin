<?php


namespace Yirius\Admin\admin\restful;


use think\App;
use Yirius\Admin\admin\model\AdminGroupAccessModel;
use Yirius\Admin\admin\model\AdminLogsModel;
use Yirius\Admin\admin\model\AdminMemberModel;
use Yirius\Admin\extend\ThinkerRestful;
use Yirius\Admin\ThinkerAdmin;

class TeAdminUsers extends ThinkerRestful
{
    public function __construct(App $app = null)
    {
        parent::__construct($app);

        $this->_UseTable = AdminMemberModel::class;

        $this->_Where = [
            "id", "status",
            ['phone', 'like', '%_var%'],
            ['username', 'like', '%_var%'],
            ['realname', 'like', '%_var%']
        ];

        $this->_Validate = [[
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

        $this->_NotDelete = [1];
    }

    protected function _beforeSave(array $entity)
    {
        if(empty($entity['id']) && empty($entity['password'])){
            ThinkerAdmin::response()->msg("您当前是添加用户，必须填写密码")->fail();
        }

        if(!empty($entity['password'])){
            $entity['salt'] = ThinkerAdmin::tools()->rand();
            $entity['password'] = sha1(md5($entity['password']) . $entity['salt']);
        }else{
            unset($entity['password']);
        }

        return $entity;
    }

    protected function _afterSave(array $entity, $isUpdate = [])
    {
        $groups = input('param.groups');

        if(!empty($groups)){
            $groups = explode(",", $groups);
            (new AdminGroupAccessModel())->setUserAccess(
                $groups,
                $entity['id'],
                0
            );
        }

        thinker_log($this->tokenInfo, $isUpdate ? "编辑用户信息" : "新增用户信息");
    }

    protected function _afterUpdate(array $entity)
    {
        thinker_log($this->tokenInfo, "修改用户-" . $entity['id']);
    }

    protected function _afterDelete(array $deledArr)
    {
        thinker_log($this->tokenInfo, "删除用户-" . json_encode($deledArr));
    }
}