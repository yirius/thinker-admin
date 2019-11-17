;layui.define(['form', 'table'], function(exports){
    var $ = layui.jquery;

    if(typeof layui.colorpicker !== "undefined"){
        function getColorCallback(str){
            var onChange = 'function(value){' + str + '}';
            return new Function("return " + onChange)();
        }
        $("div[lay-colorpicker]").each(function(n, v){
            var options = $(v).data();
            if(options.colors) options.colors = JSON.parse(options.colors.replace(/'/g, '"'));
            if(options.change) options.change = getColorCallback(options.change);
            if(options.done) options.done = getColorCallback(options.done);
            layui.colorpicker.render($.extend({
                elem: v,
                done: function (value) {
                    $("#" + v.id + "_hidden").val(value);
                }
            }, options));
        });
    }

    if(typeof layui.formSelects !== "undefined"){
        var formSelects = layui.formSelects;
        function getTemplateCallback(str){
            var onChange = 'function(name, value, selected, disabled){return ' + str + '}';
            return new Function("return " + onChange)();
        }
        $("select[xm-select]").each(function(n, v){
            var options = $.extend({}, $(v).data());
            if(options.template) options.template = getTemplateCallback(options.template);

            v.formSelects = formSelects.render($(v).attr("xm-select"), options);
        });
    }

    if(typeof layui.laydate !== "undefined"){
        function getDateCallback(str){
            var onChange = 'function(value, date, endDate){' + str + '}';
            return new Function("return " + onChange)();
        }
        $("input[lay-date]").each(function(n, v){
            var options = $.extend({elem: v}, $(v).data());
            if(options.ready) options.ready = getDateCallback(options.ready);
            if(options.change) options.change = getDateCallback(options.change);
            if(options.done) options.done = getDateCallback(options.done);

            v.laydateIndex = layui.laydate.render(options);
        });
    }

    if(typeof layui.tinymce !== "undefined"){
        $("textarea[lay-tinymce]").each(function(n, v){
            v.tinymce = layui.tinymce.render({
                elem: "#" + v.id,
                height: 400
            });
        });
    }

    if(typeof layui.slider !== "undefined"){
        function getSliderCallback(str){
            var onChange = 'function(value){' + str + '}';
            return new Function("return " + onChange)();
        }
        $("div[lay-slider]").each(function(n, v){
            var options = $(v).data();
            if(options.change) options.change = getColorCallback(options.change);
            if(options.setTips) options.setTips = getColorCallback(options.setTips);
            layui.slider.render($.extend({
                elem: v,
                done: function (value) {
                    $("#" + v.id + "_hidden").val(value);
                }
            }, options));
        });
    }

    //渲染form
    layui.form.render();

    exports("common", {});
});