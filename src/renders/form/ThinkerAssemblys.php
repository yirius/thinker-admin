<?php


namespace Yirius\Admin\renders\form;


use Yirius\Admin\renders\form\assemblys\Button;
use Yirius\Admin\renders\form\assemblys\Checkbox;
use Yirius\Admin\renders\form\assemblys\ColorPicker;
use Yirius\Admin\renders\form\assemblys\Date;
use Yirius\Admin\renders\form\assemblys\Email;
use Yirius\Admin\renders\form\assemblys\Hidden;
use Yirius\Admin\renders\form\assemblys\Html;
use Yirius\Admin\renders\form\assemblys\IconPicker;
use Yirius\Admin\renders\form\assemblys\Password;
use Yirius\Admin\renders\form\assemblys\Radio;
use Yirius\Admin\renders\form\assemblys\Select;
use Yirius\Admin\renders\form\assemblys\SelectPlus;
use Yirius\Admin\renders\form\assemblys\Slider;
use Yirius\Admin\renders\form\assemblys\Switchs;
use Yirius\Admin\renders\form\assemblys\Text;
use Yirius\Admin\renders\form\assemblys\Textarea;
use Yirius\Admin\renders\form\assemblys\TinyEditor;
use Yirius\Admin\renders\form\assemblys\Transfer;
use Yirius\Admin\renders\form\assemblys\Tree;
use Yirius\Admin\renders\form\assemblys\TreePlus;
use Yirius\Admin\renders\form\assemblys\Upload;
use Yirius\Admin\support\abstracts\LayoutAbstract;

abstract class ThinkerAssemblys extends LayoutAbstract
{
    /**
     * @var array<LayoutAbstract>
     */
    protected $assemblys = [];

    /**
     * @var array Key-Value
     */
    protected $useValue = [];

    /**
     * @title      setUseValue
     * @description
     * @createtime 2020/5/27 2:38 下午
     * @param array $useValue
     * @return $this
     * @author     yangyuance
     */
    public function setUseValue(array $useValue)
    {
        $this->useValue = $useValue;
        return $this;
    }

    /**
     * @return array
     */
    public function getUseValue()
    {
        return $this->useValue;
    }

    /**
     * @title      addAssemblys
     * @description
     * @createtime 2020/5/27 2:38 下午
     * @param LayoutAbstract $assemblys
     * @return $this
     * @author     yangyuance
     */
    public function addAssemblys(LayoutAbstract $assemblys)
    {
        $this->assemblys[] = $assemblys;
        return $this;
    }

    /**
     * @title       getAssembly
     * @description 设置组件相关内容
     * @createtime  2020/5/27 2:49 下午
     * @param          $field
     * @param          $text
     * @param Assembly $assembly
     * @return Assembly
     * @author      yangyuance
     */
    public function getAssembly($field, $text, Assembly $assembly)
    {
        $assembly->setThinkerAssemblys($this);

        if (isset($this->useValue[$field])) {
            $assembly->setValue($this->useValue[$field]);
        }

        $this->addAssemblys($assembly);

        return $assembly;
    }

    /**
     * @return array
     */
    public function getAssemblys()
    {
        return $this->assemblys;
    }

    /**
     * @title      button
     * @description
     * @createtime 2020/5/27 2:56 下午
     * @param $field
     * @param $text
     * @return Button
     * @author     yangyuance
     */
    public function button($field, $text)
    {
        return $this->getAssembly($field, $text, new Button($field, $text));
    }

    /**
     * @title      button
     * @description
     * @createtime 2020/5/27 2:56 下午
     * @param $field
     * @param $text
     * @return Checkbox
     * @author     yangyuance
     */
    public function checkbox($field, $text)
    {
        return $this->getAssembly($field, $text, new Checkbox($field, $text));
    }

    /**
     * @title      colorpicker
     * @description
     * @createtime 2020/5/27 2:56 下午
     * @param $field
     * @param $text
     * @return ColorPicker
     * @author     yangyuance
     */
    public function colorpicker($field, $text)
    {
        return $this->getAssembly($field, $text, new ColorPicker($field, $text));
    }

    /**
     * @title      button
     * @description
     * @createtime 2020/5/27 2:56 下午
     * @param $field
     * @param $text
     * @return Date
     * @author     yangyuance
     */
    public function date($field, $text)
    {
        return $this->getAssembly($field, $text, new Date($field, $text));
    }

    /**
     * @title      button
     * @description
     * @createtime 2020/5/27 2:56 下午
     * @param $field
     * @param $text
     * @return Email
     * @author     yangyuance
     */
    public function email($field, $text)
    {
        return $this->getAssembly($field, $text, new Email($field, $text));
    }

