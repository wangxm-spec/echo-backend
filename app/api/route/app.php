<?php
// 匹配 /user/123 /user/abc
use Webman\Route;


Route::group('/api', function () {
    Route::group('/common', function () {
        Route::group('/captcha', function () {
            Route::post('/build', [app\api\controller\common\Captcha::class, 'build']);
        });
    });
    Route::group('/init', function () {
        Route::group('/login', function () {
            Route::post('/sms', [app\api\controller\init\Login::class, 'sms']);
            Route::post('/account', [app\api\controller\init\Login::class, 'account']);
            Route::post('/phone', [app\api\controller\init\Login::class, 'phone']);
        });
        Route::group('/register', function () {
            Route::post('/sms', [app\api\controller\init\Register::class, 'sms']);
            Route::post('/account', [app\api\controller\init\Register::class, 'account']);
        });
    });
});
Route::group('/api', function () {
    Route::group('/init', function () {
        Route::group('/mbti', function () {
            Route::post('/type', [app\api\controller\init\Mbti::class, 'type']);
            Route::post('/question', [app\api\controller\init\Mbti::class, 'question']);
            Route::post('/calculator', [app\api\controller\init\Mbti::class, 'calculator']);
        });
        Route::group('/character', function () {
            Route::post('/build', [app\api\controller\init\Character::class, 'build']);
        });
    });
    Route::group('/member', function () {
        Route::group('/info', function () {
            Route::post('/account', [app\api\controller\member\Info::class, 'info']);
            Route::post('/device', [app\api\controller\member\Info::class, 'device']);
            Route::post('/character', [app\api\controller\member\Info::class, 'character']);
        });
        Route::group('/update', function () {
            Route::post('/password', [app\api\controller\member\Update::class, 'password']);
            Route::any('/email', [app\api\controller\member\Update::class, 'email']);
            Route::post('/qq', [app\api\controller\member\Update::class, 'qq']);
            Route::any('/phone', [app\api\controller\member\Update::class, 'phone']);
            Route::post('/mbti', [app\api\controller\member\Update::class, 'mbti']);
            Route::any('/reset', [app\api\controller\member\Update::class, 'reset']);
        });
    });
})->middleware([
    app\api\middleware\JwtAuth::class,
]);
