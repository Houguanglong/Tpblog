<?php
/**
 * Created by PhpStorm.
 * User: 357397264@qq.com
 * Date: 2018/6/7
 * Time: 下午2:29
 */

namespace app\sys\model;


use think\Db;
use think\Model;

class AdminDIyUrl extends Model
{

    /**
     * 获取diyurl
     * @param string $dispatch
     * @return string
     */
    public static function get_diy_url(string $dispatch)
    {
        $diy_url = cache('dis_url:'.$dispatch);
        if( empty($diy_url) ){
            $diy_url = Db::name('admin_diy_url')->where(['url_sign'=>$dispatch])->find();
            cache('dis_url:'.$dispatch,$diy_url);
        }
        return $diy_url;
    }

}