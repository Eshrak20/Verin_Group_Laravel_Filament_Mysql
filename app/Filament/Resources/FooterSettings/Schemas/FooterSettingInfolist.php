<?php

namespace App\Filament\Resources\FooterSettings\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FooterSettingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Root Container stretching across the full screen viewport
                Section::make()
                    ->columns(3) // Split layout: 2 cols for main data, 1 col for metadata/logo sidebar
                    ->columnSpanFull() 
                    ->schema([
                        
                        // Left Column Block: Core Brand Content
                        Section::make('General Profile')
                            ->columnSpan(2)
                            ->schema([
                                Section::make()
                                    ->columns(2)
                                    ->schema([
                                        TextEntry::make('page_key')
                                            ->label('Target Frontend Page Key')
                                            ->inlineLabel(false)
                                            ->weight('bold')
                                            ->color('primary')
                                            ->icon('heroicon-m-document-text'),

                                        TextEntry::make('company_name')
                                            ->label('Registered Corporate Title')
                                            ->weight('semibold'),
                                    ]),

                                TextEntry::make('description')
                                    ->label('Footer Description Text')
                                    ->markdown()
                                    ->placeholder('No custom company description applied to this layout.'),

                                TextEntry::make('copyright_text')
                                    ->label('Copyright Legal Footer Line')
                                    ->color('gray')
                                    ->placeholder('—'),
                            ]),

                        // Right Column Block: Operational Status & Media Branding Assets
                        Section::make('Branding & Diagnostics')
                            ->columnSpan(1)
                            ->schema([
                                ImageEntry::make('logo')
                                    ->label('Configured Logo Asset')
                                    ->disk('public')
                                    ->square() // Crisp sharp edge look instead of circular bounds
                                    ->width('100%')
                                    ->height(120)
                                    ->placeholder('Default System Text fallback in use'),

                                Section::make()
                                    ->columns(2)
                                    ->schema([
                                        IconEntry::make('is_active')
                                            ->label('Live Status')
                                            ->boolean(),

                                        IconEntry::make('show_social_links')
                                            ->label('Social Blocks')
                                            ->boolean(),
                                    ]),
                            ]),

                        // Bottom Layout Row: Contextual Relational Feeds
                        Section::make('Deep Linked System Data Tables')
                            ->columnSpanFull()
                            ->columns(3)
                            ->schema([
                                
                                // Sub-Block 1: Real-time Communication Anchors (HasOne Relation)
                                Section::make('Contact Node')
                                    ->columnSpan(1)
                                    ->relationship('contactInfo')
                                    ->schema([
                                        TextEntry::make('email')
                                            ->icon('heroicon-m-envelope')
                                            ->copyable(),
                                        TextEntry::make('phone')
                                            ->icon('heroicon-m-phone')
                                            ->copyable(),
                                        TextEntry::make('address')
                                            ->icon('heroicon-m-map-pin'),
                                    ]),

                                // Sub-Block 2: Navigation Links Aggregation Array (HasMany Relation)
                                Section::make('Active Navigation Routers')
                                    ->columnSpan(2)
                                    ->schema([
                                        TextEntry::make('links.title')
                                            ->label('Active Links Menu Stack')
                                            ->listWithLineBreaks()
                                            ->bulleted()
                                            ->placeholder('No quick routing links assigned.'),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}