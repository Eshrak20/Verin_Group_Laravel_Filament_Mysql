<?php

namespace App\Filament\Resources\Pages\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use App\Models\FooterSetting;
use Illuminate\Support\Str;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // Company dropdown
                Select::make('footer_setting_id')
                    ->label('Company')
                    ->options(FooterSetting::pluck('company_name', 'id'))
                    ->searchable()
                    ->required()
                    ->live(),

                // Page Title
                TextInput::make('title')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Auto generate page_type from title
                        $set('page_type', Str::slug($state));
                    }),

                // Auto generated (hidden or readonly)
                TextInput::make('page_type')
                    ->required()
                    ->disabled()
                    ->dehydrated(), // still saved in DB

                // Short description (rich text)
                RichEditor::make('short_description')
                    ->columnSpanFull()
                    ->extraInputAttributes([
                        'style' => 'min-height: 120px;',
                    ]),

                // Main content (rich text)
                RichEditor::make('content')
                    ->columnSpanFull()
                    ->extraInputAttributes([
                        'style' => 'min-height: 300px;',
                    ]),

                // Published toggle
                Toggle::make('is_published')
                    ->default(true)
                    ->required(),

                // Publish date
                DateTimePicker::make('published_at'),
            ]);
    }
}
