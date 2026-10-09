<?php

namespace App\Livewire\Public;

use App\Models\Contact;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

/**
 * Landing-page contact form. Messages land in the admin panel (Contacts).
 */
class ContactForm extends Component
{
    public string $name = '';

    public string $email = '';

    public string $subject = '';

    public string $message = '';

    // Honeypot: hidden from people, filled in by bots.
    public string $website = '';

    public bool $sent = false;

    public function send(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'min:10', 'max:3000'],
        ], [], [
            'name' => __('Name'), 'email' => __('Email address'), 'subject' => __('Subject'), 'message' => __('Message'),
        ]);

        $key = 'contact:'.request()->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $this->addError('message', __('Too many messages. Please try again in a few minutes.'));

            return;
        }
        RateLimiter::hit($key, 600);

        if ($this->website === '') {
            Contact::query()->create([
                'name' => $this->name,
                'email' => $this->email,
                'subject' => $this->subject,
                'message' => $this->message,
                'locale' => app()->getLocale(),
            ]);
        }

        $this->reset('name', 'email', 'subject', 'message');
        $this->sent = true;
    }

    public function render(): View
    {
        return view('livewire.public.contact-form');
    }
}
