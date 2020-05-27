<?php


namespace Yirius\Admin\templates\table;


use Yirius\Admin\templates\Templates;

class SortJs extends Templates
{
    protected $args = ["id"];

    /**
     * @title      render
     * @description
     * @createtime 2020/5/26 9:18 下午
     * @return string
     * @author     yangyuance
     */
    public function render()
    {
        return <<<HTML
layui.tableplus.on('sort({$this->getConfig("id")})', function(obj){
    layui.tableplus.reload('{$this->getConfig("id")}', {
        initSort: obj,
        where: {
          sort: obj.field,
          order: obj.type || "desc"
        }
    });
});
HTML;
    }
}