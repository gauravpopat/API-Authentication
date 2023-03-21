<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Models\Error;
use Throwable;
use Illuminate\Support\Facades\Auth;

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
        AuthorizationException::class,
        HttpException::class,
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
     */
    public function register()
    {
        $this->reportable(function (Throwable $exception) {
            // only create entries if app environment is not local
            if (!app()->environment('local')) {
                $user_id = 0;


                if (Auth::user()) {
                    $user_id = Auth::user()->id;
                }

                $data = array(
                    'user_id'   => $user_id,
                    'code'      => $exception->getCode(),
                    'file'      => $exception->getFile(),
                    'line'      => $exception->getLine(),
                    'message'   => $exception->getMessage(),
                    'trace'     => $exception->getTraceAsString(),
                );

                Error::create($data);
            }
        });
    }
}
