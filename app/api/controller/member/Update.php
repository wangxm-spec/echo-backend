<?php

namespace app\api\controller\member;

use app\api\controller\Base;
use app\api\exception\AuthException;
use app\common\model\CommonMbtiTypeModel;
use app\common\model\MemberAccountModel;
use app\common\service\EmailService;
use app\common\service\SmsService;

class Update extends Base
{
    function password(){
        $old_password = $this->request->param('old_password');
        if (!$old_password) {
            return $this->error('请输入原密码');
        }
        $user = MemberAccountModel::where('uuid', $this->uuid)->find();
        if (!$user) {
            throw new AuthException();
        }

        $new_password = $this->request->param('new_password');
        if (!password_verify($old_password, $user['password'])) {
            return $this->error('原密码不正确');
        }
        if (!$new_password) {
            return $this->error('请输入新密码');
        }
        // 与注册保持一致：6-20 位，必须同时包含英文和数字
        if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,20}$/', $new_password)) {
            return $this->error('密码需为6-20位，且同时包含英文和数字');
        }
        MemberAccountModel::where('uuid', $this->uuid)->update([
            'password' => password_hash($new_password, PASSWORD_DEFAULT),
            'update_time' => formatDate(),
        ]);
        return $this->success('修改成功');
    }

    function email(){
        $email = trim((string)$this->request->param('email'));
        $password = $this->request->param('password');
        if (!$password) {
            return $this->error('请输入账号密码');
        }
        $user = MemberAccountModel::where('uuid', $this->uuid)->find();
        if (!password_verify($password, $user['password'])) {
            return $this->error('密码验证失败');
        }
        if (!$this->request->isPost()) {
            if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $this->error('请输入正确的邮箱');
            }
            $captcha_key = $this->request->param('captcha_key');
            $captcha_code = $this->request->param('captcha_code');
            $check = \Tinywan\Captcha\Captcha::check($captcha_code, $captcha_key);
            if(!$check){
                return $this->error('图片验证码错误');
            }
            $exists = MemberAccountModel::where('email', $email)
                ->where('uuid', '<>', $this->uuid)
                ->count();
            if ($exists > 0) {
                return $this->error('该邮箱已被其他账号绑定');
            }
            $msg = EmailService::send('bind', ['email' => $email]);
            if (!env('DATA_DEBUG')) {
                unset($msg['msg_code']);
            }
            return $this->success('发送成功', $msg);
        }else{
            $msg_id = $this->request->param('msg_id');
            $msg_code = $this->request->param('msg_code');
            if (!$msg_id) {
                return $this->error('请先发送验证码');
            }
            if (!$msg_code) {
                return $this->error('请输入验证码');
            }
            if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $this->error('请输入正确的邮箱');
            }
            EmailService::verify($msg_id, 'bind', $email, $msg_code);
            $exists = MemberAccountModel::where('email', $email)
                ->where('uuid', '<>', $this->uuid)
                ->count();
            if ($exists > 0) {
                return $this->error('该邮箱已被其他账号绑定');
            }
            MemberAccountModel::where('uuid', $this->uuid)->update([
                'email' => $email,
                'update_time' => formatDate(),
            ]);
            return $this->success('绑定成功');
        }
    }

    function qq(){
        $user = MemberAccountModel::where('uuid', $this->uuid)->find();
        if (!$user) {
            return $this->error('账号不存在');
        }
        $qq = trim((string)$this->request->param('qq', ''));
        if ($qq !== '') {
            if (!preg_match('/^[1-9]\d{4,12}$/', $qq)) {
                return $this->error('请输入正确的QQ号码');
            }
            $exists = MemberAccountModel::where('qq_number', $qq)
                ->where('uuid', '<>', $this->uuid)
                ->count();
            if ($exists > 0) {
                return $this->error('该QQ已被其他账号绑定');
            }
        }
        MemberAccountModel::where('uuid', $this->uuid)->update([
            'qq_number' => $qq,
            'update_time' => formatDate(),
        ]);
        return $this->success('修改成功');
    }

    function phone(){
        $phone = trim((string)$this->request->param('phone'));
        $password = $this->request->param('password');
        if (!$password) {
            return $this->error('请输入原密码');
        }
        $user = MemberAccountModel::where('uuid', $this->uuid)->find();
        if (!$user) {
            return $this->error('账号不存在');
        }
        if (!password_verify($password, $user['password'])) {
            return $this->error('密码校验失败');
        }
        // 发送验证码
        if (!$this->request->isPost()) {
            if (!$phone || !preg_match('/^1[3-9]\d{9}$/', $phone)) {
                return $this->error('请输入正确的手机号');
            }

            $captcha_key = $this->request->param('captcha_key');
            $captcha_code = $this->request->param('captcha_code');
            $check = \Tinywan\Captcha\Captcha::check($captcha_code, $captcha_key);
            if(!$check){
                return $this->error('图片验证码错误');
            }

            $exists = MemberAccountModel::where('account', $phone)
                ->where('uuid', '<>', $this->uuid)
                ->count();
            if ($exists > 0) {
                return $this->error('该手机号已被其他账号绑定');
            }
            $msg = SmsService::send($phone, 'bind');
            if (!env('DATA_DEBUG')) {
                unset($msg['msg_code']);
            }
            return $this->success('发送成功', $msg);
        }else{
            $msg_id = $this->request->param('msg_id');
            $msg_code = $this->request->param('msg_code');
            if (!$msg_id) {
                return $this->error('请先发送验证码');
            }
            if (!$msg_code) {
                return $this->error('请输入验证码');
            }
            if (!$phone || !preg_match('/^1[3-9]\d{9}$/', $phone)) {
                return $this->error('请输入正确的手机号');
            }
            $exists = MemberAccountModel::where('account', $phone)
                ->where('uuid', '<>', $this->uuid)
                ->count();
            if ($exists > 0) {
                return $this->error('该手机号已被其他账号绑定');
            }
            SmsService::verify($msg_id, 'bind', $phone, $msg_code);
            MemberAccountModel::where('uuid', $this->uuid)->update([
                'account' => $phone,
                'update_time' => formatDate(),
            ]);
            return $this->success('绑定成功');
        }
    }

    function mbti(){
        $user = MemberAccountModel::where('uuid', $this->uuid)->find();
        if (!$user) {
            return $this->error('账号不存在');
        }
        $mbti_type = strtoupper($this->request->param('mbti', ''));
        if ($mbti_type !== '') {
            $check = CommonMbtiTypeModel::where('code', $mbti_type)->where('status', 1)->count();
            if ($check <= 0) {
                return $this->error('请选择正确的MBTI类型');
            }
        }
        MemberAccountModel::where('uuid', $this->uuid)->update([
            'mbti_type' => $mbti_type,
            'update_time' => formatDate(),
        ]);
        return $this->success('修改成功');
    }

    function reset(){
        $user = MemberAccountModel::where('uuid', $this->uuid)->find();
        if (!$user) {
            return $this->error('账号不存在');
        }
        if(!$this->request->isPost()){
            $captcha_key = $this->request->param('captcha_key');
            $captcha_code = $this->request->param('captcha_code');
            $check = \Tinywan\Captcha\Captcha::check($captcha_code, $captcha_key);
            if(!$check){
                return $this->error('验证码错误');
            }
            $msg = SmsService::send($user['account'], 'reset');
            if(!env('DATA_DEBUG')){
                unset($msg['msg_code']);
            }
            return $this->success('发送成功', $msg);
        }
        $password = $this->request->param('password');
        if (!$password) {
            return $this->error('请输入新密码');
        }
        // 与注册保持一致：6-20 位，必须同时包含英文和数字
        if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,20}$/', $password)) {
            return $this->error('密码需为6-20位，且同时包含英文和数字');
        }
        $sms_id = $this->request->param('sms_id');
        $sms_code = $this->request->param('sms_code');
        SmsService::verify($sms_id, 'reset', $user['account'], $sms_code);
        MemberAccountModel::where('uuid', $this->uuid)->update([
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'update_time' => formatDate(),
        ]);
        return $this->success('修改成功');
    }
}
