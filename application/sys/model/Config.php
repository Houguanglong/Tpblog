<?php
/**
 *
 *                             _ooOoo_
 *                            o8888888o
 *                            88" . "88
 *                            (| -_- |)
 *                            O\  =  /O
 *                         ____/`---'\____
 *                       .'  \\|     |//  `.
 *                      /  \\|||  :  |||//  \
 *                     /  _||||| -:- |||||-  \
 *                     |   | \\\  -  /// |   |
 *                     | \_|  ''\---/''  |   |
 *                     \  .-\__  `-`  ___/-. /
 *                   ___`. .'  /--.--\  `. . __
 *                ."" '<  `.___\_<|>_/___.'  >'"".
 *               | | :  `- \`.;`\ _ /`;.`/ - ` : | |
 *               \  \ `-.   \_ __\ /__ _/   .-` /  /
 *          ======`-.____`-.___\_____/___.-`____.-'======
 *                             `=---='
 *          ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
 *                     佛祖保佑        永无BUG
 *
 * @copyright JungoPhpFramework 深圳俊网网络有限公司 http://www.junnet.net/
 * @author 吴跃忠 <357397264@qq.com>
 */

namespace app\sys\model;


use think\Model;

class Config extends Model
{
    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
        $this->name('admin_config');
    }

    /**
     * 配置列表
     * @param string $group 配置组
     * @param bool $status 是否查询状态
     * @return mixed
     */
    public function configList($group,$status = false)
    {
        $where = [
            'group' => $group,
        ];
        if( $status === true ){
            $where['status'] = 1;
        }
        $list = $this->where($where)->select();
        return $list;
    }


    /**
     * 验证码配置列表
     * @param bool $cleanCache 是否清楚缓存
     * @return mixed
     */
    public function captchaConfig($cleanCache = false)
    {
        if( empty(cache('captchaConfig')) || $cleanCache == true ){
            $lists = $this->configList('captcha',true);
            $captchaList = [];
            foreach ( $lists as $key=>$item ){
                $name = substr($item['name'],8);
                $captchaList[$name] = $item['value'];
            }
            cache('captchaConfig',$captchaList);
        }
        $captchaList = cache('captchaConfig');
        return $captchaList;

    }


    /**
     * 更新缓存
     * @param $id
     */
    public function updateCache($id)
    {
        $config = getTableValue('admin_config', ['id' => $id]);
        cache('config:' . $config['name'], null, 'system-config');
        cache('config:' . $config['name'], $config, 'system-config');
    }


    /**
     * 更改事件
     * @return void
     */
    public function eventUpdate()
    {
        $data = func_get_args();
//        if( !empty($data['id']) ){
            foreach ($data as $key=>$item){
                $this->updateCache($item['id']);
            }
//        }
        $this->captchaConfig(true);
    }

}