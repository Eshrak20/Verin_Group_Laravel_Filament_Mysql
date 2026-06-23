<?php

namespace App\Filament\Resources\ClientReviews;

use App\Filament\Resources\ClientReviews\Pages\CreateClientReview;
use App\Filament\Resources\ClientReviews\Pages\EditClientReview;
use App\Filament\Resources\ClientReviews\Pages\ListClientReviews;
use App\Filament\Resources\ClientReviews\Schemas\ClientReviewForm;
use App\Filament\Resources\ClientReviews\Tables\ClientReviewsTable;
use App\Models\ClientReview;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ClientReviewResource extends Resource
{
    protected static ?string $model = ClientReview::class;

    protected static ?int $navigationSort = 10;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;
    protected static string|\UnitEnum|null $navigationGroup = 'Site Management';

    public static function form(Schema $schema): Schema
    {
        return ClientReviewForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClientReviewsTable::configure($table);
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
            'index' => ListClientReviews::route('/'),
            'create' => CreateClientReview::route('/create'),
            'edit' => EditClientReview::route('/{record}/edit'),
        ];
    }
}
