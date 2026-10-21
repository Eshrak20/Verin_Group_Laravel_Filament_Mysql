<?php

namespace App\Services;

use App\Enums\VerificationPurpose;
use App\Models\PendingUser;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(private readonly OtpService $otpService) {}

    /**
     * @param  array{name: string, email: string, password: string}  $data
     * @return array<string, mixed>
     */
    public function signup(array $data): array
    {
        $pendingUser = DB::transaction(function () use ($data): PendingUser {
            return PendingUser::query()->updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => $data['password'],
                    'attempts' => 0,
                    'expired_at' => now()->addMinutes(10),
                ],
            );
        });

        $this->otpService->sendPendingEmailVerification($pendingUser);

        return [
            'email' => $pendingUser->email,
            'email_verification_required' => true,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function login(string $email, string $password): array
    {
        $user = User::query()->where('email', $email)->first();
        if ($user && $user->is_blocked) {
            throw ValidationException::withMessages([
                'email' => ['Your account has been blocked. Please contact support.'],
            ]);
        }

        if (! $user) {
            $pendingUser = PendingUser::query()->where('email', $email)->first();

            if ($pendingUser && Hash::check($password, $pendingUser->password)) {
                $this->otpService->sendPendingEmailVerification($pendingUser, true);

                return [
                    'success' => false,
                    'message' => 'Email verification required. A new OTP has been sent.',
                    'code' => 403,
                    'data' => [
                        'email' => $pendingUser->email,
                        'email_verification_required' => true,
                    ],
                ];
            }
        }

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return [
            'success' => true,
            'message' => 'Login successful.',
            'code' => 200,
            'data' => [
                'user' => $this->userPayload($user->loadMissing('profile')),
                'token' => $user->createToken('customer')->plainTextToken,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function verifyEmail(string $email, string $otp): array
    {
        $pendingUser = $this->otpService->verifyPendingUser($email, $otp);

        $user = DB::transaction(function () use ($pendingUser): User {
            $user = User::query()->create([
                'name' => $pendingUser->name,
                'email' => $pendingUser->email,
                'password' => $pendingUser->password,
                'email_verified_at' => now(),
            ]);

            $user->profile()->create();
            $pendingUser->delete();

            return $user->load('profile');
        });

        return [
            'verified' => true,
            'user' => $this->userPayload($user),
            'token' => $user->createToken('customer')->plainTextToken,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function user(User $user): array
    {
        return [
            'user' => $this->userPayload($user->loadMissing('profile')),
        ];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }

    public function forgotPassword(string $email): void
    {
        $user = User::query()->where('email', $email)->first();

        if ($user) {
            $this->otpService->sendPasswordResetOtp($user);
        }
    }

    public function resetPassword(string $email, string $otp, string $password): void
    {
        $verification = $this->otpService->verifyUserOtp($email, VerificationPurpose::PasswordReset, $otp);
        $user = User::query()->where('email', $verification->email)->firstOrFail();

        $user->forceFill([
            'password' => $password,
            'remember_token' => Str::random(60),
        ])->save();

        $user->tokens()->delete();
        $verification->delete();
    }

    public function changePassword(User $user, string $currentPassword, string $password): void
    {
        if (! Hash::check($currentPassword, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.'],
            ]);
        }

        $user->forceFill([
            'password' => $password,
        ])->save();

        $user->tokens()->whereKeyNot($user->currentAccessToken()?->getKey())->delete();
    }

    /**
     * @return array<string, mixed>|null
     */
    public function sendOtp(string $email, VerificationPurpose $purpose, bool $resend = false): ?array
    {
        if ($purpose === VerificationPurpose::EmailVerification) {
            $pendingUser = PendingUser::query()->where('email', $email)->first();

            if (! $pendingUser) {
                throw ValidationException::withMessages([
                    'email' => ['No pending registration was found for this email.'],
                ]);
            }

            $this->otpService->sendPendingEmailVerification($pendingUser, $resend);

            return [
                'email' => $pendingUser->email,
                'email_verification_required' => true,
            ];
        }

        $user = User::query()->where('email', $email)->firstOrFail();
        $this->otpService->sendUserOtp($user, $purpose, $resend);

        return null;
    }

    /**
     * @return array<string, mixed>
     */
    public function verifyOtp(string $email, VerificationPurpose $purpose, string $otp): array
    {
        if ($purpose === VerificationPurpose::EmailVerification) {
            return $this->verifyEmail($email, $otp);
        }

        $verification = $this->otpService->verifyUserOtp($email, $purpose, $otp);
        $user = User::query()->where('email', $verification->email)->firstOrFail();
        if ($user->is_blocked) {
            throw ValidationException::withMessages([
                'email' => ['Your account has been blocked. Please contact support.'],
            ]);
        }
        $verification->delete();

        $data = ['verified' => true];

        if ($purpose === VerificationPurpose::Login) {
            $data['user'] = $this->userPayload($user->loadMissing('profile'));
            $data['token'] = $user->createToken('customer')->plainTextToken;
        }

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    private function userPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at,
            'is_active' => $user->is_active,
            'profile' => $user->profile,
        ];
    }
}
