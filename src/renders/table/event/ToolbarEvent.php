<?php


namespace Yirius\Admin\renders\table\event;


use Yirius\Admin\renders\ThinkerTable;
use Yirius\Admin\templates\TemplateList;
use Yirius\Admin\ThinkerAdmin;

class ToolbarEvent extends ViewEvent
{
    /**
     * @var ThinkerTable
     */
    protected $thinkerTable = null;

    /**
     * 事件
     * @var string
     */
    protected $event = "";

    /**
     * ColsEvent constructor.
     * @param ThinkerTable  $thinkerTable
     * @param \Closure|null $closure
     */
    public function __construct(ThinkerTable $thinkerTable, \Closure $closure = null)
    {
        parent::__construct();

        $this->thinkerTable = $thinkerTable;

        if(is_callable($closure)) call($closure, [$this]);
    }


    /**
     * @title event
     * @description
     * @createtime 2019/2/26 下午10:56
     * @param $eventName
     * @param $callback
     * @return $this
     */
    public function event($eventName, $callback)
    {
        $this->event .= <<<HTML
if(obj.event === "{$eventName}"){
    {$callback}
}
HTML;
        return $this;
    }

    /**
     * @title      add
     * @description
     * @createtime 2019/11/15 4:49 下午
     * @param null   $view
     * @param string $title
     * @param array  $config
     * @return ToolbarEvent
     * @author     yangyuance
     */
    public function add($view = null, $title = '添加信息', array $config = [])
    {
        $config = array_merge([
            'eventName' => "add",
            'area' => ['80%', '80%'],
            'id' => null,
            'data' => []
        ], $config);

        if(is_null($view)) $view = $this->thinkerTable->getOperateUrl();
        if(is_null($config['id'])) $config['id'] = $this->thinkerTable->getId() . "_adddialog";

        return $this->event($config['eventName'], $this->_popup($view, $title, $config));
    }

    /**
     * @title      delete
     * @description
     * @createtime 2019/11/15 6:22 下午
     * @param null  $url
     * @param array $sendData
     * @param null  $afterDelete
     * @return ToolbarEvent
     * @author     yangyuance
     */
    public function delete($url = null, array $sendData = [], $afterDelete = null)
    {
        if(is_null($url)) $url = $this->thinkerTable->getRestfulUrl();
        if(is_null($afterDelete)) $afterDelete = "layer.msg(res.msg || '已删除')";

        return $this->event(
            "delete",
            $this->_multiverify(
                "确定删除选中数据?",
                $url,
                $sendData,
                "delete",
                null,
                $afterDelete,
                null
            )
        );
    }

    public function xlsx($url, $parseData = '', array $sendData = [], array $config = [])
    {
        ThinkerAdmin::script("excel", false, true);

        $config = array_merge([
            'afterReload' => '',
            'afterRequest' => 'layui.admin.reloadCurrentTable(); parent.layui.layer.closeAll(); parent.layui.layer.msg(msg);',
            'errorRequest' => TemplateList::table()->event()->ToolbarErrorRequestJs()->templates([

            ])->render(),
            'method' => 'post'
        ], $config);

        $parseData = empty($parseData) ? "" : str_replace(["\n", "\r", "'"], "", $parseData);

        ThinkerAdmin::script(
            TemplateList::table()->event()->ToolbarInputChange()->templates([
                $this->thinkerTable->getId()."_xlsximport",
                $this->thinkerTable->getId(),
                $parseData,
                $config['afterRequest']
            ])->render()
        );

        $this->event("submitexcel", TemplateList::table()->event()->ToolbarSubmitXlsxJs()->templates([
            strtolower($config['method']),
            $url,
            json_encode($sendData),
            $config['afterRequest'],
            $config['errorRequest']
        ])->render());

        return $this->event("importexcel", <<<HTML
$("#{$this->thinkerTable->getId()}_xlsximport").click();
HTML
        );
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
        ThinkerAdmin::script(<<<HTML
layui.tableplus.on('toolbar({$this->thinkerTable->getId()})', function(obj){
{$this->event}
});
HTML
        );

        return "";
    }
}