<?php
/**
 *
 * @copyright JungoPhpFramework 深圳俊网网络有限公司 http://www.junnet.net/
 * @author 吴跃忠 <357397264@qq.com>
 */

namespace app\sys\model;


use think\Db;
use think\Model;

class Message extends Model
{


    /**
     * 发送手机验证码
     * @param array $data
     * @param bool $captcha 是否需要图形验证码
     * @throws \Exception
     * @return mixed
     */
    public function sendSmsCode($data,$captcha = true)
    {
        if (empty($data['mobile'])) {
            throw new \Exception('请输入手机号码');
        } elseif ( $captcha === true && empty($data['code'])) {
            throw new \Exception('请输入图形验证码');
        }
        // 验证手机号码正确性
        $mobile = $data['mobile'];
        $checkM = '/^1[35678]\d{9}$/';
        if (!preg_match($checkM, $mobile)) {
            throw new \Exception('手机号码格式错误');
        }
        if ( $captcha === true && !captcha_check($data['code'])) {
            throw new \Exception('图形验证码验证失败，请重试');
        };
        $sess_id = session_id();
        $smsCode = rand(100000, 999999);
        $end_time = NOW_TIME + tb_config('sms_code_time', 1);
        $codeData = [
            'mobile' => $data['mobile'],
            'session_id' => $sess_id,
            'add_time' => date('Y-m-d H:i:s'),
            'end_time' => $end_time,
            'code' => $smsCode
        ];
        $res = Db::name('admin_sms_code')->insert($codeData);
        if ($res !== false) {
            $res = sendMsg(0,$data['mobile'],'user_reg',['code'=>$smsCode]);
            if ( $res['code'] == 1 ){
                return $smsCode;
            }
        }
        throw new \Exception('验证码发送失败,请重试!',1);
    }


    /**
     * 验证手机验证码
     * @param string $mobile
     * @param string $code
     * @return bool
     */
    public function checkSmsCode($mobile = '', $code = '')
    {
        $sess_id = session_id();
        $code_serv = Db::name('admin_sms_code')->where(['mobile' => $mobile, 'session_id' => $sess_id, 'is_used' => '0'])->where('end_time', '>', NOW_TIME)->order(['id' => 'DESC'])->find();
        // 不论验证码是否验证通过，均将该验证码弃用
        if (empty($code_serv) || $code != $code_serv['code']) {
            return false;
        } else {
            Db::name('admin_sms_code')->where('id', $code_serv['id'])->setField('is_used', 1);
            return true;
        }
    }

}