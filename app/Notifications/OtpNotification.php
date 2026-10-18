<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OtpNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $code,
        private readonly string $purpose,
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
        return (new MailMessage)
            ->subject($this->subject())
            ->view('emails.auth.otp', [
                'subject' => $this->subject(),
                'intro' => $this->intro(),
                'otp' => $this->code,
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

    private function subject(): string
    {
        if ($this->resend) {
            return match ($this->purpose) {
                'login' => 'Your new login OTP',
                default => 'Your new verification OTP',
            };
        }

        return match ($this->purpose) {
            'login' => 'Your login OTP',
            default => 'Your verification OTP',
        };
    }

    private function intro(): string
    {
        if ($this->resend) {
            return match ($this->purpose) {
                'login' => 'Here is your new OTP to continue logging in:',
                default => 'Here is your new OTP for '.str_replace('_', ' ', $this->purpose).':',
            };
        }

        return match ($this->purpose) {
            'login' => 'Use this OTP to continue logging in:',
            default => 'Use this OTP for '.str_replace('_', ' ', $this->purpose).':',
        };
    }
}
