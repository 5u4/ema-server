<?php

namespace App\Exceptions;

use Exception;
use Firebase\JWT\ExpiredException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

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

    /**
     * Report or log an exception.
     *
     * @param  \Exception $exception
     *
     * @return void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $exception
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function render($request, Exception $exception)
    {
        $exception = $this->prepareException($exception);

        if ($exception instanceof ValidationException) {
            $exception = new BadRequestHttpException($exception->getMessage(), $exception);
        } elseif ($exception instanceof ExpiredException) {
            $exception = new UnauthorizedHttpException('', 'Token expired.');
        } elseif ($exception instanceof UnauthorizedException) {
            $exception = new UnauthorizedHttpException('', 'Unauthorized.');
        }

        return $this->prepareJsonResponse($request, $exception);
    }
}
