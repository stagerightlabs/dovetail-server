<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\AccessLevel;
use App\Organization;
use Illuminate\Http\Request;
use App\Billing\PaymentGateway;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /**
     * @var PaymentGateway
     */
    protected $billing;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PaymentGateway $billing)
    {
        $this->middleware('guest');

        $this->billing = $billing;
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        return response()->authorization($request);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'organization' => 'required|string|unique:organizations,name',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $organization = Organization::create(['name' => $data['organization']]);

        $this->billing->subscribe($organization, $data['email'])
            ->to('vip')
            ->charge('tok_visa');

        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'access_level' => AccessLevel::$ORGANIZATION_ADMIN,
            'organization_id' => $organization->id
        ]);
    }
}
