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
use app\sys\model\Module;
use think\Db;


/**
 * 初始化配置信息行为
 * 将系统配置信息合并到本地配置
 * @package app\common\behavior
 */
class Config
{

    /**
     * @var array
     */
    protected $dispatch;


    /**
     * 执行行为 run方法是Behavior唯一的接口
     * @access public
     * @param mixed $params  行为参数
     * @return void
     */
    public function run(&$params)
    {
        //获取系统设置的默认模块
        $default_module = tb_config('default_module',1);
        //如果设置则更改配置信息
        if(!empty($default_module)){
            config('default_module'.$default_module);
        }

        //判断是否是安装模块操作 如果是安装操作则直接返回
        if(defined('BIND_MODULE') && BIND_MODULE === 'install') return;

        //获取当前模块名称
        $module = '';
        //获取调度信息 包括模块名、控制器名、方法名 数组形式
        $this->dispatch = request()->dispatch();
        //请求的模块名
        $dispath_module = $this->dispatch['module'][0];
        //设为当前模块
        if(isset($this->dispatch['module'])){
            $module = $dispath_module;
        }

        //当前入口文件 /index.php
        $base_file = request()->baseFile();
        $basedir = $base_file;

        cookie('think_var','zh-cn');
        \think\Config::set('captcha',model('sys/Config')->captchaConfig());

        //判断是否为后台访问
        if(defined('ENTRANCE') && ENTRANCE == 'admin'){
            //如果没有模块名称则302跳转 到后台登陆
            if($this->dispatch['type'] == 'module' && $module == ''){
                header("Location:".$basedir.'/sys/login/index',true,302);exit();
            }
            // 修改默认访问控制器层
            config('url_controller_layer','admin');
            // 修改视图模板路径
            config('template.view_path',APP_PATH.$module.'/view/');
            config('template.mobile_path', APP_PATH. $module. '/view/');
        }else{
            //前台访问
            //如果访问的模块是admin 则302跳转
            if($this->dispatch['type'] == 'module' && $module == 'admin'){
                header("Location:".$basedir.'/sys',true,302);exit();
            }

            //修改默认控制器层
            if($module !== 'index'){
                if($module == ''){
                    $this->dispatch['module'][0] = $module = config('default_module');
                }
                //获取模块配置
                $module_config = Module::module_config($module);
                //获取当前的域名
                $domain = request()->domain();
                if(strpos($domain,'http://') === 0){
                    $domain = substr($domain,7);
                }elseif (strpos($domain,'https://') === 0){
                    $domain = substr($domain,8);
                }
                //判断是否是api接口域名
                if($domain == tb_config('api_domain')){
                    //更改配置信息 默认控制器层为api
                    config('url_controller_layer','api');
                }elseif (isMobile()){
                    //如果是手机访问
                    config('url_controller_layer','mobile');
                    if(empty($module_config['mobile_template'])){
                       $module_config['mobile_template'] = $module;
                    }
                    //修改配置信息 模板
                    config('template.view_path',config('template.home_view_path').$module_config['mobile_template'].'/');
                }else{
                    //pc端访问
                    //如果是手机访问
                    config('url_controller_layer','home');
                    if(empty($module_config['home_template'])){
                        $module_config['home_template'] = $module;
                    }
                    //修改配置信息 模板
                    config('template.view_path',config('template.home_view_path').$module_config['home_template'].'/');
                }
                //如果模板存在跳转模板 则设置配置信息
                if(is_file(config('template.view_path'). DS . 'dispatch_jump.tpl')){
                    config('dispatch_success_tmpl',config('template.view_path'). DS . 'dispatch_jump.tpl');
                    config('dispatch_error_tmpl',config('template.view_path'). DS . 'dispatch_jump.tpl');
                }
            }
            //验证控制器是否存在 不存在则设置controller为默认控制器层
            $this->check_controller();
        }
    }


    /**
     * 验证操作是否存在
     * @return mixed
     */
    public function check_controller()
    {
        if( empty($this->dispatch['module'][1]) ){
            $this->dispatch['module'][1] = config('default_controller');
        }
        $controller_arr = explode('_',$this->dispatch['module'][1]);
        $controller = implode('',array_map(function ($item){
            return ucfirst($item);
        },$controller_arr));
        $namespace = config('app_namespace').'\\'.$this->dispatch['module'][0].'\\'.config('url_controller_layer').'\\'.$controller;
        if( !class_exists($namespace) ){
            config('url_controller_layer', 'controller');
        }
    }


}
