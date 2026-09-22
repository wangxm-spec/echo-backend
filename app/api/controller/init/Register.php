<?php

namespace app\api\controller\init;

use app\api\controller\Base;
use app\common\exception\ParamException;
use app\common\exception\SystemException;
use app\common\model\MemberAccountModel;
use app\common\service\SmsService;

class Register extends Base
{
    public function __construct()
    {
        parent::__construct();
        if(!sys('site_status')){
            throw new SystemException();
        }
        if(!sys('register_status')){
            throw new ParamException('已关闭注册，请注意查看公告');
        }
    }

    function sms()
    {
        $phone = $this->request->param('phone');
        $captcha_key = $this->request->param('captcha_key');
        $captcha_code = $this->request->param('captcha_code');
        $check = \Tinywan\Captcha\Captcha::check($captcha_code, $captcha_key);
        if(!$check){
            return $this->error('验证码错误');
        }
        $msg = SmsService::send($phone, 'register');
        if(!env('DATA_DEBUG')){
            unset($msg['msg_code']);
        }
        return $this->success('发送成功',$msg);
    }

    function account(){
        $account = $this->request->param('account');

        $sms_id = $this->request->param('sms_id');
        $sms_code = $this->request->param('sms_code');
        $password = $this->request->param('password');
        // 密码校验：6-20 位，必须同时包含英文和数字
        if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,20}$/', $password)) {
            return $this->error('密码需为6-20位，且同时包含英文和数字');
        }
        SmsService::verify($sms_id, 'register', $account, $sms_code);

        $check = MemberAccountModel::where('account', $account)->count();
        if($check > 0){
            return $this->error('该账号已被注册');
        }

        MemberAccountModel::create([
            'uuid' => uuid(),
            'account' => $account,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'salt' => random(6),
            'email' => '',
            'nickname' => '用户-' . random(6, 'number'),
            'qq_number' => '',
            'avatar' => '/static/img/avatar.png',
            'status' => 1,
            'hope_amount' => 0,
            'mbti_type' => '',
            'create_time' => formatDate(),
            'update_time' => formatDate(),
        ]);
        return $this->success('注册成功');
    }
}