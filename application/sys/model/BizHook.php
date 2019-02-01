<?php

namespace app\sys\model;


use think\Db;
use think\Log;
use think\Model as SysModel;

class BizHook extends SysModel
{

    /**
     * 表名称
     * @var string
     */
    const TABLE_NAME = 'sys_hook';

    protected static $classLibrary;

    //是否为系统钩子
    protected static $is_sys = false;


    /**
     * 执行当前操作方法
     * @param $key
     * @return mixed
     */
    public static function exec($key='')
    {
        $hook_list = [];
        if(empty($key) && self::$is_sys == true){
            $hook_list = self::$classLibrary;
        }
        $list = self::hook_exec_list($key);
        if( $list !== false){
            $hook_list = $list;
        }
        self::exec_hook($hook_list);
    }


    /**
     * 执行钩子
     * @param array $execList 钩子列表
     * @return mixed
     */
    protected static function exec_hook(array $execList)
    {
        if(self::$is_sys == false){
            foreach ($execList as $key => $item) {
                $item = json_decode($item, true);
                if (!class_exists($item['class'])) {
                    continue;
                }
                $class = self::getClass($item['class']);
                if (!method_exists($class, $item['method'])) {
                    continue;
                }
                call_user_func([$class, $item['method']]);
            }
        }else{
            self::$is_sys = false;
            foreach ($execList as $key => $item) {
                if (!class_exists($key)) {
                    continue;
                }
                if (!method_exists($key, $item)) {
                    continue;
                }
                call_user_func([$key, $item]);
            }
        }
        return true;
    }


    /**
     * 当前hook的执行列表
     * @param string $key
     * @return mixed
     */
    protected static function hook_exec_list($key = null)
    {
        if (empty(cache('hook_exec_list:' . $key))) {
            $execList = self::M()->where(['key' => $key, 'status' => '1'])->column('exec');
            if ( empty($execList) ){
                $execList = 'nothing';
            }
            cache('hook_exec_list:' . $key, $execList);
        } else {
            $execList = cache('hook_exec_list:' . $key);
        }
        if ($execList == 'nothing')
            return false;
        return $execList;
    }

    /**
     * 添加钩子
     * @param string $class 类名
     * @param string $method 方法名
     * @return mixed
     */
    public static function add_hook($class,$method)
    {
        if(empty($class) || empty($method)){
            return false;
        }
        if(is_array($class)){
            if(!is_array($method)){
                return false;
            }
            foreach ($class as $key=>$value){
                self::set_hook_array($value,$method[$key]);
            }
        }else{
            self::set_hook_array($class,$method);
        }
        self::$is_sys = true;
    }

    protected static function set_hook_array($class,$method)
    {
        if(!class_exists($class)) return false;
        if(!method_exists($class,$method)) return false;
        self::$classLibrary[$class] = $method;
    }


    /**
     * 数据库
     * @return \think\db\Query
     */
    protected static function M()
    {
        return Db::name(self::TABLE_NAME);
    }


    /**
     * 获取钩子对象
     * @return mixed
     */
    protected static function getClass($class)
    {
        if (empty(self::$classLibrary[$class])) {
            self::$classLibrary[$class] = new $class;
        }
        return self::$classLibrary[$class];
    }


}