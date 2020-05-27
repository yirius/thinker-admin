<?php


namespace Yirius\Admin\utils;


class JsonUtil
{
    /**
     * @title      toJavaObject
     * @description
     * @createtime 2020/5/26 11:00 下午
     * @param array|string $jsonStr
     * @param $class
     * @return mixed
     * @author     yangyuance
     */
    public static function toJavaObject($jsonStr, $class) {
        $jsonStr = is_string($jsonStr) ? json_decode($jsonStr, true) : $jsonStr;
        return new $class($jsonStr);
    }

    /**
     * @title      fieldToArray
     * @description
     * @createtime 2020/5/26 11:02 下午
     * @param $fieldStr
     * @return array|mixed
     * @author     yangyuance
     */
    public static function fieldToObject($fieldStr) {
        if(!empty($fieldStr)) {
            return json_decode($fieldStr, true);
        } else {
            return [];
        }
    }

    /**
     * @title      fieldToArray
     * @description 字段转array
     * @createtime 2020/5/26 11:02 下午
     * @param $fieldStr
     * @return array|mixed
     * @author     yangyuance
     */
    public static function fieldToArray($fieldStr) {
        if(!empty($fieldStr)) {
            return json_decode($fieldStr, true);
        } else {
            return [];
        }
    }
}