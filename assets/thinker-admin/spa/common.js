layui.define(['form'], function(exports){
    var $ = layui.jquery;

    if(layui.thinkeradmin.debug){
        console.log("common init");
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

    if(typeof layui.upload !== "undefined"){
        var upload = layui.upload;
        function _createImageElement(name, path, index, isShow){
            var suffixIndex = path.lastIndexOf(".");
            var suffix = path.substring(suffixIndex+1).toUpperCase();
            var notImage = (suffix!=="BMP"&&suffix!=="JPG"&&suffix!=="JPEG"&&suffix!=="PNG"&&suffix!=="GIF");

            return '<dd class="item_img" id="thinkeradmin_upload_' + index + '">' +
                '<div class="operate">' +
                '<i class="thinkeradmin-upload-close layui-icon layui-icon-delete"></i>' +
                '</div>' +
                (notImage ?
                    '<a href="' + path + '">' + path + '</a>':
                    '<img src="' + path + '" class="img" ' + (isShow ? 'href="' + path + '" data-fancybox=""' : '') + '>') +
                '<input type="hidden" name="' + name +'" value="' + path + '" />' +
                '</dd>';
        }
        //common upload
        $("button[lay-upload]").each(function(n, v){
            var currentEle = $(v);

            v.currentIndex = 1;
            var isMultiImage = currentEle.data('multiple') || false,
                isShow = currentEle.data('isshow') || false;

            var findThinkerList = null;
            currentEle.nextAll().each(function(j, val){
                if($(val).hasClass("thinkeradmin-upload-list")){
                    findThinkerList = $(val);
                }
            });

            var options = $.extend({
                elem: v,
                before: function(obj) {
                    layer.msg('文件上传中...', {icon: 16, shade: 0.01, time: 0});
                },
                done: function(res) {
                    layer.close(layer.msg());
                    for(var i in res.data){
                        findThinkerList[isMultiImage ? 'append' : 'html'](_createImageElement(
                            currentEle.data('name') + (isMultiImage ? '[]' : ''),
                            res.data[i],
                            v.currentIndex,
                            isShow
                        ));
                        v.currentIndex++;
                    }
                }
            }, currentEle.data());

            v.upload = upload.render(options);
        });

        //add event
        $(document)
            .off("click", ".thinkeradmin-upload-close")
            .on("click", ".thinkeradmin-upload-close", function(){
                $(this).parent().parent().remove();
            });
    }

    if(typeof layui.wangEditor !== "undefined"){
        $("div[lay-wangeditor]").each(function(n, v){
            var editor = new layui.wangEditor(v);
            //judge data[]
            var textarea = $(v).prev();
            editor.customConfig.onchange = function (html) {
                textarea.val(html)
            };
            //append data for customer
            var datas = $(v).data();
            for(var i in datas){
                editor.customConfig[i] = datas[i];
            }
            editor.create();
        });
    }

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

    layui.form.render();

    exports("common", {});
});