<?php

namespace App\Actions\Fortify;

trait ProfileValidationRules
{
    /**
     * Phone / country / city / area: required at registration, optional on the
     * profile (accounts created through social login may not have them).
     *
     * @return array<string, array<int, string>>
     */
    protected function contactRules(bool $required): array
    {
        $presence = $required ? 'required' : 'nullable';

        return [
            'phone' => [$presence, 'string', 'regex:/^\+?[0-9\s\-().]{6,30}$/'],
            'country' => [$presence, 'string', 'max:100'],
            'city' => [$presence, 'string', 'max:100'],
            'area' => [$presence, 'string', 'max:100'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function contactAttributes(): array
    {
        return [
            'phone' => __('Phone number'),
            'country' => __('Country'),
            'city' => __('City'),
            'area' => __('Area'),
        ];
    }
}
