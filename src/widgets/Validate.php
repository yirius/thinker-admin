<?php


namespace Yirius\Admin\widgets;


use Yirius\Admin\ThinkerAdmin;

class Validate
{
    /**
     * @title      make
     * @description 快捷验证方法
     * @createtime 2019/11/12 7:28 下午
     * @param array $params
     * @param array $fields
     * @param array $msg
     * @return array
     * @author     yangyuance
     */
    public function make(array $params, array $fields, array $msg)
    {
        $validate = \think\facade\Validate::make($fields, $msg);

        if(!$validate->check($params)){
            ThinkerAdmin::Send()->json([], 0, $validate->getError());
        }

        return $params;
    }
}