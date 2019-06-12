<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/24
 * Time: 下午6:17
 */

namespace Yirius\Admin\form;


use Yirius\Admin\Layout;

/**
 * Class Inline
 * @method Assembly\Button button($name, $label)
 * @method Assembly\Checkbox checkbox($name, $label)
 * @method Assembly\ColorPicker colorpicker($name, $label)
 * @method Assembly\Date date($name, $label)
 * @method Assembly\Hidden hidden($name, $label)
 * @method Assembly\Html html($name, $label)
 * @method Assembly\Password password($name, $label)
 * @method Assembly\Radio radio($name, $label)
 * @method Assembly\Select select($name, $label)
 * @method Assembly\SelectPlus selectplus($name, $label)
 * @method Assembly\Slider slider($name, $label)
 * @method Assembly\Switchs switchs($name, $label)
 * @method Assembly\Text text($name, $label)
 * @method Assembly\Textarea textarea($name, $label)
 * @method Assembly\Transfer transfer($name, $label)
 * @method Assembly\Tree tree($name, $label)
 * @method Assembly\Upload upload($name, $label)
 * @method Assembly\WangEditor wangeditor($name, $label)
 * @package Yirius\Admin\form
 */
class Inline extends Layout
{
    /**
     * @var Form
     */
    protected $form;

    protected $assemblys = [];

    /**
     * Inline constructor.
     * @param Form $form
     * @param \Closure|null $callback
     */
    public function __construct(Form $form, \Closure $callback = null)
    {
        $this->form = $form;

        if($callback instanceof \Closure){
            call($callback, [$this]);
        }
    }

    /**
     * @title setAssemblys
     * @description
     * @createtime 2019/2/24 下午6:26
     * @param Assembly $assembly
     * @return $this
     */
    public function setAssemblys(Assembly $assembly)
    {
        $assembly->setForm($this->form)->setValue($this->form->getValue($assembly->getName()));

        $this->assemblys[] = $assembly;

        return $this;
    }

    /**
     * @title render
     * @description render html
     * @createtime 2019/2/24 下午4:25
     * @return string
     */
    public function render()
    {
        $render = "";
        foreach ($this->assemblys as $i => $v) {
            $render .= '<div class="layui-inline">' . $v->setClass('layui-input-inline')->render() . "</div>";
        }

        return $render;
    }

    /**
     * @title __call
     * @description find assembly
     * @createtime 2019/2/24 下午4:15
     * @param $method
     * @param $arguments
     * @return mixed
     * @throws \Exception
     */
    public function __call($method, $arguments)
    {
        if ($className = $this->form->getExtends($method)) {
            $assembly = new $className(...$arguments);
            $this->setAssemblys($assembly);
            return $assembly;
        }

        throw new \Exception("form extends not found " . $method);
    }
}