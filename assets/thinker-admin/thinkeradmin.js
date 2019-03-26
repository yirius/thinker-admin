/**
 * 对参数进行定义
 */
layui.define(['jquery', 'layer', 'laytpl'], function(exports){

    var thinkerAdmin = {
        //thinkeradmin version
        v: "1.0.0",

        //登录界面的名称和进入后台后左上角名称
        name: "ThinkerAdmin",

        //登录界面第二行显示
        span: "ThinkerAdmin Login",

        //是否开启多页版本
        isIframe: false,

        //是否在调试状态
        debug: true,

        //本地储存的表
        tableName: "thinkerAdmin",

        //有事件触发的时候，显示的名称
        eventName: "thinkerAdmin",

        //携带的token名
        tokenName: 'access_token',

        //判断是否需要登录
        tokenNeed: true,

        //登录界面地址, 仅仅为后端显示地址
        login: "/login",

        //ajax参数配置
        ajax: {
            resultCodeName: "code",
            resultCode: {
                success: 1,
                logout: 1001
            },
            resultMsgName: "msg",
            resultDataName: "data",
            beforeAjax: false
        },

        //界面的相关配置
        container: {
            id: "#thinkerAdmin_app",
            body: "#thinkerAdmin_app_body",
            menu: "#thinkeradmin-side-menu",
            menuIcon: "#thinkerAdmin_flexible",
            tab: "#thinkeradmin_app_tabs",
            tabheader: "#thinkeradmin_app_tabsheader"
        },

        //视图模板的配置
        view: {
            home: "/index",
            index: "index",
            path: "./iframe/",
            suffix: ".html",
            //配置错误界面
            error: {
                notfound: "../404",
                error: "../error"
            },
            //样式界面
            theme: "../../thinkeradmin/theme",
            //登录界面
            login: "../login",
        },

        //菜单界面
        menu: {
            top: {
                "0": "theme",
                "1": "note",
                "screen-full": "fullscreen"
            },
            list: []
        },

        /**
         * 配置参数
         */
        config: {
            editpwd: "/thinkeradmin/editpwd",
            captcha: "/captcha.html",
            login: "/thinkeradmin/login",
            dyconfig: "/thinkeradmin/config"
        },

        /**
         * 需要触发的事件
         */
        events: {

        },

        //extend其他的插件
        extend: [
            'formSelects',
            'jszip',
            'xlsx',
            'FileSaver',
            'excel',
            'eleTree',
            'wangEditor'
        ],

        //table defaulttoolbar
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

        theme: {
            //内置主题配色方案
            color: [
                {"main": "#FFFFFF", "selected": "#62a8ea", "logo": "#FFFFFF", "header": "#FFFFFF", "alias": "white"},
                {"main": "#FFFFFF", "selected": "#62a8ea", "logo": "#62a8ea", "header": "#62a8ea", "alias": "primary"},
                {"main": "#20222A", "selected": "#8d6658", "logo": "#8d6658", "header": "#8d6658", "alias": "brown"},
                {"main": "#20222A", "selected": "#57c7d4", "logo": "#57c7d4", "header": "#57c7d4", "alias": "cyan"},
                {"main": "#20222A", "selected": "#46be8a", "logo": "#46be8a", "header": "#46be8a", "alias": "green"},
                {"main": "#20222A", "selected": "#757575", "logo": "#757575", "header": "#757575", "alias": "grey"},
                {"main": "#20222A", "selected": "#677ae4", "logo": "#677ae4", "header": "#677ae4", "alias": "indigo"},
                {"main": "#20222A", "selected": "#f2a654", "logo": "#f2a654", "header": "#f2a654", "alias": "orange"},
                {"main": "#20222A", "selected": "#f96197", "logo": "#f96197", "header": "#f96197", "alias": "pink"},
                {"main": "#20222A", "selected": "#926dde", "logo": "#926dde", "header": "#926dde", "alias": "purple"},
                {"main": "#20222A", "selected": "#f96868", "logo": "#f96868", "header": "#f96868", "alias": "red"},
                {"main": "#20222A", "selected": "#3aa99e", "logo": "#3aa99e", "header": "#3aa99e", "alias": "teal"},
                {"main": "#20222A", "selected": "#f7da64", "logo": "#f7da64", "header": "#f7da64", "alias": "yellow"}
            ]

            //初始的颜色索引，对应上面的配色方案数组索引
            //如果本地已经有主题色记录，则以本地记录为优先，除非请求本地数据（localStorage）
            , initColorIndex: 0
        }
    };

    thinkerAdmin.view.path = thinkerAdmin.isIframe ? "/thinker-admin/iframe/" : "./thinker-admin/spa/";

    /**
     * 定义是哪种类型的，spa单页还是iframe多页
     */
    layui.config({
        base: thinkerAdmin.isIframe ? "/thinker-admin/iframe/" : "./thinker-admin/spa/"
    });

    exports("thinkeradmin", thinkerAdmin);
});