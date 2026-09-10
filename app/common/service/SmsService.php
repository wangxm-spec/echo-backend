<?php

namespace app\common\service;

use app\common\exception\ParamException;
use app\common\model\ServiceSmsLogModel;
use Curl\Curl;

class SmsService
{
    protected static $error_limit = 3;
    protected static $exp_time = 300;
    public static function send($phone, $type):bool
    {
        $code = random(4,'number');
        $key = 'wezjVRW5jgLk';
        $smsConf = [
            'code' => $code,
            'targets' => $phone,
            'name' => '验证码',
            'time' => 5,
        ];
        $curl = new Curl();
        $curl->post('https://push.spug.cc/send/' . $key, $smsConf);
        $content = $curl->response;
        if ($content){
            $result = json_decode($content, true);
            if ($result['code'] != 200) {
                throw new ParamException($result['reason']);
            }
            ServiceSmsLogModel::create([
                'mobile' => $phone,
                'code' => $code,
                'type' => $type,
                'status' => 0,
                'error_count' => 0,
                'use_time' => null,
                'create_time' => formatDate(),
                'update_time' => formatDate()
            ]);
        }else{
            throw new ParamException('请求失败');
        }
        return true;
    }

    public static function verify($id, $type, $phone, $code)
    {
        $last = ServiceSmsLogModel::where('id', $id)
            ->where('mobile', $phone)
            ->where('type', $type)
            ->field('id,code,verify_count,status')
            ->find();

        if (!$last) {
            throw new ParamException('请先发送验证码');
        }

        if (time() - strtotime($last['send_time']) > self::$exp_time) {
            throw new ParamException('验证码已过期');
        }

        if ($last['status'] != 0 || $last['verify_count'] >= self::$error_limit) {
            throw new ParamException('验证码已失效');
        }

        if ($last['code'] != $code) {
            ServiceSmsLogModel::where('id', $id)->inc('verify_count')->update();
            throw new ParamException('验证码有误');
        }
        self::commit($id);
        return true;
    }

    private static function commit($id)
    {
        ServiceSmsLogModel::where('id', $id)->update([
            'status' => 1,
            'update_time' => formatDate()
        ]);
        return true;
    }
}