<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmailNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $otp,
        private readonly bool $resend = false,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subject = $this->resend ? 'Your new email verification OTP' : 'Verify your email address';

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.auth.verify-email', [
                'subject' => $subject,
                'intro' => $this->resend ? 'Here is your new OTP to verify your email address:' : 'Use this OTP to verify your email address:',
                'otp' => $this->otp,
                'expiresInMinutes' => 10,
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
