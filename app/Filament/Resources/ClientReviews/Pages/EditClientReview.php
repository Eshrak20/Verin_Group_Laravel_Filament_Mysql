<?php

namespace App\Filament\Resources\ClientReviews\Pages;

use App\Filament\Resources\ClientReviews\ClientReviewResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditClientReview extends EditRecord
{
    protected static string $resource = ClientReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
