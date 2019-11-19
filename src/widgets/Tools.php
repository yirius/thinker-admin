<?php


namespace Yirius\Admin\widgets;


class Tools extends Widgets
{
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
}