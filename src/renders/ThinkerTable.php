<?php


namespace Yirius\Admin\renders;


use Yirius\Admin\renders\table\event\ColsEvent;
use Yirius\Admin\renders\table\event\TableEvent;
use Yirius\Admin\renders\table\ThinkerColumns;
use Yirius\Admin\renders\table\ThinkerToolbar;
use Yirius\Admin\support\abstracts\LayoutAbstract;
use Yirius\Admin\templates\TemplateList;
use Yirius\Admin\ThinkerAdmin;

/**
 * Class ThinkerTable
 * @package Yirius\Admin\renders
 * @method ThinkerTable setUrl(string $url);
 * @method ThinkerTable setToolbar($toolbar);
 * @method ThinkerTable setWidth($width);
 * @method ThinkerTable setHeight($height);
 * @method ThinkerTable setCellMinWidth(int $width);
 * @method ThinkerTable setData(array $data);
 * @method ThinkerTable setTotalRow(bool $isTotal);
 * @method ThinkerTable setPage(bool $isPage);
 * @method ThinkerTable setLimit(int $limit);
 * @method ThinkerTable setLimits(array $limits);
 * @method ThinkerTable setLoading(bool $loading);
 * @method ThinkerTable setTitle(string $title);
 * @method ThinkerTable setText(string $text);
 * @method ThinkerTable setAutoSort(bool $isSort);
 *
 * @method ThinkerTable getUrl();
 * @method ThinkerTable getToolbar();
 * @method ThinkerTable getWidth();
 * @method ThinkerTable getHeight();
 * @method ThinkerTable getCellMinWidth();
 * @method ThinkerTable getData();
 * @method ThinkerTable getTotalRow();
 * @method ThinkerTable getPage();
 * @method ThinkerTable getLimit();
 * @method ThinkerTable getLimits();
 * @method ThinkerTable getLoading();
 * @method ThinkerTable getTitle();
 * @method ThinkerTable getText();
 * @method ThinkerTable getAutoSort();
 */
class ThinkerTable extends LayoutAbstract
{
    protected $configsFields = [
        "url", "toolbar", "width", "height", "cellMinWidth",
        "data", "totalRow", "page", "limit", "limits", "loading", "title", "text", "autoSort"
    ];

    public function __construct(callable $closure = null)
    {
        parent::__construct();

        if(is_callable($closure)) call($closure, [$this]);
    }

    /**
     * 对应的Restful网址
     */
    protected $restfulUrl;

