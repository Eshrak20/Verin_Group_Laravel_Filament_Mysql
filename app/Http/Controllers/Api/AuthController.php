<?php

namespace App\Http\Controllers\Api;

use App\Enums\VerificationPurpose;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\ChangePasswordRequest;
use App\Http\Requests\Api\V1\Auth\ForgotPasswordRequest;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Requests\Api\V1\Auth\ResendOtpRequest;
use App\Http\Requests\Api\V1\Auth\ResetPasswordRequest;
use App\Http\Requests\Api\V1\Auth\SendOtpRequest;
use App\Http\Requests\Api\V1\Auth\SignupRequest;
use App\Http\Requests\Api\V1\Auth\VerifyEmailRequest;
use App\Http\Requests\Api\V1\Auth\VerifyOtpRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}

    public function signup(SignupRequest $request): JsonResponse
    {
        return $this->response(
            true,
            'Registration pending. An OTP has been sent to your email.',
            201,
            $this->authService->signup($request->safe()->only(['name', 'email', 'password'])),
        );
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login(
            $request->string('email')->toString(),
            $request->string('password')->toString(),
        );

        return $this->response($result['success'], $result['message'], $result['code'], $result['data']);
    }

    public function myprofile(Request $request): JsonResponse
    {
        return $this->response(
            true,
            'Authenticated user fetched successfully.',
            200,
            $this->authService->user($request->user()),
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return $this->response(true, 'Logout successful.');
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $this->authService->forgotPassword($request->string('email')->toString());

        return $this->response(true, 'If the email exists, a password reset OTP has been sent.');
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $this->authService->resetPassword(
            $request->string('email')->toString(),
            $request->string('otp')->toString(),
            $request->string('password')->toString(),
        );

        return $this->response(true, 'Password reset successful.');
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $this->authService->changePassword(
            $request->user(),
            $request->string('current_password')->toString(),
            $request->string('password')->toString(),
        );

        return $this->response(true, 'Password changed successfully.');
    }

    public function sendOtp(SendOtpRequest $request): JsonResponse
    {
        $data = $this->authService->sendOtp(
            $request->string('email')->toString(),
            $this->purposeFromRequest($request),
        );

        return $this->response(true, 'OTP sent successfully.', 200, $data);
    }

    public function resendOtp(ResendOtpRequest $request): JsonResponse
    {
        $data = $this->authService->sendOtp(
            $request->string('email')->toString(),
            $this->purposeFromRequest($request),
            true,
        );

        return $this->response(true, 'OTP resent successfully.', 200, $data);
    }

    public function verifyEmail(VerifyEmailRequest $request): JsonResponse
    {
        return $this->response(
            true,
            'Registration successful.',
            201,
            $this->authService->verifyEmail(
                $request->string('email')->toString(),
                $request->string('otp')->toString(),
            ),
        );
    }

    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $purpose = $this->purposeFromRequest($request);
        $data = $this->authService->verifyOtp(
            $request->string('email')->toString(),
            $purpose,
            $request->string('otp')->toString(),
        );

        return $this->response(
            true,
            $purpose === VerificationPurpose::EmailVerification ? 'Registration successful.' : 'OTP verified successfully.',
            $purpose === VerificationPurpose::EmailVerification ? 201 : 200,
            $data,
        );
    }

    private function purposeFromRequest(Request $request): VerificationPurpose
    {
        return VerificationPurpose::tryFrom($request->string('purpose')->toString())
            ?? VerificationPurpose::EmailVerification;
    }
 
}
