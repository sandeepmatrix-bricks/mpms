<?php

namespace App\Mail;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $password,
        public ?Tenant $tenant = null,
    ) {}

    public function envelope(): Envelope
    {
        $for = $this->tenant?->name ?? config('app.name');

        return new Envelope(subject: "Your login details for {$for}");
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.credentials',
            with: [
                'name' => $this->user->name,
                'email' => $this->user->email,
                'password' => $this->password,
                'tenant' => $this->tenant,
                // Company users sign in at the company portal; admins at the console.
                'loginUrl' => $this->tenant ? route('company.login') : route('admin.login'),
            ],
        );
    }
}
