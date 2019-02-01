<?php
/**
 * API基础控制器
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
namespace app\sys\api;


use think\Controller;

class Base extends Controller
{

    /**
     * api输出数据
     * @var array
     */
    protected $data = [];


    /**
     * 设置api输出数据
     * @param $key
     * @param string $value
     */
    public function setData($key,$value = '')
    {
        if( is_array($key) ){
            foreach ($key as $key2=>$value){
                $this->data[$key2] = $value;
            }
        }else{
            $this->data[$key] = $value;
        }
    }


    /**
     * 返回JSON数据
     * @param int|array $code
     * @param array $data
     * @return mixed
     */
    public function returnJson($code = 200,$data = [])
    {
        header('Content-Type: text/json');
        if( is_string($data) ){
            $message = $data;
            $data = [];
            $data['message'] = $message;
        }
        $data = array_merge($this->data,$data);
        if( empty($data) && is_array($code) ){
            $data = $code;
            $code = 200;
        }else{
            if( !is_numeric($code) ){
                $data = $code;
                $code = 200;
            }
            if( is_string($data) ){
                $message = $data;
                $data = [];
                $data['message'] = $message;
            }
        }
        $data['sessionid'] = session_id();
        echo json_encode(["code"=>$code,"data"=>$data]);
        die;
    }

}