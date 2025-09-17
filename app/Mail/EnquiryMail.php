<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EnquiryMail extends Mailable
{
    use Queueable, SerializesModels;

    /** @var array<string, mixed> */
    public array $details;

    /**
     * Create a new message instance.
     *
     * @param array<string, mixed> $details
     * @return void
     */
    public function __construct(array $details)
    {
        $this->details = $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('Email.enquiry', [
            'first_name' => $this->details['first_name'],
            'last_name' => $this->details['last_name'],
            'email' => $this->details['email'],
            'subject' => $this->details['subject'],
            'description' => $this->details['description'],
        ])
            ->from('ayushbm84@gmail.com', 'LaraBaseX')
            ->subject('LaraBaseX - Thank You for Your Enquiry');
    }
}
