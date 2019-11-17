layui.extend({
    view: 'lay/modules/view'
}).define(['conf', 'view', 'jquery'], function (exports) {
    window.POPUP_DATA = {};
    var conf = layui.conf,
        view = layui.view,
        element = layui.element,
        $ = layui.jquery;

    //集成自定义的组件，方便后期调用
    layui.extend(conf.extend);

    var $win = $(window), $doc = $(document), $body = $('body');

    var self = {
        route: layui.router(),
        closeOnceHashChange: false,
        ie8: view.ie8,
        get: view.request,
        appBody: null,
        shrinkCls: 'thinker-sidebar-shrink',
        isInit: false,
        routeLeaveFunc: null,
        loginToken: null,
        /**
         * 获取屏幕尺寸
         * @returns {number}
         */
        getScreenType: function(){
            var width = $win.width();
            if(width > 1200){
                return 3; //大屏幕
            } else if(width > 992){
                return 2; //中屏幕
            } else if(width > 768){
                return 1; //小屏幕
            } else {
                return 0; //超小屏幕
            }
        },
        /**
         * 离开界面的触发效果
         * @param callback
         */
        routeLeave: function (callback) {
            this.routeLeaveFunc = callback;
        },
        /**
         * session快捷操作
         */
        session: view.session,
        /**
         * 渲染界面
         * @param elem
         */
        render: function (elem) {
            if (typeof elem == 'string') elem = $('#' + elem);
            var action = elem.get(0).tagName == 'SCRIPT' ? 'next' : 'find';
            elem[action]('[is-template]').remove();
            view.parse(elem);
        },
        /**
         * 初始化界面
         */
        initPage: function () {
            //加载样式文件
            layui.each(layui.conf.views.style, function (index, url) {
                layui.link(url + '?v=' + conf.v)
            });
            this.initView(self.route)
        },
        /**
         * 初始化视图区域
         * @param route
         */
        initView: function (route) {
            //如果是默认界面或者不存在，就打开首页
            if (!self.route.href || self.route.href == '/') {
                route = this.route = layui.router('#' + conf.views.entry);
            }
            //赋值打开的界面地址
            route.fileurl = '/' + route.path.join('/');

            //判断登录页面
            if (conf.login.loginCheck == true) {
                if (this.session.token()) {
                    if (route.fileurl == conf.login.loginPage) {
                        this.navigate('/');
                        return;
                    }
                } else {
                    if (route.fileurl != conf.login.loginPage) {
                        this.session.logout();
                        return;
                    }
                }
            }

            //判断是否当前界面是独立界面
            if ($.inArray(route.fileurl, conf.indPage) === -1) {
                //非独立界面
                var loadRenderPage = function (params) {
                    if (conf.views.viewTabs == true) {
                        view.renderTabs(route)
                    } else {
                        view.render(route.fileurl)
                    }
                };

                //如果主界面都没渲染，就去渲染主界面
                if (view.containerBody == null) {
                    //加载layout文件
                    view.renderLayout(function () {
                        //重新渲染导航
                        element.render('nav', 'thinker-sidebar');
                        //加载视图文件
                        loadRenderPage()
                    })
                } else {
                    //layout文件已加载，加载视图文件
                    loadRenderPage();
                }
            } else {
                //加载单页面
                view.renderIndPage(route.fileurl, function () {
                    if (conf.views.viewTabs == true) view.tab.clear();
                })
            }
        },
        /**
         * 设置侧边栏高亮
         * @param url
         */
        sidebarFocus: function (url) {
            //传递了就按传递的走，否则去当前界面的
            url = url || this.route.href;
            //找到当前界面对应按钮
            var elem = $('#' + conf.views.sidebar)
                .find('[lay-href="' + url + '"]')
                .eq(0);
            //如果存在，就全部赋值
            if (elem.length > 0) {
                elem.parents('.layui-nav-item').addClass('layui-nav-itemed')
                elem.click()
            }
        },
        /**
         * 设置伸缩
         * @param open
         */
        flexible: function (open) {
            if (open == true) {
                view.container.removeClass(this.shrinkCls)
            } else {
                view.container.addClass(this.shrinkCls)
            }
        },
        /**
         * 时间监听定义
         * @param name
         * @param callback
         * @returns {*}
         */
        on: function (name, callback) {
            return layui.onevent(conf.eventName, 'system(' + name + ')', callback)
        },
        fire: function (name, params) {
            layui.event(conf.eventName, 'system(' + name + ')', params)
        },
        /**
         * 获得css的路径
         * @param name
         * @returns {string}
         */
        csshref: function (name) {
            name = name == undefined ? this.route.path.join('/') : name;
            return conf.views.css + 'views/' + name + '.css' + '?v=' + conf.v;
        },
        /**
         * 后退一页
         * @param n
         */
        prev: function (n) {
            if (n == undefined) n = -1;
            window.history.go(n);
        },
        /**
         * 跳转路径
         * @param url
         */
        navigate: function (url) {
            if (url == conf.views.entry) url = '/';
            window.location.hash = url;
        },
        /**
         * 弹出
         */
        modal: {
            info: function (msg, params) {
                params = params || {}
                params.titleIcoColor = params.titleIcoColor || '#5a8bff';
                params.titleIco = params.titleIco || 'exclaimination';
                params.title = [
                    '<i class="layui-icon layui-icon-' +
                    params.titleIco +
                    '" style="font-size:12px;background:' +
                    params.titleIcoColor +
                    ';display:inline-block;position:relative;top:-2px;height:24px;line-height:24px;text-align:center;width:24px;color:#fff;border-radius:50%;margin-right:10px;"></i>' +
                    (params.title || '提醒'),
                    'background:#fff;border:none;font-weight:bold;font-size:18px;color:#08132b;padding-top:20px;height:46px;line-height:46px;padding-bottom:0;'
                ];
                params = $.extend(
                    {
                        btn: ['我知道了'],
                        skin: 'layui-layer-admin-modal',
                        area: [self.getScreenType() < 1 ? '90%' : '50%'],
                        closeBtn: 0,
                        shadeClose: true
                    },
                    params
                );
                layer.alert(msg, params);
            },
            success: function (msg, params) {
                params = params || {};
                params.titleIco = 'ok';
                params.titleIcoColor = '#30d180';
                this.info(msg, params)
            },
            warn: function (msg, params) {
                params = params || {};
                params.titleIco = 'exclaimination';
                params.titleIcoColor = '#ff9900';
                this.info(msg, params)
            },
            error: function (msg, params) {
                params = params || {};
                params.titleIco = 'close';
                params.titleIcoColor = '#ed4014';
                this.info(msg, params)
            }
        },
        isUrl: function (str) {
            return /^([hH][tT]{2}[pP]:\/\/|[hH][tT]{2}[pP][sS]:\/\/)(([A-Za-z0-9-~]+)\.)+([A-Za-z0-9-~\/])+$/.test(
                str
            )
        },
        /**
         * 弹出
         */
        popup: view.popup,
        /**
         * 所有的网络请求便捷化
         */
        http: {
            request: view.request,
            _request: function(type, url, data, sucCall, errCall, throwCall){
                view.request({
                    type: type,
                    url: url,
                    data: data,
                    done: function(res){
                        typeof sucCall === 'function' && sucCall(res.code, res.msg, res.data, res);
                    },
                    complete: function(status, res){
                        if(status === 1){
                            if(typeof errCall === 'function'){
                                return errCall(res.code, res.msg, res.data, res);
                            }
                        }else{
                            if(typeof throwCall === 'function'){
                                return throwCall(status, res);
                            }
                        }
                    }
                });
            },
            //POST请求便捷化
            get: function(url, data, sucCall, errCall, throwCall){
                this._request('GET', url, data, sucCall, errCall, throwCall);
            },
            //POST请求便捷化
            post: function(url, data, sucCall, errCall, throwCall){
                this._request('POST', url, data, sucCall, errCall, throwCall);
            },
            //put请求便捷化
            put: function(url, data, sucCall, errCall, throwCall){
                this._request('PUT', url, data, sucCall, errCall, throwCall);
            },
            //delete请求便捷化
            delete: function(url, data, sucCall, errCall, throwCall){
                this._request('DELETE', url, data, sucCall, errCall, throwCall);
            }
        },
        //找到当前界面，然后刷新
        reloadTable: function(){

        }
    };

    //当小于这个尺寸的时候会进行手机端的适配
    var isMobileAdapter = false;
    //适应手机端
    function mobileAdapter() {
        self.flexible(false);
        var device = layui.device();
        if (device.weixin || device.android || device.ios) {
            //点击空白处关闭侧边栏
            $(document).on('click', '#' + conf.views.containerBody, function () {
                if (
                    self.getScreenType() < 2 &&
                    !view.container.hasClass(self.shrinkCls)
                ) {
                    self.flexible(false)
                }
            })
        }
        isMobileAdapter = true;
    }

    $win.on('resize', function (e) {
        //当界面大小变化的时候
        if (self.getScreenType() < 2) {
            if (isMobileAdapter == true) return;
            mobileAdapter()
        } else {
            isMobileAdapter = false
        }
    }).on('hashchange', function (e) {
        //移动端跳转链接先把导航关闭
        if (self.getScreenType() < 2) {
            self.flexible(false);
        }
        self.route = layui.router();
        //跳转的时候关闭所有弹出窗口
        layui.layer.closeAll();
        //初始化界面
        self.initView(self.route)
    });

    //放大缩小按钮
    var shrinkSidebarBtn = '.' + self.shrinkCls + ' #'+conf.views.sidebar+' .layui-nav-item a';

    /**
     * 所有的可以执行事件
     * @type {jQuery|any|{}}
     */
    var events = $.extend({
        /**
         * 放展缩小
         */
        flexible: function(){
            var status = view.container.hasClass(self.shrinkCls);
            self.flexible(status);
            self.session.set('flexible', status);
        },
        refresh: function(){
            var url = self.route.href;
            if (conf.views.viewTabs == true) {
                view.tab.refresh(url);
            } else {
                view.render(url)
            }
        },
        prev: function(){
            self.prev()
        },
        logout: function(){
            self.session.logout()
        },
        fullscreen: function(e){
            var normalCls = 'layui-icon-screen-full';
            var activeCls = 'layui-icon-screen-restore';
            var ico = e.find('.layui-icon');

            if (ico.hasClass(normalCls)) {
                var e = document.body;
                e.webkitRequestFullScreen
                    ? e.webkitRequestFullScreen()
                    : e.mozRequestFullScreen
                    ? e.mozRequestFullScreen()
                    : e.requestFullScreen && e.requestFullscreen();
                ico.removeClass(normalCls).addClass(activeCls);
            } else {
                var e = document;
                e.webkitCancelFullScreen
                    ? e.webkitCancelFullScreen()
                    : e.mozCancelFullScreen
                    ? e.mozCancelFullScreen()
                    : e.cancelFullScreen
                        ? e.cancelFullScreen()
                        : e.exitFullscreen && e.exitFullscreen();
                ico.removeClass(activeCls).addClass(normalCls)
            }
        }
    }, conf.events);

    //所有document的点击
    $doc.on('keydown', function (e) {
        //在屏幕中敲击回车键，需要判断是否存在form
        var ev = document.all ? window.event : e;
        if (ev.keyCode == 13) {
            var form = $(':focus').parents('.layui-form');
            form.find('[lay-submit]').click();
        }
    }).on('click', '[lay-href]', function (e) {
        //界面上点击了含有href标签的
        var href = $(this).attr('lay-href');
        var target = $(this).attr('target');
        //判断是否空标签
        if (href == '') return;
        //可以进行下一步
        function next() {
            target == '__blank' ? window.open(href) : self.navigate(href);
        }

        if ($.isFunction(self.routeLeaveFunc)) {
            self.routeLeaveFunc(self.route, href, next)
        } else {
            next()
        }
        return false;
    }).on('click', '[lay-popup]', function (e) {
        //点击了界面上的弹出
        var params = $(this).attr('lay-popup')
        self.popup(
            params.indexOf('{') === 0
                ? new Function('return ' + $(this).attr('lay-popup'))()
                : {url: params}
        );
        return false
    }).on('mouseenter', '[lay-tips]', function (e) {
        //点击了小tips
        var title = $(this).attr('lay-tips')
        var dire = $(this).attr('lay-dire') || 3;
        if (title) {
            layui.layer.tips(title, $(this), {
                tips: [dire, '#263147']
            })
        }
    }).on('mouseleave', '[lay-tips]', function (e) {
        layer.closeAll('tips');
    }).on('click', '*[' + conf.eventName + ']', function (e) {
        //点击了所有含有事件效果的
        var $this = $(this), eventName = $this.attr(conf.eventName);
        //首先判断定义的事件中是否存在，存在就去激活
        events[eventName] && events[eventName]($this);
        //触发所有的事件
        self.fire(eventName, $this);
    }).on('click', shrinkSidebarBtn, function (e) {
        //点击了侧边栏放大缩小按钮
        if (isMobileAdapter == true) return;
        var chileLength = $(this)
            .parent()
            .find('.layui-nav-child').length;
        if (chileLength > 0) {
            self.flexible(true);
            layer.closeAll('tips')
        }
    }).on('mouseenter', shrinkSidebarBtn, function (e) {
        //进入按钮
        var title = $(this).attr('title')
        if (title) {
            layui.layer.tips(title, $(this).find('.layui-icon'), {
                tips: [2, '#263147']
            })
        }
    }).on('mouseleave', shrinkSidebarBtn, function (e) {
        layer.closeAll('tips')
    });

    //判断界面大小
    if (self.getScreenType() < 2) {
        mobileAdapter()
    } else {
        var flexibleOpen = self.session.get('flexible');
        self.flexible(flexibleOpen === null ? true : flexibleOpen)
    }

    exports('admin', self)
})
