<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Laravel\Passport\Client;
use Laravel\Passport\Passport;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Passport::routes(function ($router) {
            $router->forAccessTokens();
        });

        $this->registerMacros();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Load all of the helper function files.
        foreach (glob(app_path() . '/Helpers/*.php') as $fileName) {
            require_once($fileName);
        }
    }

    /**
     * Register Application Macros
     *
     * @return void
     */
    protected function registerMacros()
    {
        Response::macro('authorization', function (Request $request) {
            $client = Client::where('password_client', true)
                ->where('secret', env('TOKEN_SECRET'))
                ->where('revoked', false)
                ->first();

            if (!$client) {
                return response()->json(['message' => 'Token Service Unavailable'], 503);
            }

            return app()->handle(
                Request::create('/oauth/token', 'POST', [
                    'grant_type' => 'password',
                    'client_id' => $client->id,
                    'client_secret' => $client->secret,
                    'username' => request('email'),
                    'password' => request('password'),
                ])
            );
        });
    }
}
