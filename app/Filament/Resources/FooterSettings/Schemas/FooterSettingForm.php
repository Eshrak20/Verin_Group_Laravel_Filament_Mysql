<?php

namespace App\Filament\Resources\FooterSettings\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class FooterSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make() // Outer wrapper to manage layout
                    ->columns(3)
                    ->columnSpanFull() // // 2 columns for content, 1 for sidebar
                    ->schema([

                        // Left Column: Main Information
                        Section::make('General Information')
                            ->columnSpan(2)
                            ->schema([
                                TextInput::make('page_key')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->placeholder('e.g., home_page')
                                    ->extraAttributes(['style' => 'border-radius: 0px;']),

                                TextInput::make('company_name')
                                    ->required()
                                    ->extraAttributes(['style' => 'border-radius: 0px;']),
                                RichEditor::make('description')
                                    ->label('Description')
                                    ->columnSpanFull()
                                    ->extraInputAttributes([
                                        'style' => 'min-height: 200px;',
                                    ]),

                                TextInput::make('copyright_text')
                                    ->extraAttributes(['style' => 'border-radius: 0px;']),
                            ]),

                        // Right Column: Sidebar (Logo & Status)
                        Section::make('Configuration')
                            ->columnSpan(1)
                            ->schema([
                                FileUpload::make('logo')
                                    ->image()
                                    ->disk('public')
                                    ->directory('footers')
                                    ->imageEditor()
                                    ->extraAttributes(['style' => 'border-radius: 0px;']),

                                Toggle::make('is_active')
                                    ->label('Footer Status')
                                    ->helperText('Enable or disable this footer on the frontend.')
                                    ->default(true),

                                Toggle::make('show_social_links')
                                    ->label('Social Media Section')
                                    ->helperText('Toggle social media icons visibility.')
                                    ->default(true)
                                    ->live(),
                            ]),

                        // Bottom Section: Relational Tabs (Spans full 3 columns)
                        Tabs::make('Detailed Components')
                            ->columnSpanFull()
                            ->tabs([
                                'links' => Tabs\Tab::make('Navigation Links')
                                    ->icon('heroicon-o-link')
                                    ->schema([
                                        Repeater::make('links')
                                            ->relationship('links')
                                            ->columns(2)
                                            ->grid(2)
                                            ->schema([
                                                TextInput::make('title')->required()->extraAttributes(['style' => 'border-radius: 0px;']),
                                                TextInput::make('url')->url()->required()->extraAttributes(['style' => 'border-radius: 0px;']),
                                                TextInput::make('sort_order')->numeric()->default(0)->extraAttributes(['style' => 'border-radius: 0px;']),
                                                Toggle::make('open_new_tab')->inline(false),
                                            ])->itemLabel(fn($state) => $state['title'] ?? 'New Link'),
                                    ]),

                                'contact' => Tabs\Tab::make('Contact Details')
                                    ->icon('heroicon-o-phone')
                                    ->schema([
                                        Section::make()
                                            ->relationship('contactInfo')
                                            ->columns(2)
                                            ->schema([
                                                TextInput::make('email')->email()->extraAttributes(['style' => 'border-radius: 0px;']),
                                                TextInput::make('phone')->extraAttributes(['style' => 'border-radius: 0px;']),
                                                Textarea::make('address')->columnSpanFull()->extraAttributes(['style' => 'border-radius: 0px;']),
                                            ]),
                                    ]),

                                'social' => Tabs\Tab::make('Social Networks')
                                    ->icon('heroicon-o-share')
                                    ->hidden(fn($get) => !$get('show_social_links'))
                                    ->schema([
                                        Repeater::make('socialLinks')
                                            ->relationship('socialLinks')
                                            ->columns(3)
                                            ->schema([
                                                Select::make('platform')->options(['facebook' => 'FB', 'instagram' => 'IG', 'twitter' => 'X', 'linkedin' => 'LI'])->required()->extraAttributes(['style' => 'border-radius: 0px;']),
                                                TextInput::make('url')->url()->required()->extraAttributes(['style' => 'border-radius: 0px;']),
                                                TextInput::make('icon')->extraAttributes(['style' => 'border-radius: 0px;']),
                                            ]),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
