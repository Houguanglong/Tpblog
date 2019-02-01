<?php
/**
 * Created by PhpStorm.
 * User: 357397264@qq.com
 * Date: 2018/6/22
 * Time: 下午2:34
 */

namespace app\sys\home;


use think\Controller;

class Qrcode extends Controller
{

    /**
     * 输出图片
     * @param $content
     * @return string
     */
    public function src($content)
    {
        $qrcode = new \Endroid\QrCode\QrCode($content);
        header('Content-Type: '.$qrcode->getContentType());
        return $qrcode->writeString();
    }

}