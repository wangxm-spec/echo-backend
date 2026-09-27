<?php

namespace app\admin\controller;

use app\admin\model\AdminRoleModel;
use app\admin\model\AdminUserModel;
use Webman\Captcha\CaptchaBuilder;
use Webman\Captcha\PhraseBuilder;

class Login extends Base
{
    function index(){
        if(!$this->request->isPost()){
            return view();
        }
        $account = $this->request->param('username');
        $password = $this->request->param('password');

        $verify = $this->request->post('verify');
        if ($verify != $this->request->session()->get('captcha')) {
            $this->request->session()->delete('captcha');
            return $this->error('输入的验证码不正确');
        }
        $this->request->session()->delete('captcha');
        $user = AdminUserModel::where('account', $account)
            ->where('status', 1)
            ->find();
        if(!$user || !password_verify($password, $user['password'])){
            return $this->error('账号或密码不正确');
        }
        $this->request->session()->set('admin_id', $user['id']);
        AdminUserModel::where('id', $user['id'])
            ->update([
                'last_login_time' => formatDate(),
                'last_login_ip' => $this->request->ip()
            ]);
        if($user['is_super_admin']){
            $this->request->session()->set('admin_role', '__ALL__');
        }else{
            $permissions = AdminRoleModel::where('id', $user['role_id'])->where('status', 1)->value('permissions');
            $this->request->session()->set('admin_role', $permissions);
        }
        return $this->success('登陆成功');
    }

    /**
     * 生成验证码
     */
    public function verify()
    {
        $builder = new PhraseBuilder(4, '01234567890');
        $captcha = new CaptchaBuilder(null, $builder);
        $captcha->build();
        $this->request->session()->set('captcha', strtolower($captcha->getPhrase()));
        $img_content = $captcha->get();
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }

    /**
     * 退出登录
     */
    public function logout()
    {
        $session = $this->request->session();
        $session->delete('admin_id');
        $session->delete('admin_role');
        if ($this->request->isAjax()) {
            return $this->success('退出成功');
        }
        redirect(url('login/index'));
    }
}