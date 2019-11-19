layui.define(function (exports) {
    exports('conf', {
        //版本号
        v: layui.cache.version,
        //是否开启调试模式，开启的话接口异常会抛出异常 URL信息
        debug: layui.cache.debug,
        //和界面配置相关的信息
        views: {
            //容器ID
            container: 'app',
            //容器内容ID
            containerBody: 'app-body',
            //侧标栏目id
            sidebar: 'app-sidebar',
            //记录thinker文件夹所在路径
            base: layui.cache.base,
            //记录thinker需要加载的css路径
            css: layui.cache.base + 'css/',
            //视图所在目录
            views: './',//layui.cache.base + 'views/',
            //是否开启选项卡
            viewTabs: true,
            //显示页面加载条
            viewLoadBar: true,
            //公用加载的样式
            style: [
                // layui.cache.base + "css/admin.css"
            ],
            //网站名称
            name: 'THINKER权限系统',
            //网站logo
            logo: './static/logo/logo.svg',
            //默认视图文件名
            entry: '/thinkeradmin/Show/index',//layui.cache.base.replace(".", "") + 'views/index',
            //视图文件后缀名
            engine: '.html',
            //layout的路径
            layout: layui.cache.base + 'views/layout'
        },
        //事件触发的名称
        eventName: 'thinker-event',
        //本地存储表名
        tableName: 'thinker',
        //登录 token 名称，request 请求的时候会带上此参数到 header
        tokenName: 'Access-Token',
        //request 基础URL
        requestUrl: './',
        //请求的相关参数
        request: {
            data: {},
            headers: {},
            //0的话token加载header中，1的话附带在携带参数中
            tokenType: 0,
        },
        //独立页面路由，可随意添加（无需写参数）
        indPage: [
            layui.cache.base.replace(".", "") + 'views/login', //登入页
            '/user/reg', //注册页
            '/user/forget' //找回密码
        ],
        //登录和权限判定的一些参数
        login: {
            //登录页面，当未登录或登录失效时进入
            loginPage: layui.cache.base.replace(".", "") + 'views/login',
            //注册页面
            regPage: layui.cache.base.replace(".", "") + 'views/reg',
            //忘记密码页面
            forgetPage: layui.cache.base.replace(".", "") + 'views/forget',
            //是否要强制检查登录状态， 使用tokenName进行登录验证，不通过的话会返回 loginPage 页面
            loginCheck: true,
            //根据服务器返回的 HTTP 状态码检查登录过期，设置为false不通过http返回码检查
            logoutHttpCode: false,
            //是否使用验证码
            useVercode: true,
            //是否可以注册账号
            canReg: false,
            //是否可以忘记密码
            canForget: false
        },
        //全局自定义响应字段
        response: {
            //数据状态的字段名称
            statusName: 'code',
            statusCode: {
                //数据状态一切正常的状态码
                ok: 1,
                //通过接口返回的登录过期状态码
                logout: 1001,
                //一段时间内操作过，重新注册jwt
                expired: 1002
            },
            msgName: 'msg', //状态信息的字段名称
            dataName: 'data', //数据详情的字段名称
            countName: 'count' //数据条数的字段名称，用于 table
        },
        //全局 table 配置
        //参数请参照 https://www.layui.com/doc/modules/table.html
        table: {
            page: {
                layout: ['refresh', 'prev', 'page', 'next', 'skip', 'count', 'limit']
            },
            size: 'sm',
            even: true,
            skin: 'line',
            //每页显示的条数
            limit: 10,
            limits: [10, 50, 100, 300, 500, 1000, 3000],
            //是否显示加载条
            loading: true,
            //用于对分页请求的参数：page、limit重新设定名称
            request: {
                pageName: 'page', //页码的参数名称，默认：page
                limitName: 'limit' //每页数据量的参数名，默认：limit
            }
        },
        //Table的toolbar，加上刷新
        toolbar: {
            icon: {
                refresh: {
                    title: '刷新',
                    layEvent: 'LAYTABLE_REFRESH',
                    icon: 'layui-icon-refresh'
                }
            },
            events: {
                LAYTABLE_REFRESH: function(that, _this){
                    that.reload();
                }
            }
        },
        //对应的一些接口
        apis: {
            menu: "thinkeradmin/Admin/menus",
            login: "thinkeradmin/Admin/login",
            captcha: "thinkeradmin/Admin/captcha"
        },
        //顶部栏目
        menu: {
            top: [{
                title: "切换全屏",
                icon: "layui-icon-screen-full",
                event: "fullscreen",
                css: "layui-hide-xs",
                notShow: function(){
                    return false;
                }
            }],
            list: [{
                title: "个人信息",
                event: "userinfo",
                notShow: function(){
                    return false;
                }
            }]
        },
        //后台对应的事件触发
        events: {

        },
        //第三方扩展
        extend: {
            //后台根据业务需求扩展的方法
            helper: 'lay/extends/helper',
            //生成二维码
            qrcode: 'lay/extends/qrcode',
            //生成 MD5 加密
            md5: 'lay/extends/md5',
            //生成图表
            echarts: 'lay/extends/echarts',
            echartsTheme: 'lay/extends/echartsTheme',
            //复制内容到剪贴板
            clipboard: 'lay/extends/clipboard',
            //excel带入
            excel: 'lay/extends/excel',
            xlsx: 'lay/extends/xlsx',
            FileSaver: 'lay/extends/FileSaver',
            jszip: 'lay/extends/jszip',
            //加强select
            formSelects: 'lay/extends/formSelects',
            //加强输入框
            tinymce: 'lay/extends/tinymce',
        },
    })
});