    /**
     * @title      setRestfulUrl
     * @description
     * @createtime 2020/5/27 5:42 下午
     * @param $restfulUrl
     * @return $this
     * @author     yangyuance
     */
    public function setRestfulUrl($restfulUrl)
    {
        $this->restfulUrl = $restfulUrl;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRestfulUrl()
    {
        return $this->restfulUrl;
    }

    /**
     * 添加或修改的网址
     */
    protected $operateUrl;

    /**
     * @title      setOperateUrl
     * @description
     * @createtime 2020/5/27 5:43 下午
     * @param $operateUrl
     * @return $this
     * @author     yangyuance
     */
    public function setOperateUrl($operateUrl)
    {
        $this->operateUrl = $operateUrl;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOperateUrl()
    {
        return $this->operateUrl;
    }

    /**
     * Columns对应的List
     * @var array<ThinkerColumns>
     */
    protected $columns = [];

    /**
     * Toolbar操作条
     */
    protected $thinkerToolbar = null;

    /**
     * 对应一列的操作栏
     */
    protected $colsEvent = null;

    /**
     * 对应table的事件
     */
    protected $tableEvent = null;

    /**
     * 搜索项目
     */
    protected $search = null;

    /**
     * @title      columns
     * @description
     * @createtime 2020/5/27 2:22 下午
     * @param $field
     * @param $name
     * @return ThinkerColumns
     * @author     yangyuance
     */
    public function columns($field, $name) {
        $columns = new ThinkerColumns($field, $name, $this);

        $this->columns[] = $columns;

        return $columns;
    }

    /**
     * @title      getColumnsCount
     * @description
     * @createtime 2020/5/27 2:23 下午
     * @return int
     * @author     yangyuance
     */
    public function getColumnsCount() {
        return count($this->columns);
    }

    /**
     * 设置一列触发的事件
     * @param closure
     * @return
     */
    public function colsEvent(callable $closure = null) {
        if($this->colsEvent == null){
            $this->colsEvent = new ColsEvent($this, $closure);
        }
        return $this->colsEvent;
    }

    /**
     * @title      event
     * @description 表格的事件
     * @createtime 2020/5/27 2:24 下午
     * @param callable|null $closure
     * @return TableEvent|null
     * @author     yangyuance
     */
    public function event(callable $closure = null) {
        if($this->tableEvent == null){
            $this->tableEvent = new TableEvent($this, $closure);
        }
        return $this->tableEvent;
    }

    /**
     * @title      search
     * @description
     * @createtime 2020/5/27 5:36 下午
     * @param callable|null $closure
     * @return ThinkerForm|null
     * @author     yangyuance
     */
    public function search(callable $closure = null) {
        if($this->search == null){
            $this->search = new ThinkerForm(function(ThinkerForm $thinkerForm) use($closure){
                $button = $thinkerForm->footer("")->inline($closure)
                    ->button(
                        $this->getId() . "_search_button",
                        "<i class=\"layui-icon layui-icon-search thinkeradmin-button-btn\"></i>"
                    );

                $button->addAttr("lay-submit", "")->setTrimId($this->getId() . "_form_search");

                $thinkerForm->setTrimId($this->getId() . "_form");
            });
        }
        return $this->search;
    }

    /**
     * @title      toolbar
     * @description
     * @createtime 2020/5/27 2:25 下午
     * @param callable|null $closure
     * @return ThinkerToolbar|null
     * @author     yangyuance
     */
    public function toolbar(callable $closure = null) {
        if($this->thinkerToolbar == null){
            $this->thinkerToolbar = new ThinkerToolbar($this, $closure);
        }
        return $this->thinkerToolbar;
    }

    public function checkbox() {
        return $this->columns("", "")->setType("checkbox");
    }

    public function radio() {
        return $this->columns("", "")->setType("radio");
    }

    public function numbers() {
        return $this->columns("", "")->setType("numbers");
    }

    /**
     * @title       render
     * @description 每一个组件需要继承渲染接口
     * @createtime  2020/5/27 1:58 下午
     * @return string
     * @author      yangyuance
     */
    public function render()
    {
        //渲染toolbar
        if(!empty($this->thinkerToolbar)) {
            $this->thinkerToolbar->render();

            $this->setToolbar("#" . $this->getId() . "_toolbar");
        }

        if(empty($this->configs['url']) && empty($this->configs['data'])) {
            return "table config's field must have [url] when data is empty";
        }

        //渲染colsEvent
        if(!empty($this->colsEvent)){
            $this->colsEvent->render();
        }

        //判断是否使用了sort排序
        if(!empty($this->tableEvent)) {
            ThinkerAdmin::script(TemplateList::table()->Sortjs()->templates([
                $this->getId()
            ])->render());
        }

        //引用table
        ThinkerAdmin::script("tableplus", false, true);

        $columns = array_map(function (LayoutAbstract $layoutAbstract){
            return $layoutAbstract->render();
        }, $this->columns);

        ThinkerAdmin::script(TemplateList::table()->Tablejs()->templates([
            $this->getId(), json_encode($columns), $this->getConfigString()
        ])->render());

        return "<table id=\"".$this->getId()."\" lay-filter=\"".$this->getId()."\"></table>";
    }

    /**
     * @title      send
     * @description
     * @createtime 2020/5/27 5:42 下午
     * @param               $title
     * @param callable|null $callable
     * @return string|void
     * @author     yangyuance
     */
    public function send($title, callable $callable = null) {
        if(is_callable($callable)) {
            return (new ThinkerPage(function(ThinkerPage $thinkerPage) use($callable){
                $thinkerPage->setFormOrTable($this);
                call($callable, [$thinkerPage]);
            }))->setTitle($title)->render();
        } else {
            return (new ThinkerPage(function(ThinkerPage $thinkerPage) use($callable){
                $thinkerPage->card()->addBodyLayout($this)
                    ->addCardHeaderClass("padding-tb-sm");
            }))->setTitle($title)->render();
        }
    }

    //设置网址相关
    public function restful($restfulUrl) {
        return $this->setRestfulUrl($restfulUrl)->setUrl($restfulUrl);
    }

}