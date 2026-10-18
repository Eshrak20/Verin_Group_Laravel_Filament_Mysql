@extends('emails.layouts.auth', ['subject' => $subject])

@section('content')
    <h1 style="margin: 0 0 12px; font-size: 24px; line-height: 1.25; color: #111827;">{{ $subject }}</h1>
    <p style="margin: 0 0 24px; font-size: 15px; line-height: 1.7; color: #4b5563;">{{ $intro }}</p>

    <div style="margin: 0 0 24px; padding: 18px 24px; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 10px; text-align: center;">
        <div style="font-size: 12px; color: #047857; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">One-time password</div>
        <div style="margin-top: 8px; font-size: 34px; line-height: 1; font-weight: 800; color: #111827; letter-spacing: 8px;">{{ $otp }}</div>
    </div>

    <p style="margin: 0; font-size: 14px; line-height: 1.7; color: #6b7280;">This OTP will expire in {{ $expiresInMinutes }} minutes.</p>
@endsection