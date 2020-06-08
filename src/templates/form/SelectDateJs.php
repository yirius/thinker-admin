<?php


namespace Yirius\Admin\templates\form;


use Yirius\Admin\templates\Templates;

class SelectDateJs extends Templates
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
layui.laydate.render({
	elem: '#{$this->getConfig("id")}_laydate',
	position: 'static',
	showBottom: false,
	format: 'yyyy-M-dd',
	change: function(){
		dateSelect(window.{$this->getConfig("id")}.getValue('value'));
	},
	done: function(value){
		console.log(value)
		var values = window.{$this->getConfig("id")}.getValue('value');
		var index = values.findIndex(function(val){
			return val === value
		});
		
		if(index != -1){
			values.splice(index, 1);
		}else{
			values.push(value);
		}
		
		dateSelect(values);
		
		console.log({
			data: values.map(function(val){
				return {
					name: val,
					value: val,
					selected: true,
				}
			})
		});
		window.{$this->getConfig("id")}.update({
			data: values.map(function(val){
				return {
					name: val,
					value: val,
					selected: true,
				}
			})
		})
	},
	ready: removeAll,
})

function removeAll(){
	document.querySelectorAll('#{$this->getConfig("id")}_laydate td[lay-ymd].layui-this').forEach(function(dom){
		dom.classList.remove('layui-this');
	});
}

function dateSelect(values){
	removeAll();
	values.forEach(function(val){
		var dom = document.querySelector('#{$this->getConfig("id")}_laydate td[lay-ymd="'+val.replace(/-0([1-9])/g, '-$1')+'"]');
		dom && dom.classList.add('layui-this');
	});
}
HTML;
    }
}