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
            v.tinymce = layui.tinymce.render($.extend({
                elem: "#" + v.id,
                height: 400
            }, $(v).data()));
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
        $("a[lay-upload]").each(function(n, v){
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

    //渲染form
    layui.form.render();

    exports("common", {});
});