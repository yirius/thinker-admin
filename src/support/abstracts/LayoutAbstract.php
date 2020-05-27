<?php


namespace Yirius\Admin\support\abstracts;


use Yirius\Admin\ThinkerAdmin;

abstract class LayoutAbstract
{
    protected $id;

    protected $configs = [];
    protected $configsFields = [];

    protected $attrs = [];
    protected $attrsFields = [];

    protected $classs = [];
    protected $classsFields = [];

    public function __construct()
    {
        $this->setTrimId($this->getParentCall());
    }

    public function getParentCall()
    {
        $classMethod = ThinkerAdmin::getClassMethod();

        if(empty($classMethod)) {
            $data = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

            for($i = 1; $i < count($data); $i++){
                //最多找10次
                if($i > 9) {
                    break;
                }
                //判断数据
                if(isset($data[$i]['file']) && strpos($data[$i]['file'], "Container.php") !== false) {
                    break;
                }
            }

            $classExplode = explode("\\", $data[$i-1]['class']);
            $classMethod = strtolower($classExplode[count($classExplode)-1] . "_" . $data[$i-1]['function']);

            //如果不存在，就赋值第二个
            if(empty($classMethod)){
                $classMethod = strtolower(ThinkerAdmin::tools()->rand(12, "CHAR"));
            }

            $data = null;

            ThinkerAdmin::setClassMethod($classMethod);
        }

        return $classMethod;
    }

    /**
     * @title      setId
     * @description
     * @createtime 2020/5/27 1:48 下午
     * @param $id
     * @return $this
     * @author     yangyuance
     */
    public function setId($id){
        $this->id = $id;
        return $this;
    }

    /**
     * @title      setTrimId
     * @description
     * @createtime 2020/5/27 1:50 下午
     * @param $id
     * @return $this
     * @author     yangyuance
     */
    public function setTrimId($id) {
        return $this->setId(str_replace([
            "'", "\"", " ", ".", "。", ",", "，", ":", "：", "/", "、", "\\", "{", "}", "lambda$", "closure"
        ], "", $id));
    }

    /**
     * @title      getId
     * @description
     * @createtime 2020/5/27 1:50 下午
     * @return string
     * @author     yangyuance
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @title      setConfigs
     * @description
     * @createtime 2020/5/27 1:50 下午
     * @param array $configs
     * @return $this
     * @author     yangyuance
     */
    public function setConfigs(array $configs){
        $this->configs = $configs;
        return $this;
    }

    /**
     * @title      addConfig
     * @description
     * @createtime 2020/5/27 1:51 下午
     * @param $key
     * @param $value
     * @return $this
     * @author     yangyuance
     */
    public function addConfig($key, $value) {
        $this->configs[$key] = $value;
        return $this;
    }

    /**
     * @title      removeConfig
     * @description
     * @createtime 2020/5/27 1:52 下午
     * @param $key
     * @return $this
     * @author     yangyuance
     */
    public function removeConfig($key) {
        unset($this->configs[$key]);
        return $this;
    }

    /**
     * @return array
     */
    public function getConfigs()
    {
        return $this->configs;
    }

    /**
     * @title      getConfigString
     * @description
     * @createtime 2020/5/27 1:53 下午
     * @return string
     * @author     yangyuance
     */
    public function getConfigString() {
        return json_encode($this->configs);
    }

    /**
     * @title      setAttrs
     * @description
     * @createtime 2020/5/27 1:50 下午
     * @param array $attrs
     * @return $this
     * @author     yangyuance
     */
    public function setAttrs(array $attrs){
        $this->attrs = $attrs;
        return $this;
    }

    /**
     * @title      addConfig
     * @description
     * @createtime 2020/5/27 1:51 下午
     * @param $key
     * @param $value
     * @return $this
     * @author     yangyuance
     */
    public function addAttr($key, $value) {
        $this->attrs[$key] = $value;
        return $this;
    }

    /**
     * @title      removeConfig
     * @description
     * @createtime 2020/5/27 1:52 下午
     * @param $key
     * @return $this
     * @author     yangyuance
     */
    public function removeAttr($key) {
        unset($this->attrs[$key]);
        return $this;
    }

    /**
     * @return array
     */
    public function getAttrs()
    {
        return $this->attrs;
    }

    /**
     * @title      getConfigString
     * @description
     * @createtime 2020/5/27 1:53 下午
     * @return string
     * @author     yangyuance
     */
    public function getAttrString() {
        $result = [];
        foreach($this->attrs as $i => $attr) {
            $result[] = $i . '="' . $attr . '"';
        }
        return join(" ", $result);
    }

    /**
     * @title      setClasss
     * @description
     * @createtime 2020/5/27 1:55 下午
     * @param array $classs
     * @return $this
     * @author     yangyuance
     */
    public function setClasss(array $classs)
    {
        $this->classs = $classs;
        return $this;
    }

    /**
     * @title      addClass
     * @description
     * @createtime 2020/5/27 1:56 下午
     * @param $value
     * @return $this
     * @author     yangyuance
     */
    public function addClass($value) {
        $this->classs[] = $value;
        return $this;
    }

    /**
     * @title      removeClass
     * @description
     * @createtime 2020/5/27 1:57 下午
     * @param $value
     * @return $this
     * @author     yangyuance
     */
    public function removeClass($value) {
        unset($this->classs[array_search($value, $this->classs)]);
        return $this;
    }

    /**
     * @return array
     */
    public function getClasss()
    {
        return $this->classs;
    }

    /**
     * @title      getClassString
     * @description
     * @createtime 2020/5/27 1:58 下午
     * @return string
     * @author     yangyuance
     */
    public function getClassString() {
        return join(" ", $this->classs);
    }

    /**
     * @title      render
     * @description 每一个组件需要继承渲染接口
     * @createtime 2020/5/27 1:58 下午
     * @return string
     * @author     yangyuance
     */
    public abstract function render();

    /**
     * @title      __call
     * @description 重写getset
     * @createtime 2020/5/27 2:12 下午
     * @param $name
     * @param $arguments
     * @return $this|mixed|null
     * @author     yangyuance
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
            if(in_array($name, $this->configsFields)) {
                $this->configs[$name] = empty($arguments[0]) ? "" : $arguments[0];
            }else if(in_array($name, $this->attrsFields)) {
                $this->attrs[$name] = empty($arguments[0]) ? "" : $arguments[0];
            }else if(in_array($name, $this->classsFields)) {
                $this->classs[$name] = empty($arguments[0]) ? "" : $arguments[0];
            }
        }else if($operateType === "get"){
            if(in_array($name, $this->configsFields)) {
                return empty($this->configs[$name]) ? null : $this->configs[$name];
            }else if(in_array($name, $this->attrsFields)) {
                return empty($this->attrs[$name]) ? null : $this->attrs[$name];
            }else if(in_array($name, $this->classsFields)) {
                return empty($this->classs[$name]) ? null : $this->classs[$name];
            }
        }

        return $this;
    }
}