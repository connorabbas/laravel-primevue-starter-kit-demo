<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use ReflectionClass;
use Spatie\Permission\Models\Role;

use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

/**
 * https://laravel.com/docs/master/prompts
 */
class RegisterUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:register';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register a new user interactively with role assignment';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('--- User Registration ---');
        $name = text('Enter the full name of the user');
        $email = text('Enter the email address of the user');
        $password = password('Enter the password');
        $passwordConfirmation = password('Confirm the password');

        // Validate the input data.
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $passwordConfirmation,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ]);
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return Command::FAILURE;
        }

        // Create the user.
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);
        $userRef = new ReflectionClass(User::class);
        if ($userRef->implementsInterface(MustVerifyEmail::class)) {
            $user->sendEmailVerificationNotification();
        }
        $this->info("User successfully created!");

        // Assign Role/s
        $availableRoles = Role::all()->pluck('name')->toArray();
        if (empty($availableRoles)) {
            $this->warn('No roles available to assign.');
        } else {
            $selectedRoles = multiselect(
                'Select roles to assign to the user',
                $availableRoles
            );
            if (!empty($selectedRoles)) {
                $user->assignRole($selectedRoles);
                $this->info("Roles assigned: " . implode(', ', $selectedRoles));
            }
        }

        return Command::SUCCESS;
    }
}
