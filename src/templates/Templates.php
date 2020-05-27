<?php


namespace Yirius\Admin\templates;

abstract class Templates
{
    protected $args = [];

    /**
     * @title      templates
     * @description
     * @createtime 2020/5/26 9:22 下午
     * @param $dataArrs
     * @return Templates
     * @author     yangyuance
     */
    public function templates($dataArrs) {
        //格式化当前参数
        foreach ($dataArrs as $i => $dataArr) {
            if(isset($this->args[$i])) {
                $this->addConfig($this->args[$i], $dataArr);
            } else {
                continue;
            }
        }
        return $this;
    }

    /**
     * 存放参数，map形式
     * @var array
     */
    protected $config = [];

    /**
     * @title      setConfig
     * @description
     * @createtime 2020/5/26 9:12 下午
     * @param $config
     * @return $this
     * @author     yangyuance
     */
    public function setConfig($config) {
        $this->config = $config;

        return $this;
    }

    /**
     * @title      addConfig
     * @description
     * @createtime 2020/5/26 9:12 下午
     * @param $key
     * @param $value
     * @return $this
     * @author     yangyuance
     */
    public function addConfig($key, $value) {
        $this->config[$key] = $value;
        return $this;
    }

    /**
     * @title      getConfig
     * @description
     * @createtime 2020/5/26 9:15 下午
     * @param null $key
     * @return array
     * @author     yangyuance
     */
    public function getConfig($key = null)
    {
        if(is_null($key)) {
            return $this->config;
        } else {
            if(isset($this->config[$key])) {
                return $this->config[$key];
            } else {
                return null;
            }
        }
    }

    /**
     * @title      render
     * @description
     * @createtime 2020/5/26 9:18 下午
     * @return string
     * @author     yangyuance
     */
    abstract public function render();
}