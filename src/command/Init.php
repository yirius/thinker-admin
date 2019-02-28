<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/28
 * Time: 下午9:11
 */

namespace Yirius\Admin\command;


use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;

class Init extends Command
{
    protected function configure()
    {
        $this
            ->setName('admin:init')
            ->setDescription("this command use for init model's table and css");
    }

    protected function execute(Input $input, Output $output)
    {
        $output->comment("start sql import");

        try{
            Db::query("show tables");
        }catch (\Exception $err){

            $output->error("database connect error, please set database's config");

            exit;
        }

        //if can use database, go for sql
        $this->createTable($output);
    }

    protected function createTable(Output $output)
    {
        try{

            $sqlContent = file_get_contents(dirname(THINKER_ROOT) . DS . "sql" . DS . "php.sql");

            $sqlContent = str_replace(["\r", "\n", "\r\n"], "", $sqlContent);

            foreach(explode("#--------------------------------------", $sqlContent) as $item){
                Db::execute($item);
            }

        }catch (\Exception $err){
            $output->error("database import error: " . $err->getMessage());
            exit;
        }

        $output->info("sql import success");

        $this->copyAssets($output);
    }

    protected function copyAssets(Output $output)
    {
        $output->comment("start copy assets");

        //copy config
        copy(
            THINKER_ROOT . DS . "config" . DS . "convention.php",
            env('config_path') . DS . "thinkeradmin.php"
        );
        copy(
            THINKER_ROOT . DS . "config" . DS . "captcha.php",
            env('config_path') . DS . "captcha.php"
        );

        $publicPath = env("root_path"). DS . "public" . DS;

        $this->copy_dir(dirname(THINKER_ROOT) . DS .  "assets" . DS, $publicPath);

        //judge
        if(is_dir($publicPath . "thinker-admin" . DS)){
            $output->info("assets copy success");
        }else{
            $output->error("assets copy error");
        }
    }

    protected function copy_dir($src, $dst) {
        $dir = opendir($src);
        @mkdir($dst);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if ( is_dir($src . DS . $file) ) {
                    $this->copy_dir($src . DS . $file,$dst . DS . $file);
                    continue;
                }
                else {
                    copy($src . DS . $file,$dst . DS . $file);
                }
            }
        }
        closedir($dir);
    }
}