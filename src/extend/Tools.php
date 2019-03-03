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
        } else {
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
                if (is_array($keys)) {
                    $tempTree = [];
                    foreach ($keys as $key) {
                        $tempTree[$key] = isset($v[$key]) ? $v[$key] : '';
                    }
                } else if ($keys instanceof \Closure) {
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

    /**
     * @title 设置一个等待时间
     * @description
     * @createtime: 2018/3/21 01:11
     * @param $name
     * @param int $second
     * @return bool
     */
    public function setTimeOut($name, $second = 60)
    {
        $canNext = session($name);
        if (empty($canNext)) {
            /**
             * 如果不存在这个标记, 那就说明原来没进行过, 可以进行下一步
             */
            session($name, time());
            return true;
        } else {
            /**
             * 如果日期大于记录时间seconds, 重新记录然后可以返回下一步
             */
            if ((time() - intval($canNext)) > $second) {
                session($name, time());
                return true;
            } else {
                //事件记录还没到
                return false;
            }
        }
    }

    /**
     * 金额小数点四舍五入
     * @param $amount
     * @param string $type
     * @return string
     */
    public function amountRound($amount, $type = "floor")
    {
        return sprintf("%.2f", $type($amount * 100) / 100);
    }

    /**
     * 使证书字符串整齐化
     * @param $certStr
     * @param string $prefix
     * @return string
     */
    public function neatCertificate($certStr, $prefix = 'PRIVATE KEY')
    {
        $trueInfo = str_replace(['-----BEGIN ' . $prefix . '-----', '-----END ' . $prefix . '-----'], '', $certStr);
        $trueInfo = str_replace(" ", "\n", $trueInfo);
        return "-----BEGIN " . $prefix . "-----\n" . $trueInfo . "\n-----END " . $prefix . "-----";
    }

    /**
     * @title formatDate
     * @description 把一个时间格式化城几天之前
     * @createtime 2019/3/3 下午7:51
     * @param $time
     * @param float|int $deadLine
     * @return string
     */
    public function formatDate($time, $deadLine = 86400 * 30)
    {
        if (empty($time)) {
            return "无时间";
        }
        if(is_numeric($time)){
            $t = time() - $time;
        }else{
            $t = time() - strtotime($time);
        }
        $f = array(
            '31536000' => '年',
            '2592000' => '个月',
            '604800' => '星期',
            '86400' => '天',
            '3600' => '小时',
            '60' => '分钟',
            '1' => '秒'
        );
        //如果是一个月之前的，直接显示时间
        if ($t > $deadLine) {
            return $time;
        }
        $result = "无时间";
        foreach ($f as $k => $v) {
            if (0 != $c = floor($t / (int)$k)) {
                $result = $c . $v . '前';
                break;
            }
        }
        return $result;
    }

    /**
     * @title is_cli
     * @description
     * @createtime 2019/3/3 下午7:52
     * @return bool
     */
    public function is_cli(){
        return preg_match("/cli/i", php_sapi_name()) ? true : false;
    }
}