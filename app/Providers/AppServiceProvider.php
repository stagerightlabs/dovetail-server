<?php

namespace App\Providers;

use HTMLPurifier;
use Illuminate\Http\Request;
use Laravel\Passport\Client;
use Laravel\Passport\Passport;
use App\Billing\PaymentGateway;
use Illuminate\Support\Facades\DB;
use App\Billing\StripePaymentGateway;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Relations\Relation;

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

        $this->registerValidationRules();

        $this->registerMorphMap();

        $this->registerHtmlPurifier();
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

        // Bind service classes
        $this->app->bind(StripePaymentGateway::class, function () {
            return new StripePaymentGateway;
        });

        $this->app->bind(PaymentGateway::class, StripePaymentGateway::class);
    }

    /**
     * Register Application Macros
     *
     * @return void
     */
    protected function registerMacros()
    {
        // Transform a password token into a Json Response
        Response::macro('authorization', function (Request $request) {
            $client = Client::where('password_client', true)
                ->where('secret', config('jwt.token_secret'))
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

        // Retrieve the current organization from the request
        Request::macro('organization', function () {
            return $this->user()->organization ?? null;
        });
    }

    /**
     * Register custom validation rules
     *
     * @return void
     */
    public function registerValidationRules()
    {
        // A case insensitive uniqueness check
        Validator::extend('iunique', function ($attribute, $value, $parameters, $validator) {

            // Usage: 'iunique:users,email_address,NULL,id,account_id,1'
            // Parameters:
            //      0. Table
            //      1. Column
            //      2. Row to be ignored
            //      3. Primary Key name to ignore (default 'id')
            //      4. Where column
            //      5. Where value equals

            // Initialize our validation query
            $query = DB::table($parameters[0]);
            $column = $query->getGrammar()->wrap($parameters[1]);

            // Are we going to ignore any rows?
            $ignore = $parameters[2] ?? null;
            if ($ignore && $ignore != 'null') {
                $primary = $parameters[3] ?? 'id';
                $query->where($primary, '<>', $ignore);
            }

            // Are we adding a where clause?
            $whereColumn = $parameters[4] ?? null;
            $whereValue = $parameters[5] ?? null;
            if ($whereColumn && $whereValue) {
                $query->where($whereColumn, $whereValue);
            }

            // Run the query to confirm validation status
            return ! $query->whereRaw("lower({$column}) = lower(?)", [$value])->exists();
        });


        //
    }

    /**
     * Associate polymorphic column values with their corresponding classes
     *
     * @return void
     */
    public function registerMorphMap()
    {
        Relation::morphMap([
            'notebook' => \App\Notebook::class,
            'organization' => \App\Organization::class,
            'page' => \App\Page::class,
            'user' => \App\User::class,
        ]);
    }

    public function registerHtmlPurifier()
    {
        $this->app->singleton(HTMLPurifier::class, function ($app) {
            $config = \HTMLPurifier_Config::createDefault();
            return new HTMLPurifier($config);
        });
    }
}
