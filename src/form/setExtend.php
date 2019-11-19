<?php


namespace Yirius\Admin\form;


use Yirius\Admin\form\assemblys\Checkbox;
use Yirius\Admin\form\assemblys\ColorPicker;
use Yirius\Admin\form\assemblys\Date;
use Yirius\Admin\form\assemblys\Hidden;
use Yirius\Admin\form\assemblys\Html;
use Yirius\Admin\form\assemblys\Password;
use Yirius\Admin\form\assemblys\Radio;
use Yirius\Admin\form\assemblys\Select;
use Yirius\Admin\form\assemblys\SelectPlus;
use Yirius\Admin\form\assemblys\Slider;
use Yirius\Admin\form\assemblys\Switchs;
use Yirius\Admin\form\assemblys\Text;
use Yirius\Admin\form\assemblys\Button;
use Yirius\Admin\form\assemblys\Textarea;
use Yirius\Admin\form\assemblys\TinyEditor;
use Yirius\Admin\form\assemblys\Transfer;
use Yirius\Admin\form\assemblys\Tree;
use Yirius\Admin\ThinkerAdmin;

/**
 * Trait setExtend
 * @package Yirius\Admin\form
 * @method Button button($name, $label)
 * @method Checkbox checkbox($name, $label)
 * @method ColorPicker colorpicker($name, $label)
 * @method Date date($name, $label)
 * @method Hidden hidden($name, $label)
 * @method Html html($name, $label)
 * @method Password password($name, $label)
 * @method Radio radio($name, $label)
 * @method Select select($name, $label)
 * @method SelectPlus selectplus($name, $label)
 * @method Slider slider($name, $label)
 * @method Switchs switchs($name, $label)
 * @method Text text($name, $label)
 * @method Textarea textarea($name, $label)
 * @method Transfer transfer($name, $label)
 * @method Tree tree($name, $label)
 * @method TinyEditor tinyeditor($name, $label)
 */
trait setExtend
{
    /**
     * All assemblys have been registered
     * @var array
     */
    protected $extends = [
        'button' => Button::class,
        'checkbox' => Checkbox::class,
        'colorpicker' => ColorPicker::class,
        'date' => Date::class,
        'hidden' => Hidden::class,
        'html' => Html::class,
        'password' => Password::class,
        'radio' => Radio::class,
        'select' => Select::class,
        'selectplus' => SelectPlus::class,
        'slider' => Slider::class,
        'switchs' => Switchs::class,
        'text' => Text::class,
        'textarea' => Textarea::class,
        'transfer' => Transfer::class,
        'tree' => Tree::class,
        'tinyeditor' => TinyEditor::class,
    ];

    /**
     * has been Initialized assembly
     * @var array<Assembly>
     */
    protected $assemblys = [];

    /**
     * @var array
     */
    protected $value = [];

    /**
     * setExtend constructor.
     * @param callable|null $callable
     */
    public function __construct(callable $callable = null, $value = null)
    {
        parent::__construct();

        //judge thinkeradmin's config extends
        if (config('thinkeradmin.form.extends')) {
            $this->setExtends(config('thinkeradmin.form.extends'));
        }

        if(is_array($value)){
            $this->setValue($value);
        }

        if(is_callable($callable)){
            call($callable, [$this]);
        }
    }

    /**
     * @title setExtends
     * @description
     * @createtime 2019/2/24 下午2:14
     * @param $name
     * @param null $class
     * @return $this
     */
    public function setExtends($name, $class = null)
    {
        if (is_null($class)) {
            if (is_array($name)) {
                $this->extends = array_merge($this->extends, $name);
            } else {
                ThinkerAdmin::Send()->json([], 0, "form extend's name must be array when class is null");
            }
        } else {
            $this->extends[strtolower($name)] = $class;
        }

        return $this;
    }

    /**
     * @title getExtends
     * @description
     * @createtime 2019/2/24 下午2:14
     * @param $name
     * @return mixed|string
     */
    public function getExtends($name)
    {
        $class = empty($this->extends[$name]) ? '' : $this->extends[$name];

        if (!empty($class) && class_exists($class)) {
            return $class;
        }

        return false;
    }

    /**
     * @title      setAssemblys
     * @description
     * @createtime 2019/11/16 9:43 下午
     * @param Assembly $assembly
     * @return $this
     * @author     yangyuance
     */
    public function setAssemblys(Assembly $assembly)
    {
        $assembly->setValue($this->getValue($assembly->getField()));

        $this->assemblys[] = $assembly;

        return $this;
    }

    /**
     * @title setValue
     * @description
     * @createtime 2019/2/28 上午11:21
     * @param $value
     * @return $this
     */
    public function setValue($value)
    {
        if(!is_array($value)){
            $value = $value->toArray();
        }

        $this->value = $value;

        return $this;
    }

    /**
     * @title getValue
     * @description
     * @createtime 2019/2/28 上午11:24
     * @param null $name
     * @return array|mixed|string
     */
    public function getValue($name = null)
    {
        if (empty($name)) {
            return $this->value;
        } else {
            if (!isset($this->value[$name])) {
                return '';
            } else {
                return $this->value[$name];
            }
        }
    }

    /**
     * @title      __call
     * @description
     * @createtime 2019/11/16 9:47 下午
     * @param $method
     * @param $arguments
     * @return mixed
     * @author     yangyuance
     */
    public function __call($method, $arguments)
    {
        if ($className = $this->getExtends($method)) {
            $assembly = new $className(...$arguments);
            $this->setAssemblys($assembly);
            return $assembly;
        }

        parent::__call($method, $arguments);
    }
}