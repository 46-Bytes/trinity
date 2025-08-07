<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CreateClientUser extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-client
                            {firstname : The first name of the user}
                            {lastname : The last name of the user}
                            {email : The email address of the user}
                            {password : The password for the user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user with the client role';

    /**
     * Execute the console command.
     */
    public function handle() {
        $firstname = $this->argument('firstname');
        $lastname = $this->argument('lastname');
        $email = $this->argument('email');
        $password = $this->argument('password');

        $user = User::create([
            'first_name' => $firstname,
            'last_name' => $lastname,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $user->assignRole('client');

        $this->info("Client user created successfully with email: $email");
    }
}
