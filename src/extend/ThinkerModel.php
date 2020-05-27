<?php


namespace Yirius\Admin\extend;


use think\Model;
use Yirius\Admin\extend\model\Lists;
use Yirius\Admin\extend\model\Selects;

class ThinkerModel extends Model
{
    protected $autoWriteTimestamp = 'datetime';

    /**
     * @title      lists
     * @description
     * @createtime 2020/5/27 10:54 下午
     * @return Lists
     * @author     yangyuance
     */
    public static function lists()
    {
        return (new Lists(new static()));
    }

    /**
     * @title      selects
     * @description
     * @createtime 2020/5/27 10:54 下午
     * @return Selects
     * @author     yangyuance
     */
    public static function selects()
    {
        return (new Selects(new static()));
    }
}