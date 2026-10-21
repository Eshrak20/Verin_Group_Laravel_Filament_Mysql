<?php

namespace App\Services;

use App\Enums\VerificationPurpose;
use App\Models\PendingUser;
use App\Models\User;
use App\Models\UserVerification;
use App\Notifications\OtpNotification;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class OtpService
{
    public function generate(): string
    {
        return (string) random_int(100000, 999999);
    }

    public function sendPendingEmailVerification(PendingUser $pendingUser, bool $resend = false): void
    {
        $otp = $this->refreshPendingUserOtp($pendingUser);

        $pendingUser->notify(new VerifyEmailNotification($otp, $resend));
    }

    public function sendUserOtp(User $user, VerificationPurpose $purpose, bool $resend = false): void
    {
        $otp = $this->createVerification($user, $purpose);

        $user->notify(new OtpNotification($otp, $purpose->value, $resend));
    }

    public function sendPasswordResetOtp(User $user): void
    {
        $otp = $this->createVerification($user, VerificationPurpose::PasswordReset);

        $user->notify(new ResetPasswordNotification($otp));
    }

    public function verifyPendingUser(string $email, string $otp): PendingUser
    {
        $pendingUser = PendingUser::query()
            ->where('email', $email)
            ->pending()
            ->latest()
            ->first();

        if (! $pendingUser) {
            throw ValidationException::withMessages([
                'otp' => ['The OTP is invalid or expired.'],
            ]);
        }

        if ($pendingUser->attempts >= 5) {
            $pendingUser->delete();

            throw ValidationException::withMessages([
                'otp' => ['Too many incorrect attempts. Please sign up again.'],
            ]);
        }

        if (! $pendingUser->matchesOtp($otp)) {
            $pendingUser->increment('attempts');

            throw ValidationException::withMessages([
                'otp' => ['The OTP is invalid or expired.'],
            ]);
        }

        return $pendingUser;
    }

    public function verifyUserOtp(string $email, VerificationPurpose $purpose, string $otp): UserVerification
    {
        $verification = UserVerification::query()
            ->where('email', $email)
            ->where('purpose', $purpose->value)
            ->pending()
            ->latest()
            ->first();

        if (! $verification) {
            throw ValidationException::withMessages([
                'otp' => ['The OTP is invalid or expired.'],
            ]);
        }

        if ($verification->attempts >= 5) {
            $verification->delete();

            throw ValidationException::withMessages([
                'otp' => ['Too many incorrect attempts. Please request a new OTP.'],
            ]);
        }
        if (! $verification->matches($otp)) {
            $verification->increment('attempts');

            throw ValidationException::withMessages([
                'otp' => ['The OTP is invalid or expired.'],
            ]);
        }

        if ($purpose === VerificationPurpose::EmailVerification) {
            User::where('email', $email)->firstOrFail()->markEmailAsVerified();
        }


        return $verification;
    }

    private function refreshPendingUserOtp(PendingUser $pendingUser): string
    {
        $otp = $this->generate();

        $pendingUser->forceFill([
            'otp' => Hash::make($otp),
            'attempts' => 0,
            'expired_at' => now()->addMinutes(10),
        ])->save();

        return $otp;
    }

    private function createVerification(User $user, VerificationPurpose $purpose): string
    {
        $otp = $this->generate();

        UserVerification::query()
            ->where('email', $user->email)
            ->where('purpose', $purpose->value)
            ->delete();

        UserVerification::query()->create([
            'user_id' => $user->id,
            'email' => $user->email,
            'otp' => Hash::make($otp),
            'purpose' => $purpose->value,
            'expired_at' => now()->addMinutes(10),
        ]);

        return $otp;
    }
}
