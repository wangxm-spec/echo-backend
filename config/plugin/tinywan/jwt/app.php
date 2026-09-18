<?php

use app\common\model\MemberAccountModel;

return [
    'enable' => true,
    'jwt' => [
        /** 算法类型 HS256、HS384、HS512、RS256、RS384、RS512、ES256、ES384、ES512、PS256、PS384、PS512 */
        'algorithms' => 'RS256',

        /** access令牌秘钥（安装时自动生成64位随机值） */
        'access_secret_key' => '3ef4f235256989b42d59f68dd6eb614f5f55627a7195cbda840729a4c2dfb8fd',

        /** access令牌过期时间，单位：秒。默认 2 小时 */
        'access_exp' => 86400,

        /** refresh令牌秘钥（安装时自动生成64位随机值） */
        'refresh_secret_key' => '899fea4782aa944f32a6c423a297d24575cb75590bef1ff04375b19e433ee8f7',

        /** refresh令牌过期时间，单位：秒。默认 7 天 */
        'refresh_exp' => 604800,

        /** refresh 令牌是否禁用，默认不禁用 false */
        'refresh_disable' => false,

        /** 令牌签发者 */
        'iss' => 'xming.wang',

        /** 某个时间点后才能访问，单位秒。（如：30 表示当前时间30秒后才能使用） */
        'nbf' => 0,

        /** 时钟偏差冗余时间，单位秒。建议这个余地应该不大于几分钟 */
        'leeway' => 60,

        /** 是否允许单设备登录，默认不允许 false */
        'is_single_device' => true,

        /** 缓存令牌时间，单位：秒。默认 7 天 */
        'cache_token_ttl' => 604800,

        /** 缓存令牌前缀，默认 JWT:TOKEN: */
        'cache_token_pre' => 'JWT:TOKEN:',

        /** 缓存刷新令牌前缀，默认 JWT:REFRESH_TOKEN: */
        'cache_refresh_token_pre' => 'JWT:REFRESH_TOKEN:',

        /** 用户信息模型 */
        'user_model' => function ($uid) {
            return MemberAccountModel::where('uuid',$uid)
                ->field('uuid,account,email,nickname,avatar,status,hope_amount,mbti_type,call_id,create_time,update_time')
                ->find();
        },

        /** 是否支持 get 请求获取令牌 */
        'is_support_get_token' => true,
        /** GET 请求获取令牌请求key */
        'is_support_get_token_key' => 'token',

        /** access令牌私钥 */
        'access_private_key' => <<<EOD
            -----BEGIN RSA PRIVATE KEY-----
            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
            -----END RSA PRIVATE KEY-----
            EOD,

        /** access令牌公钥 */
        'access_public_key' => <<<EOD
            -----BEGIN PUBLIC KEY-----
            MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEAlHRkc/iEVWhUwwsmD1eB
            uIWEbHWn3jI7bX8xVQO4oLKnuoN8U64lohjVI9qmvhek1M+/jQaNH/8fSLbFhvzr
            uNBt8eyHtQo7R1EXV7EZgN5VccpEWWQi63IyGKcqLgxcZUUUo0x1d3MeGfQcURU5
            4AFSZXNe+CmPiKughTIlt2WVNfMFduNiikxurVCMS7/EXtqp47TLBv55gIipjwiw
            zj3NkLWXrrZRIfKIz8/fLwa4Jzpyazw2gpNHWNWfvORZJIX/ForK20rRVuZRDgfK
            HsPln3CRUUBPxsoGT1XN16c0+KVnCYdXV1TyoxqoN3ArB7kwfXsjz2oawsTkyclE
            AmV15XZC9SqL+8lBiNSYdoh1RXwTeWhtD/HCpWnb+8+6GxtqT7s+8WPUe6vHEn0d
            By0kmK2FWW5RBmmQIW31n6KzdCOmYUdirhLowmUFPWRz93v9PzFMdpi5s092Dls1
            EmT3n3Tt/JzFu+9E4j9Q/UuzweqqiLUQBBCCsD0yUC8Tn6Fck98a9GlujfC9fGAV
            pX8L1u/0DOdAV1vK2wMv0ZIyHYcq+DpjlFSilFPhm7VCKmPPvLqMTXV170jEiLa+
            HpR+s3YH+TcCnVwkl6RgrHnBQyd1qT2fTNFpscya5B3J58wdQfDmYZU2bLNd9l6S
            hLrd8CXc4jzNmx3Bie0ED5cCAwEAAQ==
            -----END PUBLIC KEY-----
            EOD,

        /** refresh令牌私钥 */
        'refresh_private_key' => <<<EOD
            -----BEGIN RSA PRIVATE KEY-----
            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
            -----END RSA PRIVATE KEY-----
            EOD,

        /** refresh令牌公钥 */
        'refresh_public_key' => <<<EOD
            -----BEGIN PUBLIC KEY-----
            MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEAvHyh0qFW9mRUmBjdTrJj
            s1R/jL3UoSnCBCMkRImE07dcd+m8z1B8BSBT6YuIpiAPQxM2YR3WxLenyaK3leaL
            NrvGhKkdc58AAQUzF++HTDUwzW600DY4XNlLFYcqn3CIm7s0Oc+PyRijKIEJ9oxL
            RSwI6cG/YWD8wAKHvFubYBydcbaGmLl9cQxUKLsO31A5OURVwzcMwOqEkDb4Am11
            nRRrXV/wQgCuSq20d3aY1b4dBcgolz1hptWggw0HOwQaxrcEAZMvQsgIPQLD9rZp
            ZhoOCvewYha8tiSCbFiqTWRLgB8AoQgkkew1WEr0gOxrqxwNqDbcnmFi9s711YFU
            ojcfHu0XppQPNr15Pwmj4nBWQCm38CGWgOminPgYJfmKn0hnAB6L/kEKPWK4ZKgw
            5IaasJnK8xCSntvpO/127IxDgamKoNRN8BF/xYiZjur4kvpNctJ5tWSFCWLH36QL
            2JGTSPsq4u2YNYiS8dIZDEBYOL3bhTZMFvoxdIRa6nqJ0mhe8cTwpAGx8L21WwCN
            ik3ObC6AwSCN57O/rTB2k378ZhP//rXATPt4EpCxfVAnlVY0CDM2O7h6iGJoKjGI
            kGy2MWSa5T4qN8jDK9FxTpo0n3S5YJY7TpFoVTAeQabR7aLzi9H+c5w7CLvwUZHo
            7ucIVfYtfcwOJvHRJbUK0zMCAwEAAQ==
            -----END PUBLIC KEY-----
            EOD,
    ],
];
