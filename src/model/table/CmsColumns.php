<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/3/13
 * Time: 下午5:12
 */

namespace Yirius\Admin\model\table;


use think\Request;
use Yirius\Admin\model\AdminModel;
use Yirius\Admin\model\AdminRestful;

class CmsColumns extends AdminModel
{
    protected $table = "ices_cms_columns";

    /**
     * @title cmsmodels
     * @description relation to models
     * @createtime 2019/3/13 下午6:58
     * @return \think\model\relation\HasOne
     */
    public function cmsmodels()
    {
        return $this->hasOne("CmsModels", "id", "modelid");
    }

    /**
     * @title findIdByCache
     * @description
     * @createtime 2019/3/19 下午7:24
     * @param $columnid
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function findIdByCache($columnid)
    {
        return self::where('id', '=', $columnid)
            ->cache("thinker_cms_columns_" . $columnid, 0, "thinker_admin_cms")
            ->find();
    }
}