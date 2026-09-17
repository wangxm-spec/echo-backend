<?php

return [
    'enable' => true,
    'mailer' => [
        'scheme'   => 'smtps',// "smtps": using TLS, "smtp": without using TLS.
        'host'     => 'smtp.126.com', // 服务器地址
        'username' => 'abigsoft@126.com', //用户名
        'password' => 'FNRdLy6Qc4GTNkwm', // 密码
        'port'     => 587, // SMTP服务器端口号,一般为25
        'options'  => [], // See: https://symfony.com/doc/current/mailer.html#tls-peer-verification
        //'dsn'      => '',
    ],
    'from'   => [
        'address' => 'abigsoft@126.com',
        'name'    => 'Echo',
    ],
];
