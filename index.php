<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2019/1/10
 * Time: 18:22
 */
define('APP_PATH',__DIR__ .'/application/');
// 定义浏览器公共访问目录
define('PUBLIC_PATH','/');
// url是否在强制加上入口文件
define('URL_ENTRY_FILE',TRUE);
// 加载框架引导文件
require __DIR__ . '/include/start.php';