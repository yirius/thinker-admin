<?php


namespace Yirius\Admin\templates\admin;


use Yirius\Admin\templates\Templates;

class RedisInfo extends Templates
{
    protected $args = ["data"];

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

<style>
    #thinker-redis-terminal .layui-card {
        border-radius: 4px 4px 0 0;
    }

    #thinker-redis-terminal textarea, #thinker-redis-terminal input, #thinker-redis-terminal button {
        outline: none
    }

    #thinker-redis-terminal .window-button, #thinker-redis-terminal .window .buttons .closes, #thinker-redis-terminal .window .buttons .minimize, #thinker-redis-terminal .window .buttons .maximize {
        padding: 0;
        margin: 0 6px 0 0;
        width: 12px;
        height: 12px;
        background-color: gainsboro;
        border: 1px solid rgba(0, 0, 0, 0);
        border-radius: 6px;
        color: rgba(0, 0, 0, 0.5)
    }

    #thinker-redis-terminal .window .handle {
        height: 38px;
        font-family: 'consolas', serif;
        font-size: 13px;
        line-height: 38px;
        text-align: center
    }

    #thinker-redis-terminal .window .buttons {
        position: absolute;
        float: left;
        margin: 0 8px
    }

    #thinker-redis-terminal .window .buttons .closes {
        background-color: #fc625d
    }

    #thinker-redis-terminal .window .buttons .minimize {
        background-color: #fdbc40
    }

    #thinker-redis-terminal .window .buttons .maximize {
        background-color: #35cd4b
    }

    #thinker-redis-terminal .window .terminal {
        padding: 4px;
        background-color: black;
        opacity: 0.7;
        color: white;
        font-family: 'consolas', serif;
        font-weight: 300;
        font-size: .85rem;
        white-space: pre-wrap;
        word-wrap: break-word;
        border-radius: 1px 1px 5px 5px;
        overflow-y: scroll;
        height: 168px;
    }

    #thinker-redis-terminal .window .terminal::after {
        content: "|";
        animation: blink 1.25s steps(1) infinite
    }

    #thinker-redis-terminal .prompt {
        color: #bde371
    }

    #thinker-redis-terminal .path {
        color: #5ed7ff
    }

    @@keyframes blink {
        50% {
            color: transparent
        }
    }

    @@keyframes bounceIn {
        0% {
            transform: translateY(-1000px)
        }
        60% {
            transform: translateY(200px)
        }
        100% {
            transform: translateY(0px)
        }
    }

    #thinker-redis-terminal .kw_a {
        color: #E6DB74
    }

    #thinker-redis-terminal .kw_b {
        color: #bde371
    }

    #thinker-redis-terminal .layui-card-header {
        height: 202px;
    }
</style>
<div class="layui-fluid " id="thinker-redis-terminal" lay-title="Redis监控">
    <div class="layui-card ">
        <div class="layui-card-header ">
            <div class="layui-row ">
                <div class="layui-col-xs12 ">
                    <div class="window">
                        <div class="handle">
                            <div class="buttons">
                                <button class="closes"></button>
                                <button class="minimize"></button>
                                <button class="maximize"></button>
                            </div>
                            <span class="title"></span>
                        </div>
                        <div class="terminal"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-card-body ">
            <table id="logscontroller_redis" lay-filter="logscontroller_redis"></table>
        </div>
    </div>

