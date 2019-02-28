<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/28
 * Time: 下午3:30
 */

namespace Yirius\Admin\command;


use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;
use Yirius\Admin\model\table\AdminMenu;
use Yirius\Admin\model\table\AdminRule;

class Menu extends Command
{
    /**
     * used args
     * @var array
     */
    protected $arguments = [];

    protected function configure()
    {
        $this
            ->setName('admin:menu')
            ->addArgument('name', Argument::REQUIRED, "menu's eng name")
            ->addArgument('title', Argument::REQUIRED, "menu's chs name")
            ->addArgument('jump', Argument::REQUIRED, "menu's jump")
            ->addArgument('edit', Argument::OPTIONAL, "menu is have edit", false)
            ->addArgument('restful', Argument::OPTIONAL, "menu's restful url", null)
            ->addArgument('pid', Argument::OPTIONAL, "menu's pid", 0)
            ->addArgument('sort', Argument::OPTIONAL, "menu's sort", 0)
            ->addArgument('icon', Argument::OPTIONAL, "menu's icon", null)
            ->setDescription('this command use for create menu, args with -name -title -jump [edit] [restful] [pid] [sort] [icon]');
    }

    protected function execute(Input $input, Output $output)
    {
        $this->arguments = $input->getArguments();

        //start link operate
        $this->createMenu($output);
    }

    /**
     * @title createMenu
     * @description
     * @createtime 2019/2/28 下午3:46
     * @param Output $output
     */
    protected function createMenu(Output $output)
    {
        $output->comment("admin:menu create new menu " . $this->arguments['jump']);

        $adminMenu = AdminMenu::adminSave();

        $result = $adminMenu->setAdd($this->arguments)->getResult();

        if($result === false){
            $output->error($adminMenu->getError());
        }else{
            //if pid != 0, then find it and use
            if($this->arguments['pid'] != 0){
                $jump = AdminMenu::get(['id' => $this->arguments['pid']])->getData('jump');

                $mid = AdminRule::get(['name' => $jump])->getData('id');
            }else{
                $mid = 0;
            }

            $this->createRules($output, $mid);
        }
    }

    protected function createRules(Output $output, $mid)
    {
        $output->comment("admin:menu create new rule " . $this->arguments['jump']);

        //create main rule

        $ruleMain = AdminRule::adminSave()->setAdd([
            'name' => $this->arguments['jump'],
            'title' => $this->arguments['title'],
            'status' => 1,
            'mid' => $mid,
            'type' => 1
        ])->getResult();

        //create restful
        if(!empty($this->arguments['restful'])){
            $output->comment("admin:menu create new rule restful " . $this->arguments['restful']);

            AdminRule::adminSave()->setAdd([
                'name' => $this->arguments['restful'],
                'title' => $this->arguments['title'] . "Restful",
                'status' => 1,
                'mid' => $ruleMain->getData("id"),
                'type' => 1
            ])->getResult();
        }

        if(!empty($this->arguments['edit'])){
            //create editpath
            $output->comment("admin:menu create new rule editpath " . $this->arguments['jump'] . "Edit");

            AdminRule::adminSave()->setAdd([
                'name' => $this->arguments['jump'] . "Edit",
                'title' => $this->arguments['title'] . "Edit界面",
                'status' => 1,
                'mid' => $ruleMain->getData("id"),
                'type' => 1
            ])->getResult();
        }
    }
}