<?php


namespace Yirius\Admin\widgets;


class ThinkerResponse
{
    private $responseData = [
        'code' => 1,
        'msg' => "SUCCESS",
        'data' => []
    ];

    private $header = [];

    /**
     * @title      code
     * @description
     * @createtime 2020/5/26 11:26 下午
     * @param int $code
     * @return $this
     * @author     yangyuance
     */
    public function code($code) {
        $this->responseData['code'] = $code;
        return $this;
    }

    /**
     * @title      msg
     * @description
     * @createtime 2020/5/26 11:27 下午
     * @param string $msg
     * @return $this
     * @author     yangyuance
     */
    public function msg($msg) {
        $this->responseData['msg'] = $msg;
        return $this;
    }

    /**
     * @title      data
     * @description
     * @createtime 2020/5/26 11:27 下午
     * @param array $data
     * @return $this
     * @author     yangyuance
     */
    public function data(array $data) {
        $this->responseData['data'] = $data;
        return $this;
    }

    /**
     * @title      lists
     * @description
     * @createtime 2020/5/26 11:28 下午
     * @param array $data
     * @return $this
     * @author     yangyuance
     */
    public function lists(array $data) {
        $this->responseData['data'] = $data['data'];
        $this->responseData['count'] = $data['count'];
        return $this;
    }

    /**
     * @title      put
     * @description
     * @createtime 2020/5/26 11:28 下午
     * @param $key
     * @param $value
     * @return $this
     * @author     yangyuance
     */
    public function put($key, $value) {
        $this->responseData[$key] = $value;
        return $this;
    }

    /**
     * @title      headeer
     * @description
     * @createtime 2020/5/26 11:31 下午
     * @param      $key
     * @param null $value
     * @return $this
     * @author     yangyuance
     */
    public function headeer($key, $value = null) {
        if(is_array($key)) {
            $this->header = array_merge($this->header, $key);
        } else {
            $this->header[$key] = $value;
        }
        return $this;
    }

    /**
     * @title      success
     * @description
     * @createtime 2020/5/26 11:31 下午
     * @author     yangyuance
     */
    public function success() {
        $this->code(1);
        response($this->responseData, 200, $this->header, "json")->send();
        exit();
    }

    /**
     * @title      fail
     * @description
     * @createtime 2020/5/26 11:31 下午
     * @author     yangyuance
     */
    public function fail() {
        $this->code(0);
        response($this->responseData, 200, $this->header, "json")->send();
        exit();
    }

    /**
     * @title      response
     * @description
     * @createtime 2020/5/27 12:49 上午
     * @author     yangyuance
     */
    public function response() {
        response($this->responseData, 200, $this->header, "json")->send();
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
    public function html($data)
    {
        response($data, 200, $this->header, "html")->send();
        exit();
    }
}