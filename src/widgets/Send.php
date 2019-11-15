<?php


namespace Yirius\Admin\widgets;


class Send extends Widgets
{
    /**
     * @title      json
     * @description 正常输出json数据
     * @createtime 2019/11/12 7:24 下午
     * @param array  $data
     * @param int    $code
     * @param string $msg
     * @param array  $header
     * @author     yangyuance
     */
    public function json($data = [], $code = 1, $msg = "success", $header = [])
    {
        response([
            'data' => $data,
            'code' => $code,
            'msg' => $msg
        ], 200, $header, "json")->send();
        exit();
    }

    /**
     * @title      table
     * @description 设置列表数据的返回
     * @createtime 2019/11/12 7:24 下午
     * @param array  $data
     * @param int    $code
     * @param string $msg
     * @param array  $header
     * @author     yangyuance
     */
    public function table($data = [], $code = 1, $msg = "success", $header = [])
    {
        response([
            'data' => isset($data['data']) ? $data['data'] : [],
            'count' => isset($data['count']) ? $data['count'] : 0,
            'code' => $code,
            'msg' => $msg
        ], 200, $header, "json")->send();
        exit();
    }

    /**
     * @title      html
     * @description
     * @createtime 2019/11/15 12:03 下午
     * @param       $data
     * @param array $header
     * @author     yangyuance
     */
    public function html($data, $header = [])
    {
        response($data, 200, $header, "html")->send();
        exit();
    }
}