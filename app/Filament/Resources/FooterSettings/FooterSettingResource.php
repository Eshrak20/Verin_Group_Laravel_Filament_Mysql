<?php

namespace App\Filament\Resources\FooterSettings;

use App\Filament\Resources\FooterSettings\Pages\CreateFooterSetting;
use App\Filament\Resources\FooterSettings\Pages\EditFooterSetting;
use App\Filament\Resources\FooterSettings\Pages\ListFooterSettings;
use App\Filament\Resources\FooterSettings\Pages\ViewFooterSetting;
use App\Filament\Resources\FooterSettings\Schemas\FooterSettingForm;
use App\Filament\Resources\FooterSettings\Schemas\FooterSettingInfolist;
use App\Filament\Resources\FooterSettings\Tables\FooterSettingsTable;
use App\Models\FooterSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FooterSettingResource extends Resource
{
    protected static ?string $model = FooterSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTableCells;

    protected static ?string $recordTitleAttribute = 'page_key';
    // 1. Force the form container to stretch full-width across the screen
    protected static bool $hasFullWidthFormActions = true;
    public static function getMaxContentWidth(): string
    {
        return 'full'; // This forces the panel layout to expand 100% wide
    }
    public static function form(Schema $schema): Schema
    {
        return FooterSettingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FooterSettingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FooterSettingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFooterSettings::route('/'),
            'create' => CreateFooterSetting::route('/create'),
            'view' => ViewFooterSetting::route('/{record}'),
            'edit' => EditFooterSetting::route('/{record}/edit'),

        ];
    }
}
