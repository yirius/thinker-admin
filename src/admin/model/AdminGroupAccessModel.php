<?php


namespace Yirius\Admin\admin\model;


use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\DbException;
use Yirius\Admin\config\ConsConfig;
use Yirius\Admin\extend\ThinkerModel;
use Yirius\Admin\ThinkerAdmin;

class AdminGroupAccessModel extends ThinkerModel
{
    protected $table = "teadmin_group_access";

    /**
     * @title      findUserGroup
     * @description
     * @createtime 2020/5/27 9:14 下午
     * @param array $tokenInfo
     * @param null  $accessType
     * @return array|\PDOStatement|string|\think\Model
     * @author     yangyuance
     */
    public function findUserGroup(array $tokenInfo, $accessType = null) {
        $userGroups = ThinkerAdmin::cache()->getAuthCache("user_groups", $tokenInfo, null);
        if(empty($userGroups)) {
            try {
                $userGroups = $this
                    ->withJoin("adminGroup", "LEFT")
                    ->where("admin_group_access_model.uid", "=", $tokenInfo[ConsConfig::$JWT_KEY])
                    ->where("admin_group_access_model.type", "=",
                        is_null($accessType) ? $tokenInfo[ConsConfig::$JWT_ACCESS_TYPE] : $accessType
                    )
                    ->selectOrFail()->toArray();
                //保存
                ThinkerAdmin::cache()->setAuthCache("user_groups", $tokenInfo, $userGroups);
            } catch (\Exception $e) {
                thinker_error($e);
                $userGroups = [];
            }
        }
        return $userGroups;
    }

    /**
     * @title      findUserRuleIds
     * @description 找到ruleids
     * @createtime 2020/5/27 9:20 下午
     * @param array $tokenInfo
     * @param null  $accessType
     * @return array|mixed
     * @author     yangyuance
     */
    public function findUserRuleIds(array $tokenInfo, $accessType = null) {
        $ruleids = ThinkerAdmin::cache()->getAuthCache("user_ruleids", $tokenInfo, null);
        if(empty($ruleids)) {
            //找到组别
            $adminGroupAccesses = $this->findUserGroup($tokenInfo, $accessType);
            //记录ids
            $ruleids = [];
            foreach($adminGroupAccesses as $adminGroupAccess) {
                $ruleids = array_merge($ruleids, explode(",", $adminGroupAccess['admin_group']['rules']));
            }

            //记录ids
            ThinkerAdmin::cache()->setAuthCache("user_ruleids", $tokenInfo, $ruleids);
        }
        return $ruleids;
    }

    public function setUserAccess(array $newGroupIds, $userid, $accessType) {

    }

    /**
     * @title      adminGroup
     * @description
     * @createtime 2020/5/27 9:10 下午
     * @return \think\model\relation\HasOne
     * @author     yangyuance
     */
    public function adminGroup() {
        return $this->hasOne("AdminGroupModel", "id", "group_id");
    }
}