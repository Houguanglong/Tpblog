<?php
// +----------------------------------------------------------------------
// | ThinkBiz System
// | 功能： region操作类
// +----------------------------------------------------------------------
// | 版权所有 2013~2017     
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 作者: 吴跃忠 <357397264@qq.com>
// +----------------------------------------------------------------------

namespace app\sys\model;

use think\Model;

class Region extends Model
{

    /**
     * @var Region
     */
    private static $region;


    /**
     * 获取城市列表
     * @param $pid
     * @return array
     */
    public static function getRegionList($pid = 0)
    {
        if( empty(cache('regionList:'.$pid)) ){
            $cityList = self::_db()->where('pid',$pid)->select();
            cache('regionList:'.$pid,$cityList,'','system-region');
        }else{
            $cityList = cache('regionList:'.$pid);
        }
        return $cityList;
    }


    /**
     * 获取父级地区详情
     * @param int $id 当前地区ID
     * @return mixed
     */
    public function get_parent_region(int $id)
    {
        $detail = $this->get_region_detail($id);
        if( empty($detail['pid']) ){
            return 0;
        }else{
            return $this->get_parent_region($detail['pid']);
        }
    }


    /**
     * 获取地址详情
     * @param int $id
     * @return array|false|mixed|\PDOStatement|string|Model
     */
    public function get_region_detail($id)
    {
        if( empty(cache('region:'.$id)) ){
            $region = $this->where('id',$id)->find();
            cache('region:'.$id,$region);
        }else{
            $region = cache('region:'.$id);
        }
        return $region;
    }


    /**
     * @return Region
     */
    public static function _db(){
        if( empty(self::$region) ){
            self::$region = new Region();
        }
        return self::$region;
    }

}