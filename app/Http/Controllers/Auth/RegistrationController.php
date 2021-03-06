<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\AccessLevel;
use App\Organization;
use App\Rules\ValidEmail;
use Illuminate\Http\Request;
use App\Billing\PaymentGateway;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;

class RegistrationController extends Controller
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
    public function store(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->createUser($request->all())));

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
            'email' => ['required', 'string', new ValidEmail, 'max:255', 'unique:users'],
            'password' => 'required|string|min:8|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function createUser(array $data)
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
