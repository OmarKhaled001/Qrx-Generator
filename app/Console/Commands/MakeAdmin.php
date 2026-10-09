<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

#[Signature('app:make-admin {email} {--name= : Name for a new account} {--password= : Password for a new account}')]
#[Description('Create an administrator, or give an existing user admin access')]
class MakeAdmin extends Command
{
    public function handle(): int
    {
        $email = Str::lower((string) $this->argument('email'));
        $user = User::query()->where('email', $email)->first();

        if (! $user) {
            $user = new User([
                'name' => $this->option('name') ?: text(__('Name'), required: true),
                'email' => $email,
                'password' => Hash::make($this->option('password') ?: password(__('Password'), required: true, validate: fn (string $value) => strlen($value) < 8 ? __('At least 8 characters.') : null)),
            ]);
        }

        $user->forceFill(['is_admin' => true, 'email_verified_at' => $user->email_verified_at ?? now()])->save();

        $this->components->info("{$user->email} is now an administrator. Sign in at ".url('/admin'));

        return self::SUCCESS;
    }
}
