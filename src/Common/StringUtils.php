<?php
/**
 * Created by PhpStorm.
 * User: liuhui1
 * Date: 2018/3/17
 * Time: 10:23
 */

namespace PHPStack\Common;


class StringUtils
{

    public static function randStr($len) {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $result = [];
        for ($i=0;$i<$len;$i++) {
            $result[] = $chars[rand(0,strlen($chars)-1)];
        }
        return implode('',$result);
    }

}