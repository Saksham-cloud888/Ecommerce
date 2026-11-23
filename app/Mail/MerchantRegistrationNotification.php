<?php

namespace App\Mail;

use App\Models\Merchant;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class MerchantRegistrationNotification extends Mailable
{
    public function __construct(public Merchant $merchant)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New merchant registration: ' . $this->merchant->shop_name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.merchant-registration',
            with: [
                'merchant' => $this->merchant,
                'reviewUrl' => url('/admin/merchants'),
            ],
        );
    }
}
