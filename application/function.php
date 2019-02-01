<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2019/1/10
 * Time: 19:47
 */

// 为方便系统核心升级，业务开发中需要用到的公共函数请写在这个文件，不要去修改common.php文件
use think\Db;

/**
 * 获取指定标志的设置数据
 * @param string $key 要获取的设置标记
 * @param int $is_value 是否只取value内容
 * @param int $lang 要取的语言版本
 * @return array|bool|mixed
 */
function tb_config($key = '', $is_value = 1, $lang = 1)
{
    if (empty(cache('config:' . $key))) {
        $config = db('admin_config', '', false)->where(['name' => $key, 'lang' => $lang])->find();
        if (empty($config)) {
            $config = db('admin_config', '', false)->where(['name' => $key, 'lang' => 1])->find();
            if (empty($config)) {
                return null;
            }
        }
        cache('config:' . $key, $config, 'system-config');
    } else {
        $config = cache('config:' . $key);
    }
    if ( $config['type'] == 'array') {
        $config['value'] = optStr2Arr($config['value']);
    }
    if ($is_value == 0) {
        return $config;
    } else {
        return $config['value'];
    }
}


/**
 * 判断当前访问的用户是  PC端  还是 手机端  返回true 为手机端  false 为PC 端
 * @return boolean
 */
function isMobile()
{
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])) {
        return true;
    }

    // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset ($_SERVER['HTTP_VIA'])) {
        // 找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    }
    // 脑残法，判断手机发送的客户端标志,兼容性有待提高
    if (isset ($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array('nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile');
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT'])))
            return true;
    }
    // 协议法，因为有可能不准确，放到最后判断
    if (isset ($_SERVER['HTTP_ACCEPT'])) {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return true;
        }
    }
    return false;
}