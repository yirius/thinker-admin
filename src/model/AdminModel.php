<?php
/**
 * Created by PhpStorm.
 * User: Yirius
 * Date: 2019/2/20
 * Time: 下午5:11
 */

namespace Yirius\Admin\model;


use think\Model;
use Yirius\Admin\model\model\Delete;
use Yirius\Admin\model\model\Lists;

class AdminModel extends Model
{
    /**
     * @title init
     * @description after __construct, set model other data
     * @createtime 2019/2/20 下午5:17
     */
    protected static function init(){
        
    }

    /**
     * @title adminList
     * @description
     * @createtime 2019/2/27 下午2:22
     * @return Lists
     */
    public static function adminList(){
        return (new Lists(new static()));
    }

    /**
     * @title adminDelete
     * @description
     * @createtime 2019/2/27 下午2:22
     * @return Delete
     */
    public static function adminDelete(){
        return (new Delete(new static()));
    }
}