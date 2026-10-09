<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * A message sent through the landing-page contact form.
 */
#[Fillable(['name', 'email', 'subject', 'message', 'locale'])]
class Contact extends Model
{
    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }
}
