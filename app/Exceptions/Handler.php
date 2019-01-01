<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $request->expectsJson()
                    ? response()->json(['code' => 0, 'message' => $exception->getMessage()], 401)
                    : redirect()->guest(route('login'));
    }

    protected function getStatusMessage($status = 500, $defaultMessage = '')
    {
        $statusMessage = [
            404 => '找不到你要的页面。',
            429 => '请求频繁',
        ];

        return $statusMessage[$status] ?? $defaultMessage;
    }

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if($exception) {
            if(!method_exists($exception, 'getStatusCode')) {
                return parent::render($request, $exception);
            }

            $code = $exception->getStatusCode();

            $message = $this->getStatusMessage($code);

            if(!$message) {
                return parent::render($request, $exception);
            }

            if($request->ajax()) {
                return error($message, $code);
            }

            $path = '';

            if($request->is('admin/', 'admin/*') && auth()->check() && view()->exists('admin.errors.' . $code)) {
                $path = 'admin.errors.' . $code;
            }

            if(!$path && view()->exists('index.errors.' . $code)) {
                $path = 'index.errors.' . $code;
            } else {
                return parent::render($request, $exception);
            }

            return response()->view($path, [
                'code'      => $code,
                'message'   => $message
            ], $code);
        }

        return parent::render($request, $exception);
    }
}
