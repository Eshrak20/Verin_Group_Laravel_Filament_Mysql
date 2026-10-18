<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewOrderNotification extends Mailable
{
    use Queueable, SerializesModels;

    public string $emailCss;

    public function __construct(
        public Order $order
    ) {
        $this->emailCss = file_get_contents(
            resource_path('views/emails/css/order-email.css')
        );
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: trim(
                view('emails.subjects.order-created', [
                    'order' => $this->order,
                ])->render()
            ),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.created',
            with: [
                'order' => $this->order,
                'emailCss' => $this->emailCss,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}