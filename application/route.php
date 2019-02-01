<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;

include_module_route();

// 域名自定义
$info = api('sys', 'Domain', 'getDomainInfo', [$_SERVER['HTTP_HOST']]);
if (!empty($info)) {
    $url = $info['module'] . '/' . $info['controller'] . '/' . $info['action'] . '/' . $info['param'];
    Route::get('/', $url);
}
// 自定义地址
$dispatch = request()->pathinfo();
if( substr($dispatch,-5) == '.'.config('url_html_suffix') ){
    $dispatch = substr($dispatch,0,-5);
}
$diy_url = \app\sys\model\AdminDIyUrl::get_diy_url($dispatch);
if (!empty($diy_url)) {
    Route::get($dispatch, $diy_url['real_url']);
} else {
    $params = explode('/', $dispatch);
    $url = $params[0];
    unset($params[0]);
    $modules = getDir(APP_PATH);
    // 如果路径参数中只有一个参数，并且不是已有模块，则为自定义路径
    if (!in_array($dispatch, $modules)) {
        $real_url = explode('.', $url);
        $real_url2 = $real_url[0];
        $diy_url = \app\sys\model\AdminDIyUrl::get_diy_url($real_url2);
        if ($diy_url) {
            $code = '';
            if (!empty($params[1])) {
                if (strpos($diy_url['real_url'], '?')) {
                    $code = '&';
                } else {
                    $code = '?';
                }
            }
            for ($i = 1; $i <= count($params); $i += 2) {
                if (!empty($params[$i + 1])) {
                    $code .= $params[$i] . '=' . $params[$i + 1] . '&';
                }
            }
            $ruler = $diy_url['real_url'] . rtrim($code, '&');
            if ( !empty($relurl[1]) ) {
                Route::get($real_url2, $ruler);
            }
        }
        Route::get('', null);
    }
}


/**
 * 加载所有模块内的路由文件
 * @return void
 */
function include_module_route()
{
    // 获取每个模块下的TagLib文件
    $dirs = opendir(APP_PATH);
    while ($dir = readdir($dirs)) {
        if ($dir == 'common' || $dir == '.' || $dir == '..' || !is_dir(APP_PATH . $dir) || !is_file(APP_PATH . $dir . '/route.php')) {
            continue;
        }
        include APP_PATH . $dir. DS .'route.php';
    }
}

