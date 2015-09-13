<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Videouri\Exceptions\SearchQueryEmpty;
use Videouri\Exceptions\CreateUserException;
use Videouri\Exceptions\RegisterValidationException;
use Videouri\Exceptions\SendMailException;

class Handler extends ExceptionHandler {

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        'Symfony\Component\HttpKernel\Exception\HttpException'
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
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
    public function render($request, Exception $e)
    {
        if ($e instanceof RegisterValidationException) {
            return redirect('join')->withErrors($e->getMessage());
        }
        
        if ($e instanceof CreateUserException) {
            return redirect('join')->withErrors('Your account couldn\'t be create please try again');
        }

        if ($e instanceof SendMailException) {
            return redirect('login')->with('status', 'Failed to send activation email.');
        }

        return parent::render($request, $e);
    }

}
