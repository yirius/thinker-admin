/**
 * Author: Yirius
 */
layui.extend({
    loadBar: 'lay/modules/loadBar',
    dropdown: 'lay/modules/dropdown'
}).define(['jquery', 'laytpl', 'element', 'form', 'loadBar', 'dropdown'], function (exports) {
        var $ = layui.jquery,
            laytpl = layui.laytpl,
            conf = layui.conf,
            loadBar = layui.loadBar,
            dropdown = layui.dropdown;

        var $win = $(window), $doc = $(document), $body = $('body');

        //判断是否IE8
        var isIE8 = navigator.appName == 'Microsoft Internet Explorer' &&
            navigator.appVersion.split(';')[1].replace(/[ ]/g, '') == 'MSIE8.0';

        var self = {
            ie8: isIE8,
            container: $('#' + conf.views.container),
            containerBody: null,
            /**
             * 字符串是否含有html标签的检测
             * @param htmlStr
             */
            checkHtml: function(htmlStr){
                var reg = /<[^>]+>/g
                return reg.test(htmlStr);
            },
            /**
             * 转圈或不转
             */
            loading: function (elem) {
                elem.append(
                    (this.elemLoad = $(
                        '<i class="layui-anim layui-anim-rotate layui-anim-loop layui-icon layui-icon-loading thinker-loading"></i>'
                    ))
                )
            },
            loaded: function(){
                this.elemLoad && this.elemLoad.remove();
            },
            /**
             * 设置doc的标题
             * @param title
             */
            setTitle: function (title) {
                $doc.attr({title: title + ' - ' + conf.views.name});
            },
            /**
             * 清空内容
             */
            clear: function () {
                self.containerBody.html('')
            },
            /**
             * 弹出界面
             * @param options
             * @returns {*}
             */
            popup: function(options){
                var success = options.success,skin = options.skin;

                delete options.success;
                delete options.skin;

                return layui.layer.open($.extend({
                    type: 1,
                    title: '温馨提示',
                    content: '',
                    id: 'LAY-system-view-popup',
                    skin: 'layui-layer-admin' + (skin ? ' ' + skin : ''),
                    shadeClose: true,
                    // closeBtn: false,
                    success: function(layero, index){
                        var elemClose = $('<i class="layui-icon" close>&#x1006;</i>');
                        layero.append(elemClose);
                        elemClose.on('click', function(){
                            layer.close(index);
                        });
                        typeof success === 'function' && success.apply(this, arguments);
                    }
                }, options))
            },
            error: function(content, options){
                return this.popup($.extend({
                    content: content,
                    maxWidth: 300,
                    shade: 0.01,
                    offset: 't',
                    anim: 6,
                    id: 'Thinker_adminError'
                }, options));
            },
            /**
             * session的便捷操作
             */
            session: {
                get: function(name){
                    if(!this.has(name)){
                        return null;
                    }else{
                        return layui.data(conf.tableName)[name];
                    }
                },
                has: function(name){
                    var tableData = layui.data(conf.tableName);
                    return (name in tableData);
                },
                set: function(name, value){
                    layui.data(conf.tableName, {
                        key: name,
                        value: value
                    });
                },
                remove: function(name){
                    layui.data(conf.tableName, {
                        key: name,
                        remove: true
                    });
                },
                clear: function(){
                    layui.data(conf.tableName, null);
                },
                token: function(){
                    return this.get(conf.tokenName) || '';
                },
                login: function(token, data){
                    this.set(conf.tokenName, token);

                    if ($.isPlainObject(data)) {
                        layui.each(data, function (key) {
                            self.session.set(key, data[key]);
                        });
                    }
                },
                logout: function(){
                    //清空本地记录的 token
                    this.remove(conf.tokenName);
                    //跳转到登入页
                    location.hash = conf.login.loginPage;
                }
            },
            /**
             * 私有方法，分析请求参数
             * @param options
             * @returns {*}
             * @private
             */
            _parseRequest: function(options){
                //给data以及headers赋值
                options.data = options.data || {};
                options.headers = options.headers || {};

                //将data转化为object，不能出现字符串
                try{
                    if(typeof options.data === 'string'){
                        options.data = JSON.parse(options.data);
                    }
                }catch (e) {
                    //如果开启调试，就输出错误
                    if(conf.debug){console.error(e);}
                    //将数据制成空
                    options.data = {};
                }

                //合并一下conf.request内容
                options.data = $.extend(options.data, conf.request.data);
                options.headers = $.extend(options.headers, conf.request.headers);

                //如果tokenName不为空
                if(conf.tokenName){
                    //加载在headers中或data中
                    var tokenType = conf.request.tokenType == 0 ? 'headers' : 'data';
                    //判断是否已经存在了这个值，存在了就就不覆盖
                    if(typeof options[tokenType][conf.tokenName] == "undefined"){
                        options[tokenType][conf.tokenName] = self.session.token();
                    }
                }

                return options;
            },
            /**
             * 请求的基础参数
             * @param options
             * @returns {*|jQuery|{getAllResponseHeaders, abort, setRequestHeader, readyState, getResponseHeader, overrideMimeType, statusCode}}
             */
            request: function(options){
                var _this = this;

                //处理一下参数
                options = self._parseRequest(options);

                //删除两次信息
                delete options['success'];
                delete options['error'];

                return $.ajax($.extend({
                    type: 'get',
                    dataType: 'json',
                    success: function(res){
                        var statusCode = conf.response.statusCode;

                        //登录状态失效，清除本地 access_token，并强制跳转到登入页
                        if(res[conf.response.statusName] == statusCode.logout){
                            self.session.logout();
                        }else if(res[conf.response.statusName] == statusCode.expired){
                            //重新记录token，然后重新发送记录
                            var token = res[conf.response.dataName][conf.tokenName];
                            delete res[conf.response.dataName][conf.tokenName];
                            //重新记录token
                            self.session.login(token, res[conf.response.dataName]);

                            //重新发送当次的数据, 需要删除token对应字段
                            if(conf.tokenName){
                                //如果存在token，且有对应字段，就删除
                                var tokenType = conf.request.tokenType == 0 ? 'headers' : 'data';
                                //判断是否已经存在了这个值，存在了就就不覆盖
                                if(typeof options[tokenType][conf.tokenName] != "undefined"){
                                    delete options[tokenType][conf.tokenName];
                                }
                            }
                            //重新请求
                            self.request(options);
                        }else if(res[conf.response.statusName] == statusCode.ok) {
                            //只有 response 的 code 一切正常才执行 done
                            typeof options.done === 'function' && options.done(res);
                        }else {
                            //判断是否存在错误的执行
                            var notShowError = false;
                            if(typeof options.complete === 'function'){
                                notShowError = options.complete(1, res) || false;
                            }
                            //如果不存在或者需要提示文字
                            if(!notShowError){
                                //其它异常, 直接提示
                                self.error(
                                    '<cite>Error：</cite> ' + (res[conf.response.msgName] || '返回状态码异常')
                                );
                            }
                        }

                        //只要 http 状态码正常，无论 response 的 code 是否正常都执行 complete
                        // typeof options.complete === 'function' && options.complete(1, res);
                    },
                    error: function(e, code){
                        self.error(
                            '请求异常，服务器貌似出现一些问题<br><cite>错误信息：</cite>'+ code
                        );

                        typeof options.complete === 'function' && options.complete(0, code);
                    }
                }, options));
            },
            /**
             * 开始分析模板
             * @param container
             */
            parse: function(container){
                if (!container) container = self.containerBody;

                var _this = this, router = layui.router();

                //找到所有的模板
                var templates = container.get(0).tagName == 'SCRIPT' ? container : container.find('[template]');

                //渲染模板
                var renderTemplate = function (curTpl, data, callback) {
                    laytpl(curTpl.html()).render(data, function (html) {
                        try {
                            html = $(
                                self.checkHtml(html) ? html : '<span>' + html + '</span>'
                            )
                        } catch (err) {
                            html = $('<span>' + html + '</span>')
                        }

                        //赋值，是否template
                        html.attr('is-template', true);
                        //在当前模板之后，添加html
                        curTpl.after(html);
                        //判断是否方法，然后触发
                        if ($.isFunction(callback)){
                            callback(html);
                        }
                    })
                };

                layui.each(templates, function (index, item) {
                    item = $(item);
                    //获取回调
                    var layDone = item.attr('lay-done') || item.attr('lay-then'),
                        //接口 url
                        url = laytpl(item.attr('lay-url')|| '').render(router),
                        type = laytpl(item.attr('lay-type')|| 'POST').render(router),
                        //接口参数
                        data = laytpl(item.attr('lay-data')|| '').render(router),
                        //接口请求的头信息
                        headers = laytpl(item.attr('lay-headers')|| '').render(router);

                    //拼装参数
                    try {
                        data = new Function('return '+ data + ';')();
                    } catch(e) {
                        if(conf.debug){console.error(item, 'lay-data: ' + e.message);}
                        data = {};
                    }

                    try {
                        headers = new Function('return '+ headers + ';')();
                    } catch(e) {
                        if(conf.debug){console.error(item, 'lay-headers: ' + e.message);}
                        headers = headers || {};
                    }

                    //如果存在对外接口，需要拼装一下
                    if (url) {
                        //进行AJAX请求
                        _this.request({
                            url: url,
                            type: type,
                            data: data,
                            header: headers,
                            done: function (res) {
                                // templateData = data
                                renderTemplate(item, res[conf.response.dataName]);
                                try{
                                    if (layDone) (new Function(layDone)());
                                }catch (e) {
                                    if(conf.debug){console.error(item + "layDone error: " + e);}
                                }
                            }
                        })
                    } else {
                        renderTemplate(item, {}, self.ie8 ? function (elem) {
                            if (elem[0] && elem[0].tagName != 'LINK') return;
                            container.hide();
                            elem.load(function () {
                                container.show();
                            })
                        } : null);

                        try{
                            if (layDone) (new Function(layDone)());
                        }catch (e) {
                            if(conf.debug){console.error(item + "layDone error: " + e);}
                        }
                    }
                })
            },
            /**
             * 载入HTML界面
             * @param url
             * @param callback
             */
            loadHtml: function (url, callback) {
                var _this = this;
                //判断url是否存在，不存在就使用默认首页
                url = url || conf.views.entry;
                //判断传递的url是否存在参数, 同时拼接
                var queryIndex = url.indexOf('?');
                url = (url.indexOf(conf.views.base) === 0 ? '' : conf.views.views) + url;
                //有参数需要拼接
                if (queryIndex !== -1){
                    var splitTmp = url.split("?");
                    url = splitTmp[0] + conf.views.engine + "?v=" + layui.cache.version + "&" + splitTmp[1];
                }else{
                    url = url + conf.views.engine + "?v=" + layui.cache.version;
                }
                //开始加载进度条
                loadBar.start();
                //只能采用原生ajax
                $.ajax(self._parseRequest({
                    url: url,
                    type: 'get',
                    dataType: 'html',
                    success: function(res){
                        //判断是不是json文件
                        if(res.substr(0, 1) == "{"){
                            var statusCode = conf.response.statusCode;
                            var jsonRes = JSON.parse(res);
                            //登录状态失效，清除本地 access_token，并强制跳转到登入页
                            if(jsonRes[conf.response.statusName] == statusCode.logout){
                                loadBar.finish();
                                self.session.logout();
                            }else if(jsonRes[conf.response.statusName] == statusCode.expired){
                                //重新记录token，然后重新发送记录
                                var token = jsonRes[conf.response.dataName][conf.tokenName];
                                delete jsonRes[conf.response.dataName][conf.tokenName];
                                //重新记录token
                                self.session.login(token, jsonRes[conf.response.dataName]);

                                //重新请求
                                loadBar.finish();
                                self.loadHtml(url, callback);
                            } else{
                                loadBar.error();
                                self.error('请求视图文件异常\n文件路径：' + url + '\n状态：' + jsonRes[conf.response.msgName]);
                            }
                        }else{
                            callback({html: res, url: url});
                            loadBar.finish();
                        }
                    },
                    error: function(res){
                        self.error('请求视图文件异常\n文件路径：' + url + '\n状态：' + res.status);
                        loadBar.error();
                    }
                }));
            },
            /**
             * 填充html内容
             * @param url
             * @param htmlElem
             * @param modeName
             * @returns {{htmlElem: *, title: *, url: *}}
             */
            fillHtml: function (url, htmlElem, modeName) {
                var fluid = htmlElem.find('.layui-fluid[lay-title]');
                var title = '';
                if (fluid.length > 0) {
                    title = fluid.attr('lay-title');
                    self.setTitle(title)
                }

                var container = self.containerBody || self.container;

                container[modeName](htmlElem.html());

                if (modeName == 'prepend') {
                    self.parse(container.children('[lay-url="' + url + '"]'))
                } else {
                    self.parse(container)
                }

                //重新对面包屑进行渲染
                layui.element.render('breadcrumb', 'thinker-breadcrumb');
                return {title: title, url: url, htmlElem: htmlElem};
            },
            /**
             * 渲染上方的tab栏目
             */
            tab: {
                isInit: false,
                data: [],
                tabMenuTplId: 'TPL-app-tabsmenu',
                minLeft: null,
                maxLeft: null,
                wrap: '.thinker-tabs-wrap',
                menu: '.thinker-tabs-menu',
                next: '.thinker-tabs-next',
                prev: '.thinker-tabs-prev',
                step: 200,
                init: function () {
                    var tab = this
                    var btnCls = tab.wrap + ' .thinker-tabs-btn'

                    layui.dropdown.render({
                        elem: '.thinker-tabs-down',
                        click: function (name) {
                            if(name == 'all'){
                                tab.delAll();
                            }else if(name == 'other'){
                                tab.delOther()
                            }else if(name == "current"){
                                //删除当前的
                                tab.del(
                                    $(".thinker-tabs-menu").find(".thinker-tabs-active").attr("lay-url")
                                );
                            }
                        },
                        options: [{
                            name: 'current',
                            title: '关闭当前选项卡'
                        }, {
                            name: 'other',
                            title: '关闭其他选项卡'
                        }, {
                            name: 'all',
                            title: '关闭所有选项卡'
                        }]
                    });

                    $doc.on('click', btnCls, function (e) {
                        var url = $(this).attr('lay-url')
                        if ($(e.target).hasClass('thinker-tabs-close')) {
                            tab.del(url)
                        } else {
                            var type = $(this).attr('data-type');
                            if (type == 'page') {
                                tab.change(tab.has(url));
                            } else if (type == 'prev' || type == 'next') {
                                tab.menuElem = $(tab.menu);
                                var menu = tab.menuElem;
                                tab.minLeft = tab.minLeft || parseInt(menu.css('left'));
                                tab.maxLeft = tab.maxLeft || $(tab.next).offset().left;

                                var left = 0;
                                if (type == 'prev') {
                                    left = parseInt(menu.css('left')) + tab.step;
                                    if (left >= tab.minLeft) left = tab.minLeft;
                                } else {
                                    left = parseInt(menu.css('left')) - tab.step;
                                    var last = menu.find('li:last');
                                    if (last.offset().left + last.width() < tab.maxLeft) return
                                }
                                menu.css('left', left)
                            }
                        }
                    })

                    $('.thinker-tabs-hidden').addClass('layui-show');
                    this.isInit = true
                },
                has: function (url) {
                    var exists = false
                    layui.each(this.data, function (i, data) {
                        if (data.fileurl == url) return (exists = data)
                    })
                    return exists
                },
                delAll: function (type) {
                    var tab = this
                    var menuBtnClas = tab.menu + ' .thinker-tabs-btn'
                    $(menuBtnClas).each(function () {
                        var url = $(this).attr('lay-url')
                        if (url === conf.views.entry) return true
                        tab.del(url)
                    })
                },
                delOther: function () {
                    var tab = this
                    var menuBtnClas = tab.menu + ' .thinker-tabs-btn'
                    $(menuBtnClas + '.thinker-tabs-active')
                        .siblings()
                        .each(function () {
                            var url = $(this).attr('lay-url')
                            tab.del(url)
                        })
                },
                del: function (url, backgroundDel) {
                    var tab = this
                    if (tab.data.length <= 1 && backgroundDel === undefined) return
                    layui.each(tab.data, function (i, data) {
                        if (data.fileurl == url) {
                            tab.data.splice(i, 1)
                            return true
                        }
                    })

                    var lay = '[lay-url="' + url + '"]'
                    var thisBody = $(
                        '#' + conf.views.containerBody + ' > .thinker-tabs-item' + lay
                    )
                    var thisMenu = $(this.menu).find(lay)
                    thisMenu.remove();
                    thisBody.remove();

                    if (backgroundDel === undefined) {
                        if (thisMenu.hasClass('thinker-tabs-active')) {
                            $(this.menu + ' li:last').click()
                        }
                    }
                },
                refresh: function (url) {
                    url = url || layui.admin.route.fileurl
                    if (this.has(url)) {
                        this.del(url, true)
                        self.renderTabs(url)
                    }
                },
                clear: function () {
                    this.data = []
                    this.isInit = false
                    $(document).off('click', this.wrap + ' .thinker-tabs-btn')
                },
                change: function (route, callback) {
                    if (typeof route == 'string') {
                        route = layui.router('#' + route)
                        route.fileurl = '/' + route.path.join('/')
                    }
                    var fileurl = route.fileurl
                    var tab = this
                    if (tab.isInit == false) tab.init()

                    var changeView = function (lay) {
                        $('#' + conf.views.containerBody + ' > .thinker-tabs-item' + lay)
                            .show()
                            .siblings()
                            .hide()
                    };

                    var lay = '[lay-url="' + fileurl + '"]';

                    var activeCls = 'thinker-tabs-active'

                    var existsTab = tab.has(fileurl)
                    if (existsTab) {
                        var menu = $(this.menu)
                        var currentMenu = menu.find(lay)

                        if (existsTab.href !== route.href) {
                            tab.del(existsTab.fileurl, true)
                            tab.change(route)
                            return false
                            //tab.del(route.fileurl)
                        }
                        currentMenu
                            .addClass(activeCls)
                            .siblings()
                            .removeClass(activeCls)

                        changeView(lay)

                        this.minLeft = this.minLeft || parseInt(menu.css('left'))

                        var offsetLeft = currentMenu.offset().left
                        if (offsetLeft - this.minLeft - $(this.next).width() < 0) {
                            $(this.prev).click()
                        } else if (offsetLeft - this.minLeft > menu.width() * 0.5) {
                            $(this.next).click()
                        }
                        $(document).scrollTop(-100)

                        layui.admin.navigate(route.href)
                    } else {
                        self.loadHtml(fileurl, function (res) {
                            var htmlElem = $(
                                "<div><div class='thinker-tabs-item' lay-url='" +
                                fileurl +
                                "'>" +
                                res.html +
                                '</div></div>'
                            );
                            var params = self.fillHtml(fileurl, htmlElem, 'prepend');
                            route.title = params.title;
                            tab.data.push(route);
                            layui.admin.render(tab.tabMenuTplId);

                            var currentMenu = $(tab.menu + ' ' + lay);
                            currentMenu.addClass(activeCls);

                            changeView(lay)

                            if ($.isFunction(callback)) callback(params)
                        })
                    }

                    layui.admin.sidebarFocus(route.href);
                },
                onChange: function () {
                }
            },
            /**
             * 渲染界面
             * @param fileurl
             * @param callback
             */
            render: function (fileurl, callback) {
                self.loadHtml(fileurl, function (res) {
                    var htmlElem = $('<div>' + res.html + '</div>');
                    var params = self.fillHtml(res.url, htmlElem, 'html');
                    if ($.isFunction(callback)) callback(params)
                })
            },
            /**
             * 渲染并加载tab文件
             * @param route
             * @param callback
             */
            renderTabs: function (route, callback) {
                this.tab.change(route, callback)
            },
            /**
             * 渲染主界面
             * @param callback
             * @param url
             */
            renderLayout: function (callback, url) {
                if (!url) url = conf.views.layout;
                this.containerBody = null;

                this.render(url, function (res) {
                    self.containerBody = $('#' + conf.views.containerBody);
                    //如果打开tabs
                    if (conf.views.viewTabs == true) {
                        self.containerBody.addClass('thinker-tabs-body')
                    }
                    layui.admin.appBody = self.containerBody;
                    if ($.isFunction(callback)) callback();
                })
            },
            /**
             * 渲染单独界面
             * @param fileurl
             * @param callback
             */
            renderIndPage: function (fileurl, callback) {
                self.renderLayout(function () {
                    self.containerBody = null;
                    if ($.isFunction(callback)) callback();
                }, fileurl);
            }
        };

        exports('view', self)
    }
)
