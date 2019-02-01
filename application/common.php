<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
// 调用业务公共函数库
require("function.php");

/**
 * 选项字符串转数组
 * @param string  $string     要转换的选项字符串
 * @param string $exp         分割各选项的标记字符
 * @param string $opt_exp     分割选项键值的标记字符
 * @return array
 */
function optStr2Arr($string='',$exp=',',$opt_exp=':')
{
    $opt_arr = explode($exp, $string);
    $option = [];
    foreach ($opt_arr as $k => $opt) {
        $opts = explode($opt_exp, $opt);
        if( empty($opts[1]) ){
            $option[$k] = $opts[0];
        }else{
            $option[$opts[0]] = $opts[1];
        }
    }
    return $option;
}

/**
 * transfer中转接口助手函数调用模块api方法
 * @param $module
 * @param $api
 * @param $action
 * @param $param
 */
use api\Transfer;
function api($module,$api,$action,$param=''){
    $transfer = Transfer::getInstance();
    return $transfer->api($module,$api,$action,$param);
}


/**
 * getDir()去文件夹列表，getFile()去对应文件夹下面的文件列表,二者的区别在于判断有没有“.”后缀的文件，其他都一样
 */

//获取文件目录列表,该方法返回数组
function getDir($dir) {
    $dirArray[]=NULL;
    if (false != ($handle = opendir ( $dir ))) {
        $i=0;
        while ( false !== ($file = readdir ( $handle )) ) {
            //去掉"“.”、“..”以及带“.xxx”后缀的文件
            if ($file != "." && $file != ".." && !strpos($file,".")) {
                $dirArray[$i]=$file;
                $i++;
            }
        }
        //关闭句柄
        closedir ( $handle );
    }
    return $dirArray;
}