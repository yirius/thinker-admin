<?php


namespace Yirius\Admin\widgets;


class File extends Widgets
{
    /**
     * @title      getCatelogs
     * @description
     * @createtime 2019/12/5 5:55 下午
     * @param     $path
     * @param     $page
     * @param int $limit
     * @return array
     * @author     yangyuance
     */
    public function getCatelogs($path, $page, $limit = 10)
    {
        $dirArr = [];

        $startNum = ((intval($page) <= 1 ? 1 : intval($page))-1) * $limit;

        $count = 0;
        foreach($this->readDir($path) as $item){
            if($startNum <= $count){
                if($count < $startNum + $limit){
                    $dirArr[] = $item;
                }
            }
            ++$count;
        }

        unset($startNum);

        return [
            'data' => $dirArr,
            'count' => $count
        ];
    }

    /**
     * @title      getLogContent
     * @description
     * @createtime 2019/11/28 10:51 下午
     * @param     $path
     * @param     $page
     * @param int $limit
     * @return array
     * @author     yangyuance
     */
    public function getLogContent($path, $page, $limit = 10)
    {
        $contentArr = [];

        $startNum = ((intval($page) <= 1 ? 1 : intval($page))-1) * $limit;

        $count = 0;
        foreach($this->readFile($path) as $item){
            if(!empty($item)){
                if($startNum <= $count){
                    if($count < $startNum + $limit){
                        $contentArr[] = array_merge(json_decode($item, true), [
                            'id' => $count+1
                        ]);
                    }
                }
                ++$count;
            }
        }

        unset($startNum);

        return [
            'data' => $contentArr,
            'count' => $count
        ];
    }

    /**
     * @title      traverseDir
     * @description 使用yield获取所有文件夹
     * @createtime 2019/11/28 10:01 下午
     * @param $dir
     * @return \Generator
     * @author     yangyuance
     */
    protected function readDir($dir)
    {
        if (is_dir($dir)) {
            if ($handle = opendir($dir)) {
                while(($file = readdir($handle)) !== false){
                    if (in_array($file, ['.', '..'], true)) {
                        continue;
                    }

                    $arr = [
                        'id'  => $file,
                        'name' => $file,
                        'path' => $dir . DS . $file,
                        'isdir' => is_dir($dir . DS . $file),
                        'size' => round((filesize($dir . DS . $file)/1024),2),
                        'update_time' => date("Y-m-d H:i:s", filemtime($dir . DS . $file))
                    ];

                    unset($file);

                    yield $arr;
                }

                closedir($handle);
            }
        }
    }

    /**
     * @title      read_file
     * @description
     * @createtime 2019/11/28 10:50 下午
     * @param $path
     * @return \Generator
     * @author     yangyuance
     */
    protected function readFile($path)
    {
        if(is_file($path)){
            if ($handle = fopen($path, 'r')) {

                while (! feof($handle)) {

                    yield trim(fgets($handle));

                }

                fclose($handle);
            }
        }
    }
}