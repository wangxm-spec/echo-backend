<?php
// 匹配 /user/123 /user/abc
use Webman\Route;


Route::group('/api', function () {
    Route::post('/test', [app\api\controller\Test::class, 'test']);
});
Route::group('/api', function () {
    Route::post('/test2', [app\api\controller\Test::class, 'test2']);
})->middleware([
    app\api\middleware\JwtAuth::class,
]);
