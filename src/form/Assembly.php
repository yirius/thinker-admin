<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/24
 * Time: 下午1:48
 */

namespace Yirius\Admin\form;


use Yirius\Admin\Layout;

abstract class Assembly extends Layout
{
    /**
     * assembly's id
     * @var string
     */
    protected $id;

    /**
     * assembly's name
     * @var string
     */
    protected $name;

    /**
     * assembly's label text
     * @var string
     */
    protected $label = '';

    /**
     * assembly's value
     * @var string
     */
    protected $value = '';

    /**
     * assembly's class, if it is array, then it will be joined
     * @var string|array
     */
    protected $class = 'layui-input-block';

    /**
     * html element's attr
     * @var array
     */
    protected $attributes = [];

    /**
     * Form's object
     * @var Form
     */
    protected $form;

    /**
     * Assembly constructor.
     * @param $name
     * @param $label
     */
    public function __construct($name, $label)
    {
        //set assembly's config
        $this->setName($name)->setLabel($label)->setId("thinkeradmin_" . $name);

        $this->init();
    }

    /**
     * @title init
     * @description
     * @createtime 2019/2/24 下午7:14
     */
    protected function init(){}

    /**
     * @title setName
     * @description
     * @createtime 2019/2/24 下午2:18
     * @param $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @title setLabel
     * @description
     * @createtime 2019/2/24 下午2:18
     * @param $label
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @title setValue
     * @description
     * @createtime 2019/2/24 下午2:40
     * @param $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @title setClass
     * @description
     * @createtime 2019/2/24 下午2:40
     * @param $class
     * @return $this
     */
    public function setClass($class)
    {
        if (is_array($this->class)) {
            $this->class[] = $class;
        } else {
            $this->class = $class;
        }

        return $this;
    }

    /**
     * @title getClass
     * @description get assembly's class
     * @createtime 2019/2/24 下午4:01
     * @return string
     */
    public function getClass()
    {
        return is_array($this->class) ? join(" ", $this->class) : $this->class;
    }

    /**
     * @title setId
     * @description
     * @createtime 2019/2/24 下午2:40
     * @param $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = str_replace(["'", '"', ' ', '.', '。', ',', '，', ':', '：', '/', '、'], "_", $id);

        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @title setAttributes
     * @description
     * @createtime 2019/2/24 下午4:35
     * @param $field
     * @param null $value
     * @return $this
     * @throws \Exception
     */
    public function setAttributes($field, $value = null)
    {
        if (is_null($value)) {
            if (is_array($field)) {
                $this->attributes = array_merge($this->attributes, $field);
            } else {
                throw new \Exception("Assembly Attr's field must be array when value is null");
            }
        } else {
            $this->attributes[$field] = $value;
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
    public function offAttributes($field)
    {
        if (isset($this->attributes[$field])) {
            unset($this->attributes[$field]);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        $result = [];

        foreach ($this->attributes as $i => $attribute) {
            $result[] = $i . '="' . $attribute . '"';
        }

        return join(" ", $result);
    }

    /**
     * @title setForm
     * @description
     * @createtime 2019/2/24 下午2:17
     * @param Form $form
     * @return $this
     */
    public function setForm(Form $form)
    {
        $this->form = $form;

        $this->afterSetForm();

        return $this;
    }

    /**
     * @title afterSetForm
     * @description
     * @createtime 2019/2/24 下午7:14
     */
    protected function afterSetForm()
    {
    }

    /**
     * @title parseFieldName
     * @description parse zIndex to z-index
     * @createtime 2019/3/3 下午6:52
     * @param $name
     * @return string
     */
    protected function parseFieldName($name)
    {
        $chars = "";
        for ($i = 0; $i < strlen($name); $i++) {
            if ((ord($name[$i]) >= ord('A')) && (ord($name[$i]) <= ord('Z'))) {
                $chars .= "-";
                $chars .= strtolower($name[$i]);
            } else {
                $chars .= $name[$i];
            }
        }

        return $chars;
    }

    /**
     * @title __call
     * @description
     * @createtime 2019/3/2 下午7:17
     * @param $name
     * @param $arguments
     * @return $this|string
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
        $operateType = substr($name, 0, 3);
        $name = "data" . $this->parseFieldName(substr($name, 3));

        //if it is set
        if ($operateType === "set") {

            $this->setAttributes($name, is_array($arguments[0]) ? json_encode($arguments[0]) : $arguments[0]);

            return $this;

        } else if ($operateType === "get") {

            return empty($this->attributes[$name]) ? '' : $this->attributes[$name];

        }

        return $this;
    }
}