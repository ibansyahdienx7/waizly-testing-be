<?php

namespace App\Exceptions;

use Exception;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        'Symfony\Component\HttpKernel\Exception\HttpException'
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (\Exception $e) {
            return response()->json([
                'retcode' => 500,
                'status' => false,
                'msg' => 'Something was wrong',
                'error' => 1,
                'error_detail' => $e
            ], 500);
        });

        $this->renderable(function (\Exception $e) {
            if ($e->getPrevious() instanceof TokenMismatchException) {
                return response()->json([
                    'retcode' => 401,
                    'status' => false,
                    'msg' => 'Unauthorized',
                    'error' => 1,
                    'error_detail' => $e
                ], 401);
            }
        });
    }
}
