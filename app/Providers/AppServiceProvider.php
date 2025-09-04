<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\Facades\Response;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(ResponseFactory $responseFactory): void
    {
        $responseFactory->macro('sendResponse', function ($responseType, $code, $message, $data, $status = HttpResponse::HTTP_BAD_REQUEST) {

            if ($responseType == 'SUCCESS') {
                return response()->json([
                    'code'      =>  $code,
                    'message'   =>  $message,
                    'data'      =>  $data
                ]);
            } else if ($responseType == 'ERROR') {
                return response()->json([
                    'code'      =>  $code,
                    'message'   =>  $message,
                    'error'     =>  [$data]
                ], $status);
            } else {
                return response()->json([
                    'code'  =>  0,
                    'message'    =>  'invalid response type',
                ], $status);
            }
        });
    }
}
