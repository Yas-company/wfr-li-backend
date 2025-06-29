<?php

namespace App\Exceptions;

use App\Traits\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponse;

    /**
     * A list of the exception types that are not reported.
     */
    protected $dontReport = [];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        //
    }

    public function render($request, Throwable $e)
    {
        if ($request->expectsJson()) {

            // Validation Errors
            if ($e instanceof ValidationException) {
                return $this->validationErrorResponse($e->errors());
            }

            // Not Found Route or Model
            if ($e instanceof NotFoundHttpException) {
                return $this->notFoundResponse('العنصر غير موجود أو الرابط خطأ');
            }

            // Method not allowed
            if ($e instanceof MethodNotAllowedHttpException) {
                return $this->errorResponse('طريقة الوصول غير مسموح بها', null, 405);
            }

            // Unauthorized
            if ($e instanceof AuthenticationException) {
                return $this->unauthorizedResponse('يجب تسجيل الدخول');
            }

            // Unexpected server error
            return $this->serverErrorResponse($e->getMessage());
        }

        return parent::render($request, $e);
    }
}
