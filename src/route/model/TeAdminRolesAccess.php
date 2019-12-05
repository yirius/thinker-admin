<?php


namespace Yirius\Admin\route\model;


use Yirius\Admin\extend\ThinkerModel;

class TeAdminRolesAccess extends ThinkerModel
{
    protected $table = "teadmin_group_access";

    /**
     * @title      getAccess
     * @description
     * @createtime 2019/11/25 6:09 下午
     * @param     $userid
     * @param int $accessType
     * @return array
     * @author     yangyuance
     */
    public static function getAccess($userid, $accessType = 0)
    {
        try{
            $access = self::where('uid', '=', $userid)
                ->where('type', '=', $accessType)
                ->select();

            $useGroupIds = [];
            foreach($access as $i => $v){
                $useGroupIds[] = $v['group_id'];
            }

            return $useGroupIds;
        }catch (\Exception $exception){
            trace("file: " . $exception->getFile() . "|line: " . $exception->getMessage());
            return [];
        }
    }

    /**
     * @title      setAccess
     * @description
     * @createtime 2019/11/25 6:08 下午
     * @param array $groupids
     * @param       $userid
     * @param int   $accessType
     * @author     yangyuance
     */
    public static function setAccess(array $groupids, $userid, $accessType = 0)
    {
        $useGroupIds = self::getAccess($userid, $accessType);

        //需要添加的角色
        $addGroups = array_diff($groupids, $useGroupIds);

        //需要删除的
        $delGroups = array_diff($useGroupIds, $groupids);

        try{
            foreach($delGroups as $i => $deleteGroup){
                self::where('group_id', '=', $deleteGroup)
                    ->where('uid', '=', $userid)
                    ->where('type', '=', $accessType)
                    ->delete();
            }

            foreach($addGroups as $i => $addGroup){
                (new static())->save([
                    'uid' => $userid,
                    'group_id' => $addGroup,
                    'type' => $accessType
                ]);
            }
        }catch (\Exception $exception){
            thinker_error($exception);
        }
    }
}