<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/3/26
 * Time: 下午2:31
 */

namespace Yirius\Admin\model\table;


use think\facade\Cache;
use Yirius\Admin\model\AdminModel;

class AdminConfigs extends AdminModel
{
    protected $table = "ices_admin_configs";

    /**
     * @title getValues
     * @description
     * @createtime 2019/3/26 下午2:43
     * @param null $name
     * @return array|bool|mixed
     */
    public static function getValues($name = null)
    {
        $config = Cache::get("thinker_admin_configsvalue");
        if(!$config){
            $config = [];
            foreach(self::all()->toArray() as $i => $v){
                $config[$v['name']] = $v['value'];
            }
            Cache::set("thinker_admin_configsvalue", $config);
        }
        //judge return things
        if(is_null($name)){
            return $config;
        }else{
            if(empty($config[$name])){
                return false;
            }else{
                return $config[$name];
            }
        }
    }
}