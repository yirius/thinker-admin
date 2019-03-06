<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/20
 * Time: 下午8:01
 */

namespace Yirius\Admin\command;


use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;

class Cache extends Command
{
    protected function configure()
    {
        $this
            ->setName('admin:cache')
            ->addArgument('tag', Argument::OPTIONAL, "cache operate tag, 'thinker_admin_auth' just need auth")
            ->addArgument('type', Argument::OPTIONAL, "cache operate type")
            ->addArgument('isthinker', Argument::OPTIONAL, "cache is thinker", true)
            ->setDescription('this command use for model operate, args with [tag] [type]');
    }

    protected function execute(Input $input, Output $output)
    {
        $tag = trim($input->getArgument('tag'));

        $type = trim($input->getArgument('type'));

        $isthinker = trim($input->getArgument('isthinker'));

        $output->comment("admin:cache operate [thinker_admin_". $tag ."] -> [". $type ."]");

        if($type == "clear"){
            $this->clear($tag, $isthinker, $output);
        }
    }

    /**
     * @title clear
     * @description
     * @createtime 2019/3/5 下午4:09
     * @param $tag
     * @param $isthinker
     * @param Output $output
     */
    protected function clear($tag, $isthinker, Output $output)
    {
        $flag = \think\facade\Cache::clear(($isthinker ? "thinker_admin_" : '') . $tag);

        if($flag){
            $output->info("tag: thinker_admin_" . $tag . " clear success");
        }else{
            $output->error("tag: thinker_admin_" . $tag . " clear error");
        }
    }
}