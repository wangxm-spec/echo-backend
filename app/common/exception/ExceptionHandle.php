<?php

namespace app\common\exception;

use ErrorException;
use HttpException;
use Next\VarDumper\Dumper;
use Next\VarDumper\DumperHandler;
use think\exception\ClassNotFoundException;
use think\exception\FuncNotFoundException;
use think\exception\ValidateException;
use think\template\exception\TemplateNotFoundException;
use Throwable;
use Webman\Exception\ExceptionHandler;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\Exception\BusinessException;

class ExceptionHandle extends ExceptionHandler
{
    use DumperHandler;
    public $dontReport = [
        BusinessException::class,
        HttpException::class,
        FuncNotFoundException::class,
        ClassNotFoundException::class,
        TemplateNotFoundException::class,
        ValidateException::class,
        ErrorException::class,
        BaseException::class,
    ];

    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    public function render(Request $request, Throwable $exception): Response
    {
        //return parent::render($request, $e);
        if ($exception instanceof Dumper) {
            return \response(self::convertToHtml($exception));
        }

        if ($exception instanceof BusinessException) {
            if ($request->isAjax()) {
                return json(['status' => 500, 'msg' => $exception->getMessage(), 'data' => '']);
            } else {
                return response($exception->getMessage(), 500);
            }
        }

        // 方法不存在
        if ($exception instanceof FuncNotFoundException) {
            if ($request->isAjax()) {
                return json(['status' => 500, 'msg' => $exception->getFunc() . '方法不存在', 'data' => '']);
            } else {
                return response($exception->getFunc() . '控制器方法不存在', 404);
            }
        }

        // 控制器不存在
        if ($exception instanceof ClassNotFoundException) {
            if ($request->isAjax()) {
                return json(['status' => 500, 'msg' => $exception->getClass() . '控制器不存在', 'data' => '']);
            } else {
                return response($exception->getClass() . '控制器不存在', 404);
            }
        }

        // 模板不存在
        if ($exception instanceof TemplateNotFoundException) {
            return response($exception->getTemplate() . '模板不存在', 404);
        }

        // 验证器异常
        if ($exception instanceof ValidateException) {
            return json(['status' => 500, 'msg' => $exception->getError(), 'data' => '']);
        }

        // 系统层面错误异常
        if ($exception instanceof ErrorException) {
            if ($request->isAjax()) {
                return json(['status' => 500, 'msg' => $exception->getMessage(), 'data' => '']);
            }
            return response($exception->getMessage(), 500);
        }

        // 请求异常
        if ($exception instanceof HttpException) {
            return json(['status' => $exception->getCode(), 'msg' => $exception->getMessage(), 'data' => '']);
        }

        // 自定义基础异常
        if ($exception instanceof BaseException) {
            return json(['status' => $exception->getCode(), 'msg' => $exception->getMessage(), 'data' => '']);
        }

        // 其他错误交给系统处理
        return parent::render($request, $exception);
    }
}