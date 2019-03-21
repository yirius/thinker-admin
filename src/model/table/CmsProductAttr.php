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

class CmsProductAttr extends AdminModel
{
    protected $table = "ices_cms_product_attr";

    /**
     * @title cmscolumns
     * @description
     * @createtime 2019/3/19 上午11:41
     * @return \think\model\relation\HasOne
     */
    public function cmscolumns()
    {
        return $this->hasOne("CmsColumns", "id", "columnid");
    }
}