<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/1/30
 * Time: 下午4:03
 */

namespace Yirius\Admin\form;


use Yirius\Admin\Admin;
use Yirius\Admin\Layout;
use Yirius\Admin\layout\Breadcrumb;
use Yirius\Admin\layout\PageView;

/**
 * Class Form
 * @method Assembly\Button button($name, $label)
 * @method Assembly\Checkbox checkbox($name, $label)
 * @method Assembly\Date date($name, $label)
 * @method Assembly\Password password($name, $label)
 * @method Assembly\Radio radio($name, $label)
 * @method Assembly\Select select($name, $label)
 * @method Assembly\SelectPlus selectplus($name, $label)
 * @method Assembly\Switchs switchs($name, $label)
 * @method Assembly\Text text($name, $label)
 * @method Assembly\Textarea textarea($name, $label)
 * @method Assembly\Upload upload($name, $label)
 * @package Yirius\Admin
 */
class Form extends Layout
{
    /**
     * Form's name
     * @var string
     */
    protected $name = '';

    /**
     * All assemblys have been registered
     * @var array
     */
    protected $extends = [];

    /**
     * has been Initialized assembly
     * @var array
     */
    protected $assemblys = [];

    /**
     * @var array|\Closure|null
     */
    protected $breadcrumb = null;

    /**
     * page footer
     * @var Footer
     */
    protected $footer;

    /**
     * @var array
     */
    protected $value = [];

    /**
     * Form constructor.
     * @param $name
     * @param \Closure|null $callback
     * @throws \Exception
     */
    public function __construct($name, \Closure $callback = null)
    {
        $this->setName($name);

        $this->registerBuiltInAssemblys();

        if ($callback instanceof \Closure) {
            call($callback, [$this]);
        }
    }

    /**
     * @title registerBuiltInAssemblys
     * @description
     * @createtime 2019/2/24 下午2:06
     * @return $this
     * @throws \Exception
     */
    protected function registerBuiltInAssemblys()
    {
        $this->setExtends([
            'button' => Assembly\Button::class,
            'checkbox' => Assembly\Checkbox::class,
            'date' => Assembly\Date::class,
            'password' => Assembly\Password::class,
            'radio' => Assembly\Radio::class,
            'select' => Assembly\Select::class,
            'selectplus' => Assembly\SelectPlus::class,
            'switchs' => Assembly\Switchs::class,
            'text' => Assembly\Text::class,
            'textarea' => Assembly\Textarea::class,
            'upload' => Assembly\Upload::class,
        ]);

        //judge thinkeradmin's config extends
        if(config('thinkeradmin.form.extends')){
            $this->setExtends(config('thinkeradmin.form.extends'));
        }

        return $this;
    }

    /**
     * @title setExtends
     * @description
     * @createtime 2019/2/24 下午2:14
     * @param $name
     * @param null $class
     * @return $this
     * @throws \Exception
     */
    public function setExtends($name, $class = null)
    {
        if (is_null($class)) {
            if (is_array($name)) {
                $this->extends = array_merge($this->extends, $name);
            } else {
                throw new \Exception("form extend's name must be array when class is null");
            }
        } else {
            $this->extends[$name] = $class;
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
     * @title setAssemblys
     * @description
     * @createtime 2019/2/24 下午2:16
     * @param Assembly $assembly
     * @return $this
     */
    public function setAssemblys(Assembly $assembly)
    {
        $assembly->setForm($this)->setValue($this->getValue($assembly->getName()));

        $this->assemblys[] = $assembly;

        return $this;
    }

    /**
     * @title breadcrumb
     * @description
     * @createtime 2019/2/25 下午11:30
     * @param $breadcrumb
     * @return array|\Closure|null|Breadcrumb
     * @throws \Exception
     */
    public function breadcrumb($breadcrumb)
    {
        $this->breadcrumb = (new Breadcrumb($breadcrumb));

        return $this->breadcrumb;
    }

    /**
     * @title inline
     * @description set inline or not inline
     * @createtime 2019/2/27 下午12:22
     * @param \Closure $inline
     * @return \Closure|Inline
     */
    public function inline(\Closure $inline)
    {
        $inline = (new Inline($this, $inline));

        $this->assemblys[] = $inline;

        return $inline;
    }

    /**
     * @title footer
     * @description
     * @createtime 2019/2/25 下午11:26
     * @param \Closure $footer
     * @return Footer
     */
    public function footer(\Closure $footer = null)
    {
        $footer = (new Footer($this, $footer));

        $this->assemblys[] = $footer;

        return $footer;
    }

    /**
     * @title setName
     * @description
     * @createtime 2019/2/25 下午11:31
     * @param $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = str_replace(["'", '"', ' ', '.', '。', ',', '，', ':', '：', '/', '、'], "_", $name);

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
     * @title render
     * @description use for render each type
     * @createtime 2019/1/30 下午3:10
     * @return mixed
     */
    public function render()
    {
        //splicing assembly html
        $splicingHtml = "";
        foreach ($this->assemblys as $i => $v) {
            $splicingHtml .= '<div class="layui-form-item">' . $v->render() . '</div>';
        }

        //return all string
        return <<<HTML
<div class="layui-form" lay-filter="{$this->getName()}" id="{$this->getName()}">
{$splicingHtml}
</div>
HTML;
    }

    /**
     * @title show
     * @description
     * @createtime 2019/2/26 下午3:14
     * @return mixed
     */
    public function show()
    {
        return Admin::pageView(function (PageView $pageView) {
            if (!empty($this->breadcrumb)) {
                $pageView->setBreadcrumb($this->breadcrumb);
            }
            $pageView->card($this->render());
        })->render();
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
        if(empty($name)){
            return $this->value;
        }else{
            if(!isset($this->value[$name])){
                return '';
            }else{
                return $this->value[$name];
            }
        }
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
        if ($className = $this->getExtends($method)) {
            $assembly = new $className(...$arguments);
            $this->setAssemblys($assembly);
            return $assembly;
        }

        throw new \Exception("form extends not found " . $method);
    }
}