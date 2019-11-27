<?php


namespace Yirius\Admin\widgets;

class Widgets
{
    /**
     * 创建静态私有的变量保存该类对象
     * @var null
     */
    static protected $instance = [];

    /**
     * @var array
     */
    protected $config = [];

    /**
     * Widgets constructor.
     * @param array|null $config
     */
    public function __construct(array $config = null)
    {
        if(!is_null($config)){
            $this->config = $config;
        }

        $this->_init();
    }

    protected function _init()
    {

    }

    /**
     * @title      setConfig
     * @description
     * @createtime 2019/11/27 4:51 下午
     * @param $config
     * @return $this
     * @author     yangyuance
     */
    public function setConfig(array $config = null)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    public function __clone()
    {
        // TODO: Implement __clone() method.
    }

    /**
     * @title      getInstance
     * @description
     * @createtime 2019/11/27 4:45 下午
     * @param array|null $config
     * @return Cache|Http|Send|Tools|Tree|Validate
     * @author     yangyuance
     */
    public static function getInstance(array $config = null)
    {
        $calledClass =  get_called_class();

        if(!isset(self::$instance[$calledClass])){
            self::$instance[$calledClass] = new $calledClass($config);
        }else{
            self::$instance[$calledClass]->setConfig($config)->_init();
        }

        return self::$instance[$calledClass];
    }

    public function __destruct()
    {
        unset($this->config);
    }
}