</div>
<script>
    layui.use(["tableplus"], function () {
        function load() {
            var $ = layui.jquery;
            layui.tableplus.on('sort(logscontroller_redis)', function (obj) {
                layui.tableplus.reload('logscontroller_redis', {
                    initSort: obj,
                    where: {
                        sort: obj.field,
                        order: obj.type || "desc"
                    }
                });
            });
            var _searchField = {}, idStr = 'logscontroller_redis';
            layui.form.on('submit(' + idStr + '_form_search)', function (data) {
                _searchField = data.field;
                //执行重载
                layui.tableplus.reload(idStr, {
                    where: _searchField
                });
                return false;
            });
            window["_" + idStr + "_ins"] = layui.tableplus.init(idStr, $.extend({
                response: {
                    statusName: layui.conf.response.statusName,
                    statusCode: layui.conf.response.statusCode.ok,
                    msgName: layui.conf.response.msgName,
                    countName: layui.conf.response.countName,
                    dataName: layui.conf.response.dataName
                },
                cols: [[{"field": "text", "title": "参数名"}, {"field": "ext", "title": "参数值"}, {
                    "field": "value",
                    "title": "参数解释"
                }]]
            }, layui.conf.table, {"data":{$this->getConfig("data")}, "limit"
        :
            100, "limits"
        :
            [100, 200]
        } ))
            ;


            // $('#thinker-redis-terminal').find('.window .terminal').css({"height": document.documentElement.clientHeight - 155 + 'px'});

            var title = $(".title");
            var terminal = $(".terminal");
            var prompt = "➜";
            var path = "~";
            var commandHistory = [];
            var historyIndex = 0;
            var command = "";

            title.text("root@@thinker: ~ (redis-cli)");
            var date = new Date().toString();
            date = date.substr(0, date.indexOf("GMT") - 1);
            printf("Last login: " + date + " on thinker, type `help` for help\n");
            displayPrompt();

            var commands = [{
                name: "clear",
                function: clear
            }, {
                name: "help",
                function: help
            }, {
                name: "echo",
                function: echo
            }, {
                name: "keys",
                function: keys
            }, {
                name: "get",
                function: get
            }, {
                name: "exists",
                function: exists
            }, {
                name: "pttl",
                function: pttl
            }, {
                name: "pexpire",
                function: pexpire
            }, {
                name: "set",
                function: set
            }, {
                name: "del",
                function: del
            }];

            // system command

            function clear() {
                terminal.text("");
                printc();
            }

            function echo(args) {
                var str = args.join("");
                printf(str + "\n");
                printc();
            }

            function erase(n) {
                command = command.slice(0, -n);
                terminal.html(terminal.html().slice(0, -n));
            }

            function clearCommand() {
                if (command.length > 0) {
                    erase(command.length);
                }
            }

            // redis command
            function keys(args) {
                args = args.join("");
                if (!hasArgs(args)) {
                    errorArgs('keys');
                } else {
                    layui.http.post("/thinkeradmin/logs/redisterminal", {
                        type: "keys",
                        args: args
                    }, function (code, msg, data, all) {
                        if (data.length) {
                            for (var i = 0; i < data.length; i++) {
                                printf('"' + data[i] + '"\n');
                            }
                        } else {
                            printf("(empty list or set)\n");
                        }
                        printc();
                    }, function (code, msg, data, all) {
                        printf(data + "\n");
                        printc();
                    });
                }
            }

            function get(args) {
                args = args.join("");
                if (!hasArgs(args)) {
                    errorArgs('get');
                } else {
                    layui.http.post("/thinkeradmin/logs/redisterminal", {
                        type: "get",
                        args: args
                    }, function (code, msg, data, all) {
                        if (data.length) {
                            printf(data + '\n');
                        } else {
                            printf("(nil)\n");
                        }
                        printc();
                    }, function (code, msg, data, all) {
                        printf(data + "\n");
                        printc();
                    });
                }
            }

            function set(args) {
                if (!hasArgs(args.join(""))) {
                    errorArgs('set');
                } else {
                    layui.http.post("/thinkeradmin/logs/redisterminal", {
                        type: "set",
                        args: args.join("`")
                    }, function (code, msg, data, all) {
                        printf('SET SUCCESS\n');
                        printc();
                    }, function (code, msg, data, all) {
                        printf(data + "\n");
                        printc();
                    });
                }
            }

            function del(args) {
                if (!hasArgs(args.join(""))) {
                    errorArgs('del');
                } else {
                    layui.http.post("/thinkeradmin/logs/redisterminal", {
                        type: "del",
                        args: args.join("`")
                    }, function (code, msg, data, all) {
                        printf('DEL SUCCESS\n');
                        printc();
                    }, function (code, msg, data, all) {
                        printf(data + "\n");
                        printc();
                    });
                }
            }

            function exists(args) {
                if (!hasArgs(args.join(""))) {
                    errorArgs('exists');
                } else {
                    layui.http.post("/thinkeradmin/logs/redisterminal", {
                        type: "exists",
                        args: args.join("`")
                    }, function (code, msg, data, all) {
                        printf('EXISTS\n');
                        printc();
                    }, function (code, msg, data, all) {
                        printf("NOT EXISTS\n");
                        printc();
                    });
                }
            }

            function pttl(args) {
                args = args.join("");
                if (!hasArgs(args)) {
                    errorArgs('pttl');
                } else {
                    layui.http.post("/thinkeradmin/logs/redisterminal", {
                        type: "pttl",
                        args: args
                    }, function (code, msg, data, all) {
                        printf('EXPIRED AFTER '+data+'\n');
                        printc();
                    }, function (code, msg, data, all) {
                        printf("NOT HAVE EXPIRED TIME\n");
                        printc();
                    });
                }
            }

            function pexpire(args) {
                if (!hasArgs(args.join(""))) {
                    errorArgs('pexpire');
                } else {
                    layui.http.post("/thinkeradmin/logs/redisterminal", {
                        type: "pexpire",
                        args: args.join("`")
                    }, function (code, msg, data, all) {
                        printf('EXPIRED SUCCESS\n');
                        printc();
                    }, function (code, msg, data, all) {
                        printf("EXPIRED FAIL\n");
                        printc();
                    });
                }
            }

            function help(arg) {
                arg = arg.join(" ");
                if (hasArgs(arg)) {
                    switch (arg) {
                        case "keys": {
                            printf(" KEYS pattern\n");
                            printf(" <span class='kw_a'>summary</span>: Find all keys matching the given pattern.\n");
                            printf(" <span class='kw_a'>since</span>: 1.0.0\n");
                            printf(" <span class='kw_a'>group</span>: generic\n");
                            break;
                        }

                        case "get": {
                            printf(" GET key\n");
                            printf(" <span class='kw_a'>summary</span>: Get the value of a key\n");
                            printf(" <span class='kw_a'>since</span>: 1.0.0\n");
                            printf(" <span class='kw_a'>group</span>: string\n");
                            break;
                        }
                        case "set": {
                            printf(" DEL key [key ...]\n");
                            printf(" <span class='kw_a'>summary</span>:  Delete a key\n");
                            printf(" <span class='kw_a'>since</span>: 1.0.0\n");
                            printf(" <span class='kw_a'>group</span>: generic\n");
                            break;
                        }
                        case "del": {
                            printf(" SET key value\n");
                            printf(" <span class='kw_a'>summary</span>: Set the string value of a key\n");
                            printf(" <span class='kw_a'>since</span>: 1.0.0\n");
                            printf(" <span class='kw_a'>group</span>: string\n");
                            break;
                        }
                        case "exists": {
                            printf(" EXISTS key [key ...]\n");
                            printf(" <span class='kw_a'>summary</span>: Determine if a key exists\n");
                            printf(" <span class='kw_a'>since</span>: 1.0.0\n");
                            printf(" <span class='kw_a'>group</span>: generic\n");
                            break;
                        }
                        case "pttl": {
                            printf(" PTTL key\n");
                            printf(" <span class='kw_a'>summary</span>: Get the time to live for a key in milliseconds\n");
                            printf(" <span class='kw_a'>since</span>: 2.6.0\n");
                            printf(" <span class='kw_a'>group</span>: generic\n");
                            break;
                        }
                        case "pexpire": {
                            printf(" PEXPIRE key milliseconds\n");
                            printf(" <span class='kw_a'>summary</span>: Set a key's time to live in milliseconds\n");
                            printf(" <span class='kw_a'>since</span>: 2.6.0\n");
                            printf(" <span class='kw_a'>group</span>: generic\n");
                            break;
                        }
                        default: {
                            printf(" no more messages about command `" + arg + "`\n");
                        }
                    }
                } else {
                    printf(" Redis bash with SpringBoot by <a href='https://mrbird.cc' class='kw_b'>MrBird</a>, version v0.0.1 - Snapshot \n");
                    printf(" These shell commands are defined internally.  Type `help` to see this list.\n");
                    printf(" Type `help name` to find out more about the command `name`.\n");
                    printf(" -------------------------------- \n");
                    printf(" |       system command         | \n");
                    printf(" -------------------------------- \n");
                    printf(" clear    help    echo\n");
                    printf(" -------------------------------- \n");
                    printf(" |       redis  command         | \n");
                    printf(" -------------------------------- \n");
                    printf(" keys         get         set \n");
                    printf(" del          exists      pttl\n");
                    printf(" pexpire\n");
                }
                printc();
            }


            function processCommand() {
                var isValid = false;
                var args = command.split(" ");
                var cmd = args[0];
                args.shift();
                for (var i = 0; i < commands.length; i++) {
                    if (cmd === commands[i].name) {
                        commands[i].function(args);
                        isValid = true;
                        break;
                    }
                }
                if (!isValid) {
                    printf("redis: command not found: `" + command + "`, type `help` for help.\n");
                    printc();
                }
                commandHistory.push(command);
                historyIndex = commandHistory.length;
                command = "";
            }

            function displayPrompt() {
                printf('<span class="prompt">' + prompt + "</span> ");
                printf('<span class="path">' + path + "</span> ");
            }


            function appendCommand(str) {
                terminal.append(str);
                command += str;
            }

            $(document).off("keydown").keydown(function (e) {
                e = e || window.event;
                var keyCode = typeof e.which === "number" ? e.which : e.keyCode;
                if (keyCode === 8 && e.target.tagName !== "INPUT" && e.target.tagName !== "TEXTAREA") {
                    e.preventDefault();
                    if (command !== "") {
                        erase(1);
                    }
                }
                if (keyCode === 38 || keyCode === 40) {
                    if (keyCode === 38) {
                        historyIndex--;
                        if (historyIndex < 0) {
                            historyIndex++;
                        }
                    } else if (keyCode === 40) {
                        historyIndex++;
                        if (historyIndex > commandHistory.length - 1) {
                            historyIndex--;
                        }
                    }
                    var cmd = commandHistory[historyIndex];
                    if (cmd !== undefined) {
                        clearCommand();
                        appendCommand(cmd);
                    }
                }
            });
            $(document).off("keypress").keypress(function (e) {
                e = e || window.event;
                var keyCode = typeof e.which === "number" ? e.which : e.keyCode;
                switch (keyCode) {
                    case 13: {
                        printf("\n");
                        processCommand();
                        break;
                    }
                    case 32: {
                        e.preventDefault();
                        appendCommand(String.fromCharCode(keyCode));
                        break;
                    }

                    default: {
                        appendCommand(String.fromCharCode(keyCode));
                    }
                }
            });

            function scroll(o) {
                o = o[0];
                o.scrollTop = o.scrollHeight;
            }

            function printf(msg) {
                terminal.append(msg);
            }

            function printc() {
                displayPrompt();
                historyIndex++;
                scroll(terminal);
            }

            function hasArgs(arg) {
                try {
                    if (arg.length === 0) return 0;
                    else return 1;
                } catch (e) {
                    return 0;
                }
            }

            function errorArgs(args) {
                printf("(error) ERR wrong number of arguments for '" + args + "' command\n");
                printc();
            }
        }

        if (!layui.common) {
            layui.use('common', load);
        } else {
            layui.cache.callback.common();
            load();
        }
    });
</script>
HTML;
    }
}