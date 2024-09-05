<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
           'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'user.status'=> \App\Http\Middleware\BlockMiddleware::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
    $exceptions->renderable(function (ValidationException $exception) {
        return response()->json([
            "status" => "invalid",
            "message" => "Request body is not valid.",
            "violations" => collect($exception->errors())->map(function ($error) {
                return [
                  'message' => collect($error)->join('')
                ];
            })
        ],400);


    });

    $exceptions->renderable(function (\Illuminate\Auth\AuthenticationException $exception) {
        return response()->json([
            "status" => "unauthenticated",
            "message"=> 'Missing Token'
        ],401);
    });


    $exceptions->renderable(function (\Illuminate\Http\Exceptions\HttpResponseException $exception){
        return response()->json([
            "status"=>"not-found",
            "message"=> "Not found"
        ],404);
    });

    })->create();
