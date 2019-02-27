<?php
/**
 * Created by PhpStorm.
 * User: Yirius
 * Date: 2019/2/20
 * Time: 下午5:11
 */

namespace Yirius\Admin\model;


use think\Model;

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
     * @description get list class
     * @createtime 2019/2/20 下午7:59
     * @return ModelList
     */
    public static function adminList(){
        return (new ModelList(new static()));
    }

    public static function adminDelete(){
        return (new ModelDelete(new static()));
    }
}