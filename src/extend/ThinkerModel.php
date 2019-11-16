<?php


namespace Yirius\Admin\extend;


use think\Model;
use Yirius\Admin\extend\model\Delete;
use Yirius\Admin\extend\model\Lists;
use Yirius\Admin\extend\model\Save;
use Yirius\Admin\extend\model\Select;

class ThinkerModel extends Model
{
    protected $autoWriteTimestamp = 'datetime';

    /**
     * @title adminList
     * @description
     * @createtime 2019/2/27 下午2:22
     * @return Lists
     */
    public static function adminList()
    {
        return (new Lists(new static()));
    }

    /**
     * @title adminDelete
     * @description
     * @createtime 2019/2/27 下午2:22
     * @return Delete
     */
    public static function adminDelete()
    {
        return (new Delete(new static()));
    }

    /**
     * @title adminSave
     * @description
     * @createtime 2019/2/28 下午1:46
     * @return Save
     */
    public static function adminSave()
    {
        return (new Save(new static()));
    }

    /**
     * @title adminSelect
     * @description
     * @createtime 2019/2/28 下午8:05
     * @return Select
     */
    public static function adminSelect()
    {
        return (new Select(new static()));
    }
}