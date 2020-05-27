<?php


namespace Yirius\Admin\widgets;


class ThinkerTools
{
    /**
     * @title      match
     * @description
     * @createtime 2020/5/27 12:15 下午
     * @param $regex
     * @param $value
     * @return mixed
     * @author     yangyuance
     */
    public function match($regex, $value) {
        preg_match_all($regex, $value, $matches);
        return $matches;
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
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
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
        $canNext = cache($name);
        if (empty($canNext)) {
            /**
             * 如果不存在这个标记, 那就说明原来没进行过, 可以进行下一步
             */
            cache($name, time());
            return true;
        } else {
            /**
             * 如果日期大于记录时间seconds, 重新记录然后可以返回下一步
             */
            if ((time() - intval($canNext)) > $second) {
                cache($name, time());
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
     * @title      neatCertificate
     * @description 使证书字符串整齐化
     * @createtime 2019/11/27 6:12 下午
     * @param      $certStr
     * @param bool $isPrivate
     * @return string
     * @author     yangyuance
     */
    public function neatCertificate($certStr, $isPrivate = true)
    {
        $trueInfo = str_replace([
            '-----BEGIN PUBLIC KEY-----', '-----END PUBLIC KEY-----',
            '-----BEGIN PRIVATE KEY-----', '-----END PRIVATE KEY-----',
            "\n", "\r"
        ], '', $certStr);

        $prefix = $isPrivate ? 'PRIVATE KEY' : 'PUBLIC KEY';

        $returnData = [
            "-----BEGIN " . $prefix . "-----"
        ];
        foreach(str_split($trueInfo, 64) as $i => $item){
            $returnData[] = $item;
        }
        $returnData[] = "-----END " . $prefix . "-----";

        unset($trueInfo);
        unset($prefix);
        unset($certStr);
        unset($isPrivate);

        return join("\n", $returnData);
    }

    /**
     * @title formatDate
     * @description 把一个时间格式化城几天之前
     * @createtime 2019/3/3 下午7:51
     * @param $time
     * @param float|int $deadLine
     * @return string
     */
    public function formatDate($time, $deadLine = 86400 * 30, $isPrev = true)
    {
        if (empty($time)) {
            return "无时间";
        }
        if(is_numeric($time)){
            if($isPrev){
                $t = time() - $time;
            }else{
                $t = $time - time();
            }
        }else{
            if($isPrev){
                $t = time() - strtotime($time);
            }else{
                $t = strtotime($time) - time();
            }
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
                $result = $c . $v . ($isPrev ? '前' : '后');
                break;
            }
        }
        return $result;
    }

    /**
     * @title      deepAddslashes
     * @description 身价在反斜杠等注入参数
     * @createtime 2019/11/27 4:27 下午
     * @param $value
     * @return array|string
     * @author     yangyuance
     */
    public function deepAddslashes($value)
    {
        if(empty($value)){
            return $value;
        }else{
            return is_array($value) ? array_map('deepAddslashes', $value) : addslashes($value);
        }
    }

    /**
     * @title      isCli
     * @description
     * @createtime 2019/11/27 4:28 下午
     * @return bool
     * @author     yangyuance
     */
    public function isCli(){
        return preg_match("/cli/i", php_sapi_name()) ? true : false;
    }
}