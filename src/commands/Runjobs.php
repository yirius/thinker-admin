<?php


namespace Yirius\Admin\commands;


use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

class Runjobs extends Command
{
    /**
     * @title      configure
     * @description
     * @createtime 2019/11/28 4:52 下午
     * @author     yangyuance
     */
    protected function configure()
    {
        $this->setName('thinker:runjobs')
            ->addArgument('name', Argument::REQUIRED, "the file you want run in app/common/jobs")
            ->addOption('arg1', null, Option::VALUE_REQUIRED, 'arg1')
            ->addOption('arg2', null, Option::VALUE_REQUIRED, 'arg2')
            ->addOption('arg3', null, Option::VALUE_REQUIRED, 'arg3')
            ->addOption('arg4', null, Option::VALUE_REQUIRED, 'arg4')
            ->addOption('arg5', null, Option::VALUE_REQUIRED, 'arg5')
            ->setDescription('run jobs for php');
    }

    /**
     * @title      execute
     * @description
     * @createtime 2019/11/28 4:52 下午
     * @param Input  $input
     * @param Output $output
     * @return int|void|null
     * @author     yangyuance
     */
    protected function execute(Input $input, Output $output)
    {
        $name = trim($input->getArgument('name'));
        $name = "\\app\\common\\jobs\\".$name;
        if(class_exists($name)){
            $newClass = new $name;
            if(method_exists($newClass, "run")){
                try{
                    $newClass->run($input->getOptions());
                }catch (\Exception $exception){
                    $output->error($exception->getMessage());
                    thinker_error($exception);
                }
            }else{
                $output->error("class ".$name . "::run not found");
                thinker_error((new \Exception("class ".$name . "::run not found")));
            }
        }else{
            $output->error("class ".$name . "not found");
        }
    }
}