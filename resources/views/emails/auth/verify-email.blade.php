@extends('emails.layouts.auth', ['subject' => $subject])

@section('content')

    <h1 style="margin: 0 0 12px; font-size: 24px; line-height: 1.3; color: #0C0C0C;">
        {{ $subject }}
    </h1>

    <p style="margin: 0 0 24px; font-size: 15px; line-height: 1.7; color: #555555;">
        {{ $intro }}
    </p>

    <div style="margin: 0 0 24px; padding: 22px 24px; background-color: #F9F8F6; border: 1px solid #E8E6E2; text-align: center;">

        <div style="font-size: 12px; color: #0C0C0C; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">
            Your OTP
        </div>

        <div style="margin-top: 12px; font-size: 34px; line-height: 1.2; font-weight: 800; color: #0C0C0C; letter-spacing: 8px;">
            {{ $otp }}
        </div>

    </div>

    <p style="margin: 0; font-size: 14px; line-height: 1.7; color: #555555;">
        This OTP will expire in {{ $expiresInMinutes }} minutes.
    </p>

@endsection