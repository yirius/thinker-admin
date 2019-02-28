<?php
/**
 * Created by PhpStorm.
 * User: yangyuance
 * Date: 2019/2/21
 * Time: 下午10:36
 */

namespace Yirius\Admin\extend;


class Tools
{
    /**
     * @title jsonSend
     * @description
     * @createtime 2019/2/21 下午10:40
     * @param array|string $data
     * @param int $code
     * @param string $msg
     * @param array $header
     */
    public function jsonSend($data = [], $code = 1, $msg = "success", $header = [])
    {
        response([
            'data' => $data,
            'code' => $code,
            'msg' => $msg
        ], 200, $header, "json")->send();
        exit();
    }

    /**
     * @title tree
     * @description make data array for tree
     * @createtime 2019/2/22 下午3:12
     * @param array $data
     * @param array|\Closure|null $keys
     * @param array|null $config
     * @return array
     */
    public function tree(array $data, $keys = null, array $config = null)
    {
        //set config
        if (is_null($config)) {
            $config = [
                'parentid' => "pid",
                'id' => "id",
                'topindex' => 0,
                'sublist' => "list"
            ];
        }else{
            $config = array_merge([
                'parentid' => "pid",
                'id' => "id",
                'topindex' => 0,
                'sublist' => "list"
            ], $config);
        }

        //first, add all data to array and set key = pid
        $pidForList = [];
        foreach ($data as $i => $v) {
            if (!isset($pidForList[$v[$config['parentid']]])) $pidForList[$v[$config['parentid']]] = [];

            $pidForList[$v[$config['parentid']]][] = $v;
        }
        //use key to sort, asc
        ksort($pidForList);

        return $this->subTree($pidForList, $config["topindex"], $keys, $config);
    }

    /**
     * @title subTree
     * @description get tree data's sublist
     * @createtime 2019/2/22 下午3:12
     * @param $pidForList
     * @param $pid
     * @param $keys
     * @param $config
     * @return array
     */
    protected function subTree($pidForList, $pid, $keys, $config)
    {
        $subTree = [];
        foreach ($pidForList[$pid] as $i => $v) {
            if (is_null($keys)) {
                $tempTree = $v;
            } else {
                if(is_array($keys)){
                    $tempTree = [];
                    foreach ($keys as $key) {
                        $tempTree[$key] = isset($v[$key]) ? $v[$key] : '';
                    }
                }else if($keys instanceof \Closure){
                    $tempTree = $keys($v);
                }
            }

            $tempTree[$config['sublist']] = [];

            //judge pidForList is have this pid
            if (isset($pidForList[$v[$config['id']]])) {
                $tempTree[$config['sublist']] = $this->subTree($pidForList, $v[$config['id']], $keys, $config);
            }

            $subTree[] = $tempTree;
        }

        return $subTree;
    }

    /**
     * @title rand
     * @description create a new rand for chars or num
     * @createtime 2019/2/28 下午8:20
     * @param int $len
     * @param string $format
     * @return string
     */
    public function rand($len = 6, $format = 'NUMBER')
    {
        $format = strtoupper($format);
        switch ($format) {
            case 'ALL':
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-@#~';
                break;
            case 'CHAR':
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-@#~';
                break;
            case 'NUMBER':
                $chars = '0123456789';
                break;
            default :
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-@#~';
                break;
        }
        $password = "";
        while (strlen($password) < $len)
            $password .= substr($chars, (mt_rand() % strlen($chars)), 1);
        return $password;
    }
}