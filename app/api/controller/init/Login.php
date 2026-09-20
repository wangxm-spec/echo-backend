<?php

namespace app\api\controller\init;

use app\api\controller\Base;
use app\common\exception\ParamException;
use app\common\exception\SystemException;
use app\common\model\MemberAccountModel;
use app\common\service\SmsService;

class Login extends Base
{
    public function __construct()
    {
        parent::__construct();
        if(!config('sys.site_status')){
            throw new SystemException();
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
        $msg = SmsService::send($phone, 'login');
        if(!env('DATA_DEBUG')){
            unset($msg['msg_code']);
        }
        return $this->success('发送成功', $msg);
    }

    function account(){
        $account = $this->request->param('account');
        $password = $this->request->param('password');
        if(!$account){
            return $this->error('请填写正确的用户名');
        }
        if(!$password){
            return $this->error('请填写正确的密码');
        }
        $user = MemberAccountModel::where('account', $account)
            ->find();
        if(!$user){
            return $this->error('账号或密码不正确');
        }
        if(!env('DATA_DEBUG') && !password_verify($password, $user['password'])){
            return $this->error('账号或密码不正确');
        }
        if($user['status'] != 1){
            return $this->error('账号已禁用，请联系管理员');
        }
        return $this->success('登陆成功', $this->makeToken($user['uuid']));
    }

    function phone(){
        $sms_id = $this->request->param('sms_id');
        $sms_code = $this->request->param('sms_code');
        if(!$sms_id){
            return $this->error('请先发送验证码');
        }
        if(!$sms_code){
            return $this->error('请输入验证码');
        }
        $phone = $this->request->param('phone');
        SmsService::verify($sms_id, 'login', $phone, $sms_code);
        $user = MemberAccountModel::where('account', $phone)
            ->find();
        if(!$user){
            return $this->error('账号或密码不正确');
        }
        if($user['status'] != 1){
            return $this->error('账号已禁用，请联系管理员');
        }
        return $this->success('登陆成功', $this->makeToken($user['uuid']));
    }

    protected function makeToken($uuid): array
    {
        return  \Tinywan\Jwt\JwtToken::generateToken([
            'id' => $uuid,
            'client' => $this->platform
        ]);
    }
}