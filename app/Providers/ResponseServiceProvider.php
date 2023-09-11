<?php
namespace App\Providers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\Str;

class ResponseServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Response::macro('success', function($message = '', $data = null, $status = 200) {
            $fields = ['status' => true];
            if($message) {
                $fields['message'] = $message;
            }
            if($data) {
                $fields['data'] = $data;
            }

            return Response::make($fields)->setStatusCode($status);
        });

        Response::macro('error', function(string $message = '', $errors = null, $status = 400) {
            $fields = ['status' => false];
            if($message) {
                $fields['message'] = $message;
            }
            if($errors) {
                $fields['errors'] = $errors;
            }

            return Response::make($fields)->setStatusCode($status);
        });

        Response::macro('validationError', function($errors = null, $status = 400) {
            $fields = [
                'status' => false,
                'message' => __('response.validation_error')
            ];
            if($errors) {
                $fields['errors'] = $errors;
            }

            return Response::make($fields)->setStatusCode($status);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
