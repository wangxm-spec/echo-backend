<?php

namespace app\common\service;

use app\common\exception\ParamException;
use app\common\model\ServiceEmailLogModel;
use http\Env;
use yzh52521\mailer\Mailer;

class EmailService
{
    /**
     * 验证码默认有效期（秒）
     * @var int
     */
    protected static int $expire = 300;

    /**
     * 验证码最大可校验次数
     * @var int
     */
    protected static int $errorLimit = 3;

    /**
     * 邮件模板配置
     *
     *  type     模板标识，send() 第一个参数
     *  subject  邮件主题，支持 {变量} 替换
     *  template 模板文件名，存放于 app/common/template/email/ 目录
     *  param    模板中需要替换的变量，必须由 $param 传入（code 类型自动生成）
     *  code     是否生成并校验验证码
     *  expire   验证码有效期（秒），仅 code = true 时生效
     *
     * @var array
     */
    protected static array $template = [
        // 邮箱绑定验证码
        'bind' => [
            'subject'  => '【{site_name}】邮箱绑定验证码',
            'template' => 'bind.html',
            'param'    => ['code', 'expire'],
            'code'     => true,
            'expire'   => 300,
        ],
        // 重置密码提醒
        'password' => [
            'subject'  => '【{site_name}】密码重置提醒',
            'template' => 'password.html',
            'param'    => ['create_time'],
            'code'     => false,
        ],
        // 订单提醒
        'order' => [
            'subject'  => '【{site_name}】订单提醒',
            'template' => 'order.html',
            'param'    => ['order_sn', 'title', 'price', 'create_time'],
            'code'     => false,
        ],
    ];

    /**
     * 发送邮件
     *
     * @param string $type  模板类型 bind|password|order
     * @param array  $param 参数，必须包含 email（收件人）以及对应模板 param 中声明的全部变量
     *                      例：['email' => 'a@b.com']
     *                          ['email' => 'a@b.com', 'create_time' => formatDate()]
     *                          ['email' => 'a@b.com', 'order_sn' => '2026...', 'title' => '会员', 'price' => 19.9, 'create_time' => formatDate()]
     * @return array ['msg_id' => 日志ID, 'msg_code' => 验证码（非验证码模板为 null）]
     * @throws ParamException
     */
    public static function send(string $type, array $param): array
    {
        $tpl = self::template($type);

        // 收件人
        $to = trim($param['email'] ?? '');
        if (!$to || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            throw new ParamException('邮箱地址不正确');
        }

        // 验证码类型：自动生成验证码与有效期文案
        $code = '';
        if (!empty($tpl['code'])) {
            $code             = random(6, 'number');
            $param['code']    = $code;
            $param['expire']  = intdiv($tpl['expire'] ?? self::$expire, 60);
        }

        // 校验模板所需变量是否齐全
        foreach ($tpl['param'] as $field) {
            if (!isset($param[$field]) || $param[$field] === '') {
                throw new ParamException('缺少参数：' . $field);
            }
        }

        // 组装模板变量（公共变量 + 模板声明变量）
        $vars = self::buildVars($tpl, $param);

        // 读取模板文件
        $html = self::templateContent($tpl['template']);

        // 主题变量替换
        $subject = self::fill($tpl['subject'], $vars);
        try {
            // 注意：Mailer::instance() 是单例，webman 常驻进程下 message 不会重置，
            // 会累积上一次的收件人/内容，所以这里每次 new 一个实例发送。
            (new \yzh52521\mailer\mail\Mailer())
                ->setTo($to)
                ->setSubject($subject)
                ->setHtmlBody($html, $vars)
                ->send();
        } catch (\Throwable $e) {
            throw new ParamException('邮件发送失败：' . $e->getMessage());
        }

        // 记录发送日志
        $log = ServiceEmailLogModel::create([
            'email'       => $to,
            'code'        => $code,
            'type'        => $type,
            'subject'     => $subject,
            'content'     => $html,
            'status'      => 0,
            'error_count' => self::$errorLimit,
            'create_time' => formatDate(),
            'update_time' => formatDate(),
        ]);

        return [
            'msg_id'   => $log->id,
            'msg_code' => $code ?: null,
        ];
    }

    /**
     * 校验验证码（仅 code = true 的模板可用）
     *
     * @param int    $id    发送时返回的 msg_id
     * @param string $type  模板类型
     * @param string $email 收件邮箱
     * @param string $code  用户填写的验证码
     * @return bool
     * @throws ParamException
     */
    public static function verify($id, string $type, string $email, $code): bool
    {
        if (!isset(self::$template[$type])) {
            throw new ParamException('邮件模板不存在');
        }

        $log = ServiceEmailLogModel::where('id', $id)
            ->where('email', $email)
            ->where('type', $type)
            ->find();

        if (!$log) {
            throw new ParamException('请先获取验证码');
        }

        $expire = self::$template[$type]['expire'] ?? self::$expire;
        if (time() - strtotime($log['create_time']) > $expire) {
            throw new ParamException('验证码已过期');
        }

        if ($log['status'] != 0 || $log['error_count'] <= 0) {
            throw new ParamException('验证码已失效');
        }

        if ((string)$log['code'] !== (string)$code) {
            ServiceEmailLogModel::where('id', $id)->dec('error_count')->update();
            throw new ParamException('验证码有误');
        }

        ServiceEmailLogModel::where('id', $id)->update([
            'status'      => 1,
            'use_time'    => formatDate(),
            'update_time' => formatDate(),
        ]);

        return true;
    }

    /**
     * 渲染邮件内容（不发送，便于本地预览模板效果）
     *
     * @param string $type
     * @param array  $param
     * @return string
     */
    public static function render(string $type, array $param = []): string
    {
        $tpl = self::template($type);

        if (!empty($tpl['code'])) {
            $param['code']   = $param['code'] ?? '123456';
            $param['expire'] = $param['expire'] ?? intdiv($tpl['expire'] ?? self::$expire, 60);
        }

        return self::fill(self::templateContent($tpl['template']), self::buildVars($tpl, $param));
    }

    /**
     * 获取模板配置
     *
     * @param string $type
     * @return array
     * @throws ParamException
     */
    protected static function template(string $type): array
    {
        if (!isset(self::$template[$type])) {
            throw new ParamException('邮件模板不存在');
        }
        return self::$template[$type];
    }

    /**
     * 组装模板变量：公共变量 + 模板声明变量
     *
     * @param array $tpl
     * @param array $param
     * @return array
     */
    protected static function buildVars(array $tpl, array $param): array
    {
        return array_merge([
            'site_name' => env('APP_NAME'),
            'year'      => date('Y'),
            'send_time' => formatDate(),
        ], array_intersect_key($param, array_flip($tpl['param'])));
    }

    /**
     * 读取模板文件内容
     *
     * @param string $file
     * @return string
     * @throws ParamException
     */
    protected static function templateContent(string $file): string
    {
        $path = app_path() . '/common/template/email/' . $file;
        if (!is_file($path)) {
            throw new ParamException('邮件模板文件不存在：' . $file);
        }
        return (string)file_get_contents($path);
    }

    /**
     * 将 {变量} 替换为实际值
     *
     * @param string $content
     * @param array  $vars
     * @return string
     */
    protected static function fill(string $content, array $vars): string
    {
        $replace = [];
        foreach ($vars as $key => $value) {
            $replace['{' . $key . '}'] = (string)$value;
        }
        return strtr($content, $replace);
    }
}
