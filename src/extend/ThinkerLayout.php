<?php


namespace Yirius\Admin\extend;

/**
 * Class ThinkerLayout
 * @package Yirius\Admin\extend
 * @method self getId();
 */
abstract class ThinkerLayout
{
    /**
     * 每一个组件需要存在一个id, 只能是英文, 且不能存在特殊字符
     * @var string
     */
    protected $id = "";

    /**
     * @var array
     */
    protected $config = [];

    /**
     * 使用的attrs
     * @var array
     */
    protected $attrs = [];

    /**
     * 使用的class
     * @var array
     */
    protected $class = [];

    /**
     * ThinkerLayout constructor.
     */
    public function __construct()
    {
        //获取到上级的调用参数
        list($className, $functionName) = $this->getParentCall();

        //取出来class的名称
        $classArr = explode("/", str_replace("\\", "/", $className));

        //赋值
        $this->id = strtolower($classArr[count($classArr)-1] . "_" . $functionName);

        //防止内存溢出
        $className = null;$functionName = null;$classArr = null;
    }

    /**
     * @title      render
     * @description 每一个组件需要继承渲染接口
     * @createtime 2019/11/14 4:26 下午
     * @return string
     * @author     yangyuance
     */
    public abstract function render();

    /**
     * @title      getParentCall
     * @description 获取到上级是哪个函数调用了
     * @createtime 2019/11/14 4:53 下午
     * @return array
     * @author     yangyuance
     */
    public function getParentCall()
    {
        $data = debug_backtrace();

        $useCall = null;
        for($i = 1; $i < count($data); $i++){
            if(!isset($data[$i]['file'])){
                $useCall = [$data[$i]['class'], $data[$i]['function']];
                break;
            }
        }

        //如果不存在，就赋值第二个
        if(empty($useCall)){
            $useCall = $data[2];
        }
        $data = null;

        return $useCall;
    }

    /**
     * @title      setName
     * @description
     * @createtime 2019/11/14 4:58 下午
     * @param $id
     * @return $this
     * @author     yangyuance
     */
    public function setId($id)
    {
        $this->id = str_replace(["'", '"', ' ', '.', '。', ',', '，', ':', '：', '/', '、'], "_", $id);

        return $this;
    }

    /**
     * @title      setConfig
     * @description
     * @createtime 2019/11/14 4:59 下午
     * @param array $config
     * @return $this
     * @author     yangyuance
     */
    public function setConfig(array $config)
    {
        $this->config = array_merge($this->config, $config);

        return $this;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }


    /**
     * @title      setClass
     * @description 设置使用的class
     * @createtime 2019/11/14 6:11 下午
     * @param $class
     * @return $this
     * @author     yangyuance
     */
    public function setClass($class)
    {
        //防止传递空参数
        if(empty($class)){
            return $this;
        }

        if(is_array($class)){
            $class = join(" ", $class);
        }

        $this->class[] = $class;

        return $this;
    }

    /**
     * @title      removeClass
     * @description
     * @createtime 2019/11/16 11:33 下午
     * @param $class
     * @return $this
     * @author     yangyuance
     */
    public function removeClass($class)
    {
        $this->class = array_merge(array_diff($this->class, [$class]));

        return $this;
    }

    /**
     * @title      getUseClass
     * @description 返回使用的class
     * @createtime 2019/11/14 5:45 下午
     * @return string
     * @author     yangyuance
     */
    public function getClass()
    {
        return join(" ", $this->class);
    }

    /**
     * @title      setAttrs
     * @description
     * @createtime 2019/11/16 10:49 下午
     * @param      $attr
     * @param null $value
     * @return $this
     * @author     yangyuance
     */
    public function setAttrs($attr, $value = null)
    {
        //防止传递空参数
        if(empty($attr)){
            return $this;
        }
        if (is_array($attr)) {
            if (is_null($value)) {
                $this->attrs = array_merge($this->attrs, $attr);
            }
        } else {
            $this->attrs[$attr] = $value;
        }

        return $this;
    }

    /**
     * @title offAttributes
     * @description
     * @createtime 2019/2/25 下午6:00
     * @param $field
     * @return $this
     */
    public function removeAttr($field)
    {
        if (isset($this->attrs[$field])) {
            unset($this->attrs[$field]);
        }

        return $this;
    }

    /**
     * @title      getAttrs
     * @description
     * @createtime 2019/11/14 11:58 下午
     * @return string
     * @author     yangyuance
     */
    public function getAttrs()
    {
        $result = [];

        foreach ($this->attrs as $i => $attribute) {
            $result[] = $i . '="' . $attribute . '"';
        }

        return join(" ", $result);
    }

    /**
     * @title __call
     * @description
     * @createtime 2019/2/26 下午5:25
     * @param $name
     * @param $arguments
     * @return $this|mixed|string
     */
    public function __call($name, $arguments)
    {
        //找到是get还是set
        $operateType = substr($name, 0, 3);

        //判断第一个字符，一般大写，然后将其转为小写
        $firstChar = substr($name, 3, 1);
        $name = strtolower($firstChar) . substr($name, 4);

        //if it is set
        if($operateType === "set"){
            if(isset($this->$name)){
                //如果是类中的基础参数
                $this->$name = isset($arguments[0]) ? $arguments[0] : '';
            }else{
                $this->config[$name] = isset($arguments[0]) ? $arguments[0] : '';
            }
        }else if($operateType === "get"){
            if(isset($this->$name)){
                //如果是类中的基础参数
                return $this->$name;
            }else{
                return !isset($this->config[$name]) ? '' : $this->config[$name];
            }
        }

        return $this;
    }

    /**
     * @title      __toString
     * @description
     * @createtime 2019/11/14 4:27 下午
     * @return mixed
     * @author     yangyuance
     */
    public function __toString()
    {
        return $this->render();
    }
}