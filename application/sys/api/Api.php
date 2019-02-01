<?php
/**
 * Created by PhpStorm.
 * User: 357397264@qq.com
 * Date: 2018/7/3
 * Time: 上午10:37
 */

namespace app\sys\api;

trait Api
{

    /**
     * 返回的状态码
     * @var array
     */
    protected static $http = [
        100 => "HTTP/1.1 100 Continue",
        101 => "HTTP/1.1 101 Switching Protocols",
        200 => "HTTP/1.1 200 OK",
        201 => "HTTP/1.1 201 Created",
        202 => "HTTP/1.1 202 Accepted",
        203 => "HTTP/1.1 203 Non-Authoritative Information",
        204 => "HTTP/1.1 204 No Content",
        205 => "HTTP/1.1 205 Reset Content",
        206 => "HTTP/1.1 206 Partial Content",
        300 => "HTTP/1.1 300 Multiple Choices",
        301 => "HTTP/1.1 301 Moved Permanently",
        302 => "HTTP/1.1 302 Found",
        303 => "HTTP/1.1 303 See Other",
        304 => "HTTP/1.1 304 Not Modified",
        305 => "HTTP/1.1 305 Use Proxy",
        307 => "HTTP/1.1 307 Temporary Redirect",
        400 => "HTTP/1.1 400 Bad Request",
        401 => "HTTP/1.1 401 Unauthorized",
        402 => "HTTP/1.1 402 Payment Required",
        403 => "HTTP/1.1 403 Forbidden",
        404 => "HTTP/1.1 404 Not Found",
        405 => "HTTP/1.1 405 Method Not Allowed",
        406 => "HTTP/1.1 406 Not Acceptable",
        407 => "HTTP/1.1 407 Proxy Authentication Required",
        408 => "HTTP/1.1 408 Request Time-out",
        409 => "HTTP/1.1 409 Conflict",
        410 => "HTTP/1.1 410 Gone",
        411 => "HTTP/1.1 411 Length Required",
        412 => "HTTP/1.1 412 Precondition Failed",
        413 => "HTTP/1.1 413 Request Entity Too Large",
        414 => "HTTP/1.1 414 Request-URI Too Large",
        415 => "HTTP/1.1 415 Unsupported Media Type",
        416 => "HTTP/1.1 416 Requested range not satisfiable",
        417 => "HTTP/1.1 417 Expectation Failed",
        500 => "HTTP/1.1 500 Internal Server Error",
        501 => "HTTP/1.1 501 Not Implemented",
        502 => "HTTP/1.1 502 Bad Gateway",
        503 => "HTTP/1.1 503 Service Unavailable",
        504 => "HTTP/1.1 504 Gateway Time-out"
    ];


    /**
     * 接口配置
     * @var array
     */
    protected $config = [
        ''
    ];


    /**
     * 返回数据
     * @var array
     */
    protected $assignData = [];


    /**
     * 返回码
     * @var int
     */
    protected $code;


    /**
     * 返回消息
     * @var string
     */
    protected $message = '';


    /**
     * 返回url
     * @var string
     */
    protected $url = '';


    /**
     * 初始化
     * @return mixed
     */
    public function _initialize()
    {
        // todo 权限验证
        parent::_initialize();
    }


    /**
     * @param mixed $name
     * @param string $value
     * @return $this
     */
    public function assign($name, $value = '')
    {
        parent::assign($name,$value);
        if (is_array($name)) {
            foreach ($name as $key => $item) {
                $this->assignData[$key] = $item;
            }
        } else {
            $this->assignData[$name] = $value;
        }
        return $this;
    }


    /**
     * 返回地址跳转
     * @param $url
     * @param array $params
     * @param int $code
     */
    public function redirect($url, $params = [], $code = 302)
    {
        $this->code = $code;
        $this->assignData['url'] = $url;
        echo $this->return_client();
        die;
    }


    /**
     * 失败输出
     * @param string $msg
     * @param null $url
     * @param string $data
     * @param int $wait
     * @param array $header
     * @return mixed
     */
    public function error($msg = '', $url = NULL, $data = '', $wait = 3, array $header = [])
    {
        if (empty($this->code)) {
            $this->set_code(500);
        }
        $this->set_message($msg)->set_url($url)->assign($data);
        return $this->return_client();
    }


    /**
     * 成功输出
     * @param string $msg
     * @param null $url
     * @param string $data
     * @param int $wait
     * @param array $header
     * @return mixed
     */
    public function success($msg = '', $url = NULL, $data = '', $wait = 3, array $header = [])
    {
        if( is_array($msg) ){
            $this->assign($msg);
            $msg = '';
        }
        $this->set_message($msg)->set_url($url)->assign($data);
        return $this->return_client();
    }


    /**
     * 设置返回码
     * @param int $code 返回码
     * @return $this
     */
    public function set_code(int $code)
    {
        $this->code = $code;
        return $this;
    }


    /**
     * 设置返回消息
     * @param string $message 返回消息
     * @return $this
     */
    public function set_message(string $message)
    {
        $this->message = $message;
        return $this;
    }


    /**
     * 设置跳转链接
     * @param string $url 跳转连接
     * @return $this
     */
    public function set_url($url)
    {
        $this->url = $url;
        return $this;
    }


    /**
     * 返回数据
     * @param string $template
     * @param array $vars
     * @param array $replace
     * @param array $config
     * @return mixed
     */
    public function fetch($template = '', $vars = [], $replace = [], $config = [])
    {
        return $this->return_client();
    }


    /**
     * 返回到客户端
     * @return mixed
     */
    public function return_client()
    {
        unset($this->assignData['user']);
        header('Content-Type: text/json;charset=UTF-8');
        if (empty($this->code)) {
            $this->code = 200;
        }
        header(self::$http[$this->code]);
        $return_data = [
            'code' => $this->code,
            'message' => $this->message,
            'data' => array_filter($this->assignData),
        ];
        if (!empty($this->url)) {
            $return_data['url'] = $this->url;
        }
        return json_encode(array_filter($return_data));
    }

}