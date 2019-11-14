<?php


namespace Yirius\Admin\route\controller;


use Yirius\Admin\extend\ThinkerController;
use Yirius\Admin\form\assemblys\Button;
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
        echo ThinkerAdmin::Table(function(ThinkerTable $table){
            $table->restful("111")->setOperateUrl("1231");

            $table->columns("id", "ceshi")->edit();

            $table->columns("id1", "ce1")->switchs("field")->render();

            echo (new Button("id", "测试按钮"))->danger()->lg()->render();
        })->render();

        PRINT_R(ThinkerAdmin::getScript());
    }
}