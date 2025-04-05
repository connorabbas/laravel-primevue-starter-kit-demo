<?php

namespace Tests\Feature\Commands;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use ReflectionClass;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RegisterUserCommandTest extends TestCase
{
    use RefreshDatabase;

    public function setup(): void
    {
        parent::setup();
        // remove all roles added from migration, to accurately test when no roles are present
        Role::query()->truncate();
    }

    public function test_command_creates_user_with_valid_input()
    {
        $this->artisan('user:register')
            ->expectsQuestion('Enter the full name of the user', 'John Doe')
            ->expectsQuestion('Enter the email address of the user', 'john@example.com')
            ->expectsQuestion('Enter the password', 'password123')
            ->expectsQuestion('Confirm the password', 'password123')
            ->expectsOutput('User successfully created!')
            ->assertExitCode(0);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $user = User::where('email', 'john@example.com')->first();
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function test_command_fails_with_invalid_email()
    {
        $this->artisan('user:register')
            ->expectsQuestion('Enter the full name of the user', 'John Doe')
            ->expectsQuestion('Enter the email address of the user', 'not-an-email')
            ->expectsQuestion('Enter the password', 'password123')
            ->expectsQuestion('Confirm the password', 'password123')
            ->expectsOutput('The email field must be a valid email address.')
            ->assertExitCode(1);

        $this->assertDatabaseMissing('users', [
            'name' => 'John Doe',
            'email' => 'not-an-email',
        ]);
    }

    public function test_command_fails_with_password_confirmation_mismatch()
    {
        $this->artisan('user:register')
            ->expectsQuestion('Enter the full name of the user', 'John Doe')
            ->expectsQuestion('Enter the email address of the user', 'john@example.com')
            ->expectsQuestion('Enter the password', 'password123')
            ->expectsQuestion('Confirm the password', 'differentpassword')
            ->expectsOutput('The password field confirmation does not match.')
            ->assertExitCode(1);

        $this->assertDatabaseMissing('users', [
            'email' => 'john@example.com',
        ]);
    }

    public function test_command_fails_when_required_fields_are_missing()
    {
        $this->artisan('user:register')
            ->expectsQuestion('Enter the full name of the user', '')
            ->expectsQuestion('Enter the email address of the user', '')
            ->expectsQuestion('Enter the password', '')
            ->expectsQuestion('Confirm the password', '')
            ->expectsOutput('The name field is required.')
            ->expectsOutput('The email field is required.')
            ->expectsOutput('The password field is required.')
            ->assertExitCode(1);
    }

    public function test_command_sends_verification_email_if_user_must_verify()
    {
        $userRef = new ReflectionClass(User::class);
        if (!$userRef->implementsInterface(MustVerifyEmail::class)) {
            $this->markTestSkipped();
        }
        Notification::fake();

        $this->artisan('user:register')
            ->expectsQuestion('Enter the full name of the user', 'Jane Doe')
            ->expectsQuestion('Enter the email address of the user', 'jane@example.com')
            ->expectsQuestion('Enter the password', 'password123')
            ->expectsQuestion('Confirm the password', 'password123')
            ->expectsOutput('User successfully created!')
            ->assertExitCode(0);

        $user = User::where('email', 'jane@example.com')->first();
        Notification::assertSentTo($user, VerifyEmail::class);
    }
}
