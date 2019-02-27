<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/1/30
 * Time: 下午4:04
 */

namespace Yirius\Admin\table;


use think\Collection;
use Yirius\Admin\Admin;
use Yirius\Admin\form\Form;
use Yirius\Admin\Layout;
use Yirius\Admin\layout\PageView;
use Yirius\Admin\table\events\Tool;

class Table extends Layout
{
    /**
     * Table's name
     * @var string
     */
    protected $name = '';

    /**
     * @var Form
     */
    protected $search = null;

    /**
     * @var array
     */
    protected $config = [
        'defaultToolbar' => ['refresh', 'filter', 'print', 'exports']
    ];

    /**
     * Columns list
     * @var array
     */
    protected $columns = [];

    /**
     * @var Tool
     */
    protected $tool = null;

    /**
     * @var Toolbar
     */
    protected $toolbar = null;

    /**
     * Table constructor.
     * @param $name
     * @param \Closure|null $callback
     */
    public function __construct($name, \Closure $callback = null)
    {
        $this->setName($name);

        if ($callback instanceof \Closure) {
            call($callback, [$this]);
        }
    }

    /**
     * @title search
     * @description set table's search
     * @createtime 2019/2/26 下午3:27
     * @param \Closure $callback
     * @return Form
     * @throws \Exception
     */
    public function search(\Closure $callback)
    {
        $this->search = (new Form($this->getName() . '_form', $callback));

        $this->search
            ->button("", '<i class="layui-icon layui-icon-search thinkeradmin-button-btn"></i>')
            ->setAttributes('lay-submit', '')
            ->setAttributes('lay-filter', $this->getName() . '_form_search');

        return $this->search;
    }

    /**
     * @title columns
     * @description
     * @createtime 2019/2/26 下午3:56
     * @param $field
     * @param $name
     * @return Columns
     */
    public function columns($field, $name)
    {
        $columns = (new Columns($field, $name));

        $columns->setToolbar($this->getName() . "_". $field ."_tool");

        $this->columns[] = $columns;

        return $columns;
    }

    /**
     * @title toolbar
     * @description tool's event
     * @createtime 2019/2/26 下午7:00
     * @param \Closure|null $callback
     * @return string|Tool
     */
    public function tool(\Closure $callback = null)
    {
        $this->tool = (new Tool($this, $callback));

        return $this->tool;
    }

    /**
     * @title toolbar
     * @description
     * @createtime 2019/2/27 上午12:37
     * @param \Closure|null $callback
     * @return Tool|Toolbar
     */
    public function toolbar(\Closure $callback = null)
    {
        $this->toolbar = (new Toolbar($this, $callback));

        return $this->toolbar;
    }

    /**
     * @title render
     * @description use for render each type
     * @createtime 2019/2/26 下午3:41
     * @return mixed|string
     * @throws \Exception
     */
    public function render()
    {
        //judge table server url
        if(empty($this->config['url']))
        {
            throw new \Exception("table config's field must have [url]");
        }

        //render columns
        $columns = [];
        $columnsTool = [];
        foreach($this->columns as $i => $v){
            $columns[] = $v->render();
            $columnsTool[] = $v->getTool();
        }
        //table's config cols
        $columns = json_encode($columns);
        //use tools html string
        $columnsTool = join("", $columnsTool);

        //get tool's event
        $toolEvent = empty($this->tool) ? '' : $this->tool->render();

        //get table's toolbar
        if(empty($this->toolbar)){
            $toolbar = '';
        }else{
            $toolbar = $this->toolbar->render();
            $this->setToolbar();
        }

        Admin::script('table', 2);

        Admin::script(<<<HTML
var _{$this->getName()}_ins = layui.table.render($.extend({
    elem: "#{$this->getName()}",
    where: layui.http._beforeAjax({}),
    response: {statusCode: 1},
    page: true,
    toolbar: true,
    cols: [{$columns}]
}, {$this->getConfig()}));
{$toolEvent}
HTML
        );

        return <<<HTML
{$toolbar}
{$columnsTool}
<table id="{$this->getName()}" lay-filter="{$this->getName()}"></table>
HTML
            ;
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
            $pageView->card($this->render());
        })->render();
    }

    /**
     * @title setName
     * @description
     * @createtime 2019/2/26 下午3:15
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
     * @title setConfig
     * @description
     * @createtime 2019/2/26 下午3:34
     * @param $field
     * @param null $value
     * @return $this
     * @throws \Exception
     */
    public function setConfig($field, $value = null)
    {
        if (is_null($value)) {
            if (is_array($field)) {
                $this->config = array_merge($this->config, $field);
            } else {
                throw new \Exception("table config's field must be array when value is null");
            }
        } else {
            $this->config[$field] = $value;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return json_encode($this->config);
    }

    /**
     * @title setUrl
     * @description
     * @createtime 2019/2/26 下午6:52
     * @param $value
     * @return $this
     */
    public function setUrl($value)
    {
        $this->config['url'] = $value;

        return $this;
    }

    /**
     * @title setToolbar
     * @description
     * @createtime 2019/2/27 上午12:55
     * @param null $id
     * @return $this
     */
    public function setToolbar($id = null)
    {
        if(is_null($id)){
            $this->config['toolbar'] = "#". $this->getName() ."_toolbar";
        }else{
            $this->config['toolbar'] = $id;
        }

        return $this;
    }

    /**
     * @title setDefaultToolbar
     * @description
     * @createtime 2019/2/27 上午1:05
     * @param array $default
     * @return $this
     */
    public function setDefaultToolbar(array $default)
    {
        $this->config['defaultToolbar'] = $default;

        return $this;
    }
}