<?php
// +----------------------------------------------------------------------
// | ThinkBiz System
// | 功能： 系统行为扩展
// +----------------------------------------------------------------------
// | 版权所有 2013~2017 深圳市俊网网络有限公司 [ http://www.junnet.net ]
// +----------------------------------------------------------------------
// | 官方网站：http://www.junnet.net
// +----------------------------------------------------------------------
// | 作者: 余剑华 <528526198@qq.com>
// +----------------------------------------------------------------------
namespace app\common\behavior;

define('EBIZ_VERSION','v1.11.23');
define('EBIZ_VERSION_TIME','2018-09-10');

/**
 * 初始化配置信息行为
 * 将系统配置信息合并到本地配置
 * @package app\common\behavior
 */
class Begin
{
    /**
     * 执行行为 run方法是Behavior唯一的接口
     * @access public
     * @param mixed $params  行为参数
     * @return void
     */
    public function run(&$params)
    {
        $default_module = tb_config('default_module',1);
        if ( !empty($default_module) ){
            config('default_module',$default_module);
        }
        $this->set_taglib_build_in();
    }


    /**
     * 设置模板标签库
     * @return mixed
     */
    private function set_taglib_build_in()
    {
        if( empty(cache('tag_lib_build')) ){
            $tagLibIn = ['cx','app\common\taglib\Jun'];
            // 获取每个模块下的TagLib文件
            $dirs = opendir(APP_PATH);
            while ( $dir = readdir($dirs) ){
                if( $dir == 'common' || $dir == '.' || $dir == '..' || !is_dir(APP_PATH.$dir) || !is_file(APP_PATH.$dir.'/TagLib.php') ){
                    continue;
                }
                $tagLibIn[] = 'app\\'.$dir.'\\TagLib';
            }
            cache('tag_lib_build',$tagLibIn);
        }else{
            $tagLibIn = cache('tag_lib_build');
        }
        config('template.taglib_build_in',implode(',',$tagLibIn));
    }

}
