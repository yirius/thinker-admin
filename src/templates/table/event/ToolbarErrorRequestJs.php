<?php


namespace Yirius\Admin\templates\table\event;


use Yirius\Admin\templates\Templates;

class ToolbarErrorRequestJs extends Templates
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
var tableBody = $("[lay-id='{$this->getConfig("id")}']").find("table").eq(1).find("tbody").find("tr");
var hasErrorIndex = [];
for(var i in data){
    hasErrorIndex.push(parseInt(i));
}
tableBody.each(function(n,v){
    if(hasErrorIndex.indexOf(n) >= 0){
        $(v).css("color", "red");
    }else{
        $(v).css("color", "");
    }
});
HTML;
    }
}