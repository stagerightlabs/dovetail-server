<?php

namespace App\Console\Commands;

use App\User;
use App\AccessLevel;
use App\Organization;
use Illuminate\Support\Str;
use App\Billing\PaymentGateway;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class CreateOrganization extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth:register';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an organization account';

    /**
     * @var PaymentGateway
     */
    protected $billing;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(PaymentGateway $billing)
    {
        parent::__construct();
        $this->billing = $billing;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // User Email Address
        $email = $this->ask('User email address?');
        if (DB::table('users')->where('email', $email)->exists()) {
            $this->error("Email '{$email}' is already in use.");
            return 1;
        }

        // User Name
        $name = $this->ask("User profile name?");

        // Password
        $password = Str::random(24);

        // Organization Name
        $organizationName = $this->ask("Organization name?");
        if (DB::table('organizations')->where('name', $organizationName)->exists()) {
            $this->error("Organization '{$organizationName}' already exists.");
            return 1;
        }

        // Confirmation
        $this->info("How does this look?\n\n");
        $this->info("User Email: {$email}");
        $this->info("User Name: {$name}");
        $this->info("Password: {$password}");
        $this->info("Organization: {$organizationName}\n\n");

        if ($this->confirm("Create this new account?")) {
            event(new Registered($user = $this->create([
                'email' => $email,
                'name' => $name,
                'password' => $password,
                'organization' => $organizationName,
            ])));
        }
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
