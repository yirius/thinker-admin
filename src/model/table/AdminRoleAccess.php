<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/27
 * Time: 下午2:17
 */

namespace Yirius\Admin\model\table;


use Yirius\Admin\model\AdminModel;

class AdminRoleAccess extends AdminModel
{
    protected $table = "thinker_admin_group_access";

    /**
     * @title checkOrUpdateAccess
     * @description
     * @createtime 2019/2/28 下午8:48
     * @param array $groups
     * @param $userid
     * @param int $type
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function checkOrUpdateAccess(array $groups, $userid, $type = 0)
    {
        $currentUserGroups = self::field("group_id")->where([
            ['uid', '=', $userid],
            ['type', '=', $type]
        ])->select()->toArray();

        $hasGroups = [];
        foreach($currentUserGroups as $i => $v){
            $hasGroups[] = $v['group_id'];
        }

        $deleteGroups = array_diff($hasGroups, $groups);
        $addGroups = array_diff($groups, $hasGroups);

        foreach($deleteGroups as $i => $deleteGroup){
            self::where('group_id', '=', $deleteGroup)
                ->where('uid', '=', $userid)
                ->where('type', '=', $type)
                ->delete();
        }

        foreach($addGroups as $i => $addGroup){
            self::save([
                'uid' => $userid,
                'group_id' => $addGroup,
                'type' => $type
            ]);
        }
    }
}