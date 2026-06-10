<?php

namespace App\Mail;

use App\Models\ApplicantComment;
use App\Models\JobApplicant;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Sent to a user who was @mentioned in an applicant's Activity Chat.
 */
class MentionMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $recipient,
        public User $author,
        public ApplicantComment $comment,
        public JobApplicant $applicant,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "{$this->author->name} mentioned you on {$this->applicant->name}");
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.mention',
            with: [
                'recipientName' => $this->recipient->name,
                'authorName' => $this->author->name,
                'applicantName' => $this->applicant->name,
                'body' => $this->comment->body,
                'url' => route('company.applicants.show', $this->applicant),
            ],
        );
    }
}
