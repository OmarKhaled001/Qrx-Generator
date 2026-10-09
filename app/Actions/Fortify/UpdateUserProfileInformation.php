<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    use ProfileValidationRules;

    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, string>  $input
     *
     * @throws ValidationException
     */
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            // Optional here: accounts created through social login may not have them.
            ...$this->contactRules(required: false),
        ], [], $this->contactAttributes())->validateWithBag('updateProfileInformation');

        $user->fill([
            'name' => $input['name'],
            'phone' => $input['phone'] ?? null,
            'country' => $input['country'] ?? null,
            'city' => $input['city'] ?? null,
            'area' => $input['area'] ?? null,
        ]);

        // Users must verify their email, so a changed address needs verifying again.
        if ($input['email'] !== $user->email) {
            $user->forceFill(['email' => $input['email'], 'email_verified_at' => null])->save();
            $user->sendEmailVerificationNotification();

            return;
        }

        $user->save();
    }
}
