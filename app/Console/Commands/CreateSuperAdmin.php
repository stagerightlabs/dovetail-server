<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use App\User;
use App\AccessLevel;
use App\Organization;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class CreateSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth:superadmin {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a super admin user account.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Fetch the email argument
        $email = $this->argument('email');

        // Validate the email address
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $this->error("Invalid email address");
            return 1;
        }

        // Fetch the admin organization
        $organization = Organization::firstOrCreate(['name' => 'Super Admins']);

        // Generate a password
        $password = Str::random(16);

        // Create the user
        $user = User::create([
            'name' => 'Super Admin',
            'email' => $email,
            'password' => Hash::make($password),
            'access_level' => AccessLevel::$SUPER_ADMIN,
            'organization_id' => $organization->id
        ]);

        // Send verification email
        event(new Registered($user));

        // All set
        $this->info("Super Admin Created: {$email}");
        $this->info("Password: {$password}");
    }
}
