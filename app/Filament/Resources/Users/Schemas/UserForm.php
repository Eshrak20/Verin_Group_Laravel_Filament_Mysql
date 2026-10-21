<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('User Information')
                    ->description('Manage the user’s basic account information.')
                    ->icon('heroicon-o-user-circle')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Full Name')
                            ->placeholder('Enter full name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Email Address')
                            ->placeholder('user@example.com')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->autocomplete('new-password')
                            ->dehydrated(fn ($state) => filled($state))
                            ->helperText('Leave blank to keep the current password.')
                            ->maxLength(255),
                    ]),

                Section::make('Email Verification')
                    ->description('Manage the user’s email verification status.')
                    ->icon('heroicon-o-envelope')
                    ->schema([
                        DateTimePicker::make('email_verified_at')
                            ->label('Email Verified At')
                            ->placeholder('Select verification date and time')
                            ->native(false)
                            ->seconds(false)
                            ->displayFormat('d M Y, h:i A')
                            ->closeOnDateSelection(false)
                            ->nullable()
                            ->helperText('Leave empty if the email address is not verified.'),
                    ]),

                Section::make('Access & Permissions')
                    ->description('Control the user’s access to the admin panel and platform.')
                    ->icon('heroicon-o-shield-check')
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_filament_user')
                            ->label('Admin Panel Access')
                            ->helperText('Allow this user to access the Filament admin panel.')
                            ->default(false)
                            ->required(),

                        Toggle::make('is_blocked')
                            ->label('Blocked Account')
                            ->helperText('Prevent this user from accessing restricted features.')
                            ->default(false)
                            ->required(),
                    ]),
            ]);
    }
}