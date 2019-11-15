<?php


namespace Yirius\Admin\table;


use Yirius\Admin\extend\ThinkerLayout;
use Yirius\Admin\layout\ThinkerPage;
use Yirius\Admin\ThinkerAdmin;

/**
 * Class ThinkerTable
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
 * @method ThinkerTable setTitle($title);
 * @method ThinkerTable setText($text);
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
 *
 * @method String getRestfulUrl(); 获取到对应的restful网址
 *
 * @method ThinkerTable setOperateUrl($url); 设置对应的add或者edit操作网址
 * @method String getOperateUrl(); 获取到对应的add或者edit操作网址
 * @package Yirius\Admin\table
 */
class ThinkerTable extends ThinkerLayout
{
    /**
     * 对应的restful网址
     * @var string
     */
    protected $restfulUrl = "";

    /**
     * 添加或修改对应的界面
     * @var string
     */
    protected $operateUrl = "";

    /**
     * Columns list
     * @var array
     */
    protected $columns = [];

    /**
     * ThinkerTable constructor.
     * @param callable|null $callback
     */
    public function __construct(callable $callback = null)
    {
        parent::__construct();

        //判断是否可以执行
        if (is_callable($callback)) {
            call($callback, [$this]);
        }
    }

    /**
     * @title      restful
     * @description 设置restful的路径
     * @createtime 2019/11/14 5:20 下午
     * @param $url
     * @return $this
     * @author     yangyuance
     */
    public function restful($url)
    {
        $this->restfulUrl = $url;

        $this->setUrl($url);

        return $this;
    }

    /**
     * @title      columns
     * @description
     * @createtime 2019/11/14 6:43 下午
     * @param $field
     * @param $name
     * @return ThinkerTableCols
     * @author     yangyuance
     */
    public function columns($field, $name)
    {
        $columns = (new ThinkerTableCols($field, $name, $this));

        $this->columns[] = $columns;

        return $columns;
    }

    /**
     * @title       render
     * @description 每一个组件需要继承渲染接口
     * @createtime  2019/11/14 4:26 下午
     * @return string
     * @author      yangyuance
     */
    public function render()
    {
        //judge table server url
        if(!isset($this->config['url']) && !isset($this->config['data']))
        {
            return "table config's field must have [url] when data is empty";
        }

        $columns = [];
        for($i = 0; $i < count($this->columns); $i++){
            $columns[] = $this->columns[$i]->render();
        }

        $columns = json_encode($columns);

        //引用table
        ThinkerAdmin::script("table", false, true);

        $jsonConfig = json_encode($this->getConfig());

        //组合table使用的script
        ThinkerAdmin::script(<<<HTML
var _searchField = {};
layui.form.on('submit({$this->getId()}_form_search)', function (data) {
    _searchField = data.field;
    //执行重载
    layui.table.reload('{$this->getId()}', {
        where: _searchField
    });
});
var _{$this->getId()}_ins = layui.table.init('{$this->getId()}', $.extend({
    response: {
        statusName: layui.conf.response.statusName,
        statusCode: layui.conf.response.statusCode.ok,
        msgName: layui.conf.response.msgName,
        countName: layui.conf.response.countName,
        dataName: layui.conf.response.dataName
    },
    cols: [{$columns}]
}, layui.conf.table, {$jsonConfig}));
HTML
        );

        return <<<HTML
<table id="{$this->getId()}" lay-filter="{$this->getId()}"></table>
HTML
            ;
    }

    /**
     * @title      send
     * @description
     * @createtime 2019/11/15 12:18 下午
     * @param callable|null $callable
     * @return string
     * @author     yangyuance
     */
    public function send(callable $callable = null)
    {
        if (is_callable($callable)) {
           return  (new ThinkerPage(function($page) use($callable){
               call($callable, [$page, $this]);
            }))->render();
        } else {
            return (new ThinkerPage(function(ThinkerPage $page){
                $page->card()->setBodyLayout($this);
            }))->render();
        }
    }
}