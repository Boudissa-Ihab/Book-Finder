<?php

namespace App\Console\Commands;

use App\Enums\Roles;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:admin {email} {name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new user with admin role';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $name = $this->argument('name');
            $email = $this->argument('email');

            // Check if the email is already used (since it's unique)
            if (User::where('email', $email)->exists()) {
                $this->error('This email is already been used.');
                return Command::FAILURE;
            }

            // If the name is not provided, we generate a default one
            if (! isset($name))
                $name = 'admin-' . (User::count() + 1);

            // We keep asking for valid password until the user confirms it
            $password = ''; $password_confirmation = '';
            while (true) {
                $password = $this->secret('Enter your password');
                $password_confirmation = $this->secret(question: 'Confirm your password');

                if ($password !== $password_confirmation) {
                    $this->error('Passwords do not match. Please try again');
                    continue;
                }

                break;
            }

            // Create the admin user & mark their email as verified
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => bcrypt($password),
            ]);
            $user->markEmailAsVerified();
            $user->save();
            $user->assignRole(Roles::ADMIN);

            $this->info('Admin created successfully');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            Log::error("Admin generation error: \n" . $e->getMessage());
            $this->error('Failed to create the admin. Please check the logs for more details');
            return Command::FAILURE;
        }
    }
}
