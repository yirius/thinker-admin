<?php


namespace Yirius\Admin\route\controller;


use Yirius\Admin\extend\ThinkerController;
use Yirius\Admin\form\assemblys\Button;
use Yirius\Admin\layout\ThinkerCard;
use Yirius\Admin\layout\ThinkerCollapse;
use Yirius\Admin\layout\ThinkerCollapseItem;
use Yirius\Admin\layout\ThinkerCols;
use Yirius\Admin\layout\ThinkerRows;
use Yirius\Admin\route\model\TeAdminLogs;
use Yirius\Admin\table\ThinkerTable;
use Yirius\Admin\ThinkerAdmin;

class Show extends ThinkerController
{
    protected $tokenAuth = [
        'auth' => false
    ];

    /**
     * @title      index
     * @description
     * @createtime 2019/11/14 4:44 下午
     * @author     yangyuance
     */
    public function index()
    {
        ThinkerAdmin::Send()->html(<<<HTML
<div class="layui-fluid" id="VIEW-index" lay-title="首页">
  <div class="thinker-pad-tb20">
    <h1 class="thinker-lead">智证宝-智慧公证</h1>
    <div class="thinker-ignore thinker-pad-tb10">
      <p>为您提供银行公证-法院强执等一系列服务</p>
    </div>
  </div>
  <div class="layui-row layui-col-space15">
    <div class="layui-col-lg8">
      <div class="layui-row layui-col-space15">
        <div class="layui-col-sm4">
          <div class="thinker-linecard">
            <p class="thinker-linecard-title">
                今日新增合约
                <a lay-href="/contract/Contract/willcontract" class="thinker-c-blue thinker-font-12">立即处理</a>
            </p>
            <span class="thinker-linecard-text">101</span>
            <span class="thinker-ignore">份</span>
          </div>
        </div>
        <div class="layui-col-sm4">
          <div class="thinker-linecard thinker-br-green">
            <p class="thinker-linecard-title">
                今日新增公证
                <a lay-href="/contract/Notar/todonotar" class="thinker-c-blue thinker-font-12">立即处理</a>
            </p>
            <span class="thinker-linecard-text">55</span>
            <span class="thinker-ignore">份</span>
          </div>
        </div>
        <div class="layui-col-sm4">
          <div class="thinker-linecard thinker-br-red">
            <p class="thinker-linecard-title">
              今日新增强执
              <a lay-href="/contract/Enforce/willenforce" class="thinker-c-blue thinker-font-12">立即处理</a>
            </p>
            <span class="thinker-linecard-text">38</span>
            <span class="thinker-ignore">份</span>
          </div>
        </div>
        <div class="layui-col-xs12">
          <div class="layui-row layui-col-space15">
            <div class="layui-col-xs12">
              <div class="layui-card">
                <div class="layui-card-extra thinker-c-gray">
                  <span class="thinker-c-blue">今日</span>&nbsp;&nbsp;
                  <span>本周</span>&nbsp;&nbsp; <span>本月</span>&nbsp;&nbsp;
                  <span>全年</span>&nbsp;&nbsp;
                </div>
                <div class="layui-tab layui-tab-brief" lay-filter="index-chart">
                  <ul class="layui-tab-title">
                    <li class="layui-this">合约量</li>
                    <li>访问量</li>
                    <li>强执量</li>
                  </ul>
                  <div class="layui-tab-content" style="padding:0;">
                    <div class="layui-tab-item layui-show">
                      <div
                        id="index-echartLine"
                        style="width: 100%;height:400px;"
                      ></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
<!--            <div class="layui-col-xs12">-->
<!--              <div class="layui-card">-->
<!--                <div class="layui-tab layui-tab-brief" lay-filter="index-order">-->
<!--                  <ul class="layui-tab-title">-->
<!--                    <li class="layui-this">全部订单</li>-->
<!--                    <li>待付款</li>-->
<!--                    <li>待发货</li>-->
<!--                    <li>待处理退款</li>-->
<!--                  </ul>-->
<!--                  <div class="layui-tab-content" style="padding:0;">-->
<!--                    <div class="layui-tab-item layui-show thinker-table-full">-->
<!--                      <table lay-filter="index-tableOrder"></table>-->
<!--                    </div>-->
<!--                  </div>-->
<!--                </div>-->
<!--              </div>-->
<!--            </div>-->
          </div>
        </div>
      </div>
    </div>
    <div class="layui-col-lg4">
      <div class="layui-card">
        <div class="layui-card-header">服务器监控</div>
        <div class="layui-card-body">
          <div class="thinker-pad-b20">
            <h2 class="thinker-pad-b10">
              85%<span class="thinker-font-14 thinker-c-gray thinker-fr"
                >CPU使用率</span
              >
            </h2>
            <div class="layui-progress">
              <div
                class="layui-progress-bar layui-bg-blue"
                lay-percent="85%"
              ></div>
            </div>
          </div>
          <div class="thinker-pad-b20">
            <h2 class="thinker-pad-b10">
              58%<span class="thinker-font-14 thinker-c-gray thinker-fr"
                >内存占用率</span
              >
            </h2>
            <div class="layui-progress">
              <div
                class="layui-progress-bar layui-bg-red"
                lay-percent="58%"
              ></div>
            </div>
          </div>
          <div class="thinker-pad-b20">
            <h2 class="thinker-pad-b10">
              92%<span class="thinker-font-14 thinker-c-gray thinker-fr"
                >磁盘占用率</span
              >
            </h2>
            <div class="layui-progress">
              <div
                class="layui-progress-bar layui-bg-cyan"
                lay-percent="92%"
              ></div>
            </div>
          </div>
        </div>
      </div>

      <div class="layui-card">
        <div class="layui-card-header">系统信息</div>
        <div class="layui-card-body">
          <p>数据库信息：Mysql</p>
          <hr />
          <p>服务器IP：192.168.3.12</p>
          <hr />
          <p>服务器端口：80</p>
          <hr />
          <p>操作系统：WinXP</p>
          <hr />
          <p>WEB服务器：Apache</p>
        </div>
      </div>
    </div>
  </div>
  <script type="text/html" id="TPL-index-params">
    {{# layui.each(d.params,function(i,item){ }}
    <span class="layui-badge-rim">{{ item.val }}</span>
    {{# }) }}
  </script>
  <script type="text/html" id="TPL-index-status">
    {{#
        var status = {
            WAIT_PAY:{title:'待付款',color:'blue'},
            WAIT_DELIVER:{title:'待发货',color:'orange'},
            WAIT_REFUND:{title:'待退款',color:'red'},
        }[d.status];
    }}
    <span class="layui-badge layui-bg-{{status.color}}">{{ status.title }}</span>
  </script>
</div>
<script>
  layui.use(
    ['admin', 'echarts', 'element', 'helper', 'table', 'util'],
    function(admin, echarts, element, helper, table, util) {
      var $ = layui.jquery
      var view = $('#VIEW-chart-index')
      element.render('progress')

      createChart()

      var tableObj = table.render({
        size: 'nob',
        elem: '[lay-filter="index-tableOrder"]',
        api: 'getGoods',
        height: 400,
        cols: [
          [
            {
              title: '状态',
              fixed: true,
              templet: '#TPL-index-status',
              width: 80
            },
            { field: 'title', title: '商品名称', minWidth: 300 },
            { title: '商品参数', templet: '#TPL-index-params', minWidth: 240 },
            {
              title: '商品单价',
              templet: '<p><b class="thinker-c-red">￥{{d.price}}</b></p>',
              align: 'center',
              width: 90
            },
            {
              title: '购买数量',
              templet:
                '<p><b>{{d.buycount}}</b> <span class="thinker-c-gray">件</span></p>',
              align: 'center',
              width: 90
            },
            {
              field: 'time',
              title: '操作时间',
              templet:
                '<p><span title="{{d.time}}" class="thinker-c-gray">{{ layui.util.timeAgo(d.time)}}</span></p>',
              align: 'center',
              width: 170
            }
          ]
        ]
      })

      element.on('tab(index-order)', function(data) {
        tableObj.reload()
      })

      element.on('tab(index-chart)', function(data) {
        createChart()
      })

      function createChart() {
        var seriesData = []
        var xAxisData = []
        for (var i = 1; i <= 12; i++) {
          var val = helper.rand(10, 1000)
          seriesData.push(val)
          xAxisData.push((i < 10 ? '0' + i : i) + ':00')
        }
        var option = {
          tooltip: {
            trigger: 'axis',
            axisPointer: { type: 'cross' }
          },
          xAxis: {
            type: 'category',
            data: xAxisData
          },
          yAxis: {
            max: function(val) {
              return val.max + 400
            },
            min: function(val) {
              return val.min - 400
            },
            type: 'value',
            axisLabel: { formatter: '{value}' },
            axisPointer: { snap: true }
          },
          series: [
            {
              name: '时段销售额',
              type: 'line',
              symbolSize: 12,
              lineStyle: {
                normal: {
                  width: 5,
                  shadowColor: '#5a8bff',
                  shadowBlur: 40,
                  shadowOffsetY: 10
                }
              },
              data: seriesData
            }
          ]
        }
        echarts
          .init(document.getElementById('index-echartLine'), 'blue')
          .setOption(option)
      }
    }
  )
</script>
HTML
        );
    }
}