<?php

namespace App\Mail;

use App\Models\Merchant;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class MerchantApproved extends Mailable
{
    public function __construct(
        public Merchant $merchant,
        public string $temporaryPassword,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your merchant account has been approved',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.merchant-approved',
            with: [
                'merchant' => $this->merchant,
                'temporaryPassword' => $this->temporaryPassword,
                'loginUrl' => url('/merchant/login'),
            ],
        );
    }
}
