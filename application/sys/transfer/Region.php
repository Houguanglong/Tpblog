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

namespace app\sys\transfer;

use think\Db;

class Region
{
    protected $_DB;

    public function __construct()
    {
        $this->_DB = Db::name('region');
    }

    /**
     * 获取城市列表
     * @param $pid
     * @param $region
     * @return array
     * @throws \Exception
     */
    public function getRegionList($pid = 0,$region = 0)
    {
        if( empty(cache('regionList:'.$pid.':'.$region)) ){
            if( !empty($region) ){
                $this->_DB->where('regionname',$region);
            }
            $cityList = $this->_DB->where('pid',$pid)->select();
            cache('regionList:'.$pid.':'.$region,$cityList,'','system-region');
        }else{
            $cityList = cache('regionList:'.$pid.':'.$region);
        }
        return $cityList;
    }

}