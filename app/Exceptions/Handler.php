<?php

namespace App\Exceptions;

use Exception;
use Slack;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

use App\Exceptions\CreateUserException;
use App\Exceptions\RegisterValidationException;
use App\Exceptions\SearchQueryEmpty;
use App\Exceptions\SendMailException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class
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
        // if (env('APP_ENV') === 'local' && $e->getCode() >= 500) {
        if (env('APP_ENV') === 'local') {
            $this->sendNotification($request = null, $e);
        }

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
        if ($e->getCode() >= 500) {
            $this->sendNotification($request, $e);
        }

        if ($e instanceof ModelNotFoundException) {
            $e = new NotFoundHttpException($e->getMessage(), $e);
        }

        // if ($e instanceof RegisterValidationException) {;
        //     return redirect('login')->withErrors([
        //         $e->getMessage()
        //     ]);
        // }

        if ($e instanceof CreateUserException) {
            return redirect('login')->withErrors([
                'Your account couldn\'t be create please try again'
            ]);
        }

        if ($e instanceof SendMailException) {
            return redirect('login')->with('status', 'Failed to send activation email.');
        }

        return parent::render($request, $e);
    }

    /**
     * [sendNotification description]
     * @param  [type] $request [description]
     * @param  [type] $e       [description]
     * @return [type]          [description]
     */
    private function sendNotification($request = null, $e)
    {
        $attachment = [
            'fallback' => 'Videouri Error',
            'text'     => 'Videouri Error',
            'color'    => '#c0392b',
            'fields'   => [
                [
                    'title' => 'Requested URL',
                    'value' => $request ? $request->url() : '',
                    // 'short' => true,
                ],
                [
                    'title' => 'HTTP Code',
                    'value' => $e->getCode(),
                    // 'short' => true,
                ],
                [
                    'title' => 'Exception',
                    'value' => $e->getMessage(),
                    // 'short' => true,
                ],
                [
                    'title' => 'File',
                    'value' => $e->getFile() . ': ' . $e->getLine(),
                    // 'short' => true,
                ],
                [
                    'title' => 'Trace',
                    'value' => $e->getTraceAsString(),
                    // 'short' => true,
                ],
                [
                    'title' => 'Input',
                    'value' => $request ? json_encode($request->all()) : '',
                    // 'short' => true,
                ],
            ],
        ];

        Slack::attach($attachment)->send('Videouri Error');
    }
}
