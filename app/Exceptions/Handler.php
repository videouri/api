<?php

namespace App\Exceptions;

use App\Exceptions\SocialAuthException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(\Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, \Exception $e)
    {
        if ($e instanceof SocialAuthException) {
            return redirect('login')->withErrors([
                'There was an error registering your account. Please try again',
            ]);
        }

        if ($this->isHttpException($e)) {
            return $this->toIlluminateResponse($this->renderHttpException($e), $e);
        } else {
            // return $this->toIlluminateResponse($this->convertExceptionToResponse($e), $e);

            if (app()->environment() === 'production') {
                $e = new \Symfony\Component\HttpKernel\Exception\HttpException(500);
            }
        }

        return parent::render($request, $e);
    }
}