    /**
     * @title      button
     * @description
     * @createtime 2020/5/27 2:56 下午
     * @param $field
     * @param $text
     * @return Hidden
     * @author     yangyuance
     */
    public function hidden($field, $text)
    {
        return $this->getAssembly($field, $text, new Hidden($field, $text));
    }

    /**
     * @title      button
     * @description
     * @createtime 2020/5/27 2:56 下午
     * @param $field
     * @param $text
     * @return Html
     * @author     yangyuance
     */
    public function html($field, $text)
    {
        return $this->getAssembly($field, $text, new Html($field, $text));
    }

    /**
     * @title      button
     * @description
     * @createtime 2020/5/27 2:56 下午
     * @param $field
     * @param $text
     * @return IconPicker
     * @author     yangyuance
     */
    public function iconpicker($field, $text)
    {
        return $this->getAssembly($field, $text, new IconPicker($field, $text));
    }

    /**
     * @title      button
     * @description
     * @createtime 2020/5/27 2:56 下午
     * @param $field
     * @param $text
     * @return Password
     * @author     yangyuance
     */
    public function password($field, $text)
    {
        return $this->getAssembly($field, $text, new Password($field, $text));
    }

    /**
     * @title      button
     * @description
     * @createtime 2020/5/27 2:56 下午
     * @param $field
     * @param $text
     * @return Radio
     * @author     yangyuance
     */
    public function radio($field, $text)
    {
        return $this->getAssembly($field, $text, new Radio($field, $text));
    }

    /**
     * @title      button
     * @description
     * @createtime 2020/5/27 2:56 下午
     * @param $field
     * @param $text
     * @return Select
     * @author     yangyuance
     */
    public function select($field, $text)
    {
        return $this->getAssembly($field, $text, new Select($field, $text));
    }

    /**
     * @title      button
     * @description
     * @createtime 2020/5/27 2:56 下午
     * @param $field
     * @param $text
     * @return SelectPlus
     * @author     yangyuance
     */
    public function selectplus($field, $text)
    {
        return $this->getAssembly($field, $text, new SelectPlus($field, $text));
    }

    /**
     * @title      button
     * @description
     * @createtime 2020/5/27 2:56 下午
     * @param $field
     * @param $text
     * @return Slider
     * @author     yangyuance
     */
    public function slider($field, $text)
    {
        return $this->getAssembly($field, $text, new Slider($field, $text));
    }

    /**
     * @title      button
     * @description
     * @createtime 2020/5/27 2:56 下午
     * @param $field
     * @param $text
     * @return Switchs
     * @author     yangyuance
     */
    public function switchs($field, $text)
    {
        return $this->getAssembly($field, $text, new Switchs($field, $text));
    }

    /**
     * @title      button
     * @description
     * @createtime 2020/5/27 2:56 下午
     * @param $field
     * @param $text
     * @return Text
     * @author     yangyuance
     */
    public function text($field, $text)
    {
        return $this->getAssembly($field, $text, new Text($field, $text));
    }

    /**
     * @title      button
     * @description
     * @createtime 2020/5/27 2:56 下午
     * @param $field
     * @param $text
     * @return Textarea
     * @author     yangyuance
     */
    public function textarea($field, $text)
    {
        return $this->getAssembly($field, $text, new Textarea($field, $text));
    }

    /**
     * @title      button
     * @description
     * @createtime 2020/5/27 2:56 下午
     * @param $field
     * @param $text
     * @return TinyEditor
     * @author     yangyuance
     */
    public function tinyeditor($field, $text)
    {
        return $this->getAssembly($field, $text, new TinyEditor($field, $text));
    }

    /**
     * @title      button
     * @description
     * @createtime 2020/5/27 2:56 下午
     * @param $field
     * @param $text
     * @return Transfer
     * @author     yangyuance
     */
    public function transfer($field, $text)
    {
        return $this->getAssembly($field, $text, new Transfer($field, $text));
    }

    /**
     * @title      button
     * @description
     * @createtime 2020/5/27 2:56 下午
     * @param $field
     * @param $text
     * @return Tree
     * @author     yangyuance
     */
    public function tree($field, $text)
    {
        return $this->getAssembly($field, $text, new Tree($field, $text));
    }

    /**
     * @title      button
     * @description
     * @createtime 2020/5/27 2:56 下午
     * @param $field
     * @param $text
     * @return TreePlus
     * @author     yangyuance
     */
    public function treeplus($field, $text)
    {
        return $this->getAssembly($field, $text, new TreePlus($field, $text));
    }

    /**
     * @title      button
     * @description
     * @createtime 2020/5/27 2:56 下午
     * @param $field
     * @param $text
     * @return Upload
     * @author     yangyuance
     */
    public function upload($field, $text)
    {
        return $this->getAssembly($field, $text, new Upload($field, $text));
    }

}