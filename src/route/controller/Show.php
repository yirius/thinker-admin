<?php


namespace Yirius\Admin\route\controller;


use Yirius\Admin\extend\ThinkerController;
use Yirius\Admin\form\assemblys\Button;
use Yirius\Admin\layout\ThinkerCard;
use Yirius\Admin\layout\ThinkerCollapse;
use Yirius\Admin\layout\ThinkerCollapseItem;
use Yirius\Admin\layout\ThinkerCols;
use Yirius\Admin\layout\ThinkerRows;
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
        echo 1;
//        return ThinkerAdmin::Table(function(ThinkerTable $table){
//            $table->setData([
//                ['id' => 1111, 'id1' => 2222]
//            ]);
//
//            $table->columns("", "")->expend();
//
//            $table->columns("", "")->setType("checkbox");
//
//            $table->columns("id", "ceshi")->edit()->delete();
//
//            $table->columns("id1", "ce1")->switchs("field")->render();
//
//            $table->toolbar()->add()->delete()->xlsx()->event()->add()->delete()->xlsx("", <<<HTML
//function(data){
//    console.log(data);
//    return data;
//}
//HTML
//            );
//
//            $table->colsEvent()->edit("thinkeradmin/Show/index")->delete()->expend("1111");
//
////            echo (new Button("id", "测试按钮"))->danger()->lg()->render();
//        })->send();
    }
}