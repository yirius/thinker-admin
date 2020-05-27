<?php


namespace Yirius\Admin\templates\form;


use Yirius\Admin\templates\Templates;

class TreeJs extends Templates
{
    protected $args = ["useJsName", "tree"];

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
;(function(){
    var currentEleTree = document.querySelector("#@tree.getId()"), isLoaded = false,
        currentTreeInput = layui.jquery("#@tree.getId()_input"), config = @raw(tree.getConfigString());

    var checkedValue = [], _eachChecked = function(data){
        layui.each(data, function(n, v){
            checkedValue.push(v.value);
            if(v.children && v.children.length !== 0){
                _eachChecked(v.children);
            }
        });
    };

    if(config.data) {
        function replaceData(data) {
            layui.each(data, function (n, v) {
                if(v.text) {
                    v.title = v.text;
                    delete v.text;
                }
                if(v.value) {
                    v.id = v.value;
                }
                if(v.childs && v.childs.length > 0) {
                    v.children = v.childs;
                    delete v.childs;
                    replaceData(v.children);

                    if(v.checked) {
                        if(!v.children || v.children.length > 0) {
                            checkedValue.push(v.value);
                        }
                        v.checked = false;
                    }
                }
            })
        }

        replaceData(config.data);
    }

    currentEleTree['{$this->getConfig("useJsName")}'] = layui['{$this->getConfig("useJsName")}'].render($.extend({
        elem: "#@tree.getId()",
        id: "@tree.getId()",
        click: function(obj){
            @raw(tree.getClickEvent())
        },
        oncheck: function(obj){
            if(isLoaded){
                checkedValue = [];
                _eachChecked(layui['{$this->getConfig("useJsName")}'].getChecked('@tree.getId()'));
                currentTreeInput.val(checkedValue.join(","));
                @raw(tree.getCheckedEvent())
            }
        },
        beforeOperate: function(type, obj){
            @raw(tree.getBeforeOperateEvent())
        },
        operate: function(obj){
            @raw(tree.getOperateEvent())
        }
    }, config));

    //赋值
    if(checkedValue.length > 0) {
        layui['{$this->getConfig("useJsName")}'].setChecked('@tree.getId()', checkedValue);
    }
//防止错误触发
    isLoaded = true;
    layui.form.render();
})();
HTML;
    }
}