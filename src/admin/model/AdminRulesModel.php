<?php


namespace Yirius\Admin\admin\model;


use Yirius\Admin\extend\ThinkerModel;
use Yirius\Admin\ThinkerAdmin;

class AdminRulesModel extends ThinkerModel
{
    protected $table = "teadmin_rules";

    /**
     * @title      findUserRules
     * @description 找到用户规则
     * @createtime 2020/5/27 9:25 下午
     * @param            $tokenInfo
     * @param array|null $ruleType
     * @return array|mixed|\PDOStatement|string|\think\Collection
     * @author     yangyuance
     */
    public function findUserRules($tokenInfo, array $ruleType = []) {
        $adminRules = ThinkerAdmin::cache()->getAuthCache("user_rules_" . join("_", $ruleType), $tokenInfo, null);

        if(empty($adminRules)) {
            $ruleIds = (new AdminGroupAccessModel())->findUserRuleIds($tokenInfo);

            try{
                if(!empty($ruleType)) {
                    $adminRules = $this
                        ->whereIn("id", $ruleIds)
                        ->whereIn("type", $ruleType)
                        ->where("status", "=", 1)
                        ->order("list_order", "DESC")
                        ->select()->toArray();
                } else {
                    $adminRules = $this
                        ->whereIn("id", $ruleIds)
                        ->where("status", "=", 1)
                        ->order("list_order", "DESC")
                        ->select()->toArray();
                }
            } catch (\Exception $exception) {
                thinker_error($exception);
                $adminRules = [];
            }

            ThinkerAdmin::cache()->setAuthCache("user_rules_" . join("_", $ruleType), $tokenInfo, $adminRules);
        }

        return $adminRules;
    }
}