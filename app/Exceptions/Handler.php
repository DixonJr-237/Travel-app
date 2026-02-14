<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        //
    ];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (Throwable $e, $request) {
            if ($request->expectsJson()) {
                return $this->handleApiException($e);
            }
        });
    }

    protected function handleApiException(Throwable $e)
    {
        $statusCode = 500;
        $message = 'Internal Server Error';
        $errors = [];

        if ($e instanceof AuthenticationException) {
            $statusCode = 401;
            $message = 'Unauthenticated';
        } elseif ($e instanceof ModelNotFoundException) {
            $statusCode = 404;
            $message = 'Resource not found';
        } elseif ($e instanceof ValidationException) {
            $statusCode = 422;
            $message = 'Validation failed';
            $errors = $e->errors();
        } elseif ($e instanceof HttpException) {
            $statusCode = $e->getStatusCode();
            $message = $e->getMessage() ?: 'HTTP Exception';
        }

        // Log error for debugging
        if ($statusCode >= 500) {
            Log::error('API Error: '.$e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);
        }

        $response = [
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ];

        if (config('app.debug')) {
            $response['debug'] = [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTrace(),
            ];
        }

        return response()->json($response, $statusCode);
    }
}
