<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Edit Order button
            Action::make('editOrder')
                ->label('Edit Order')
                ->icon('heroicon-o-pencil-square')
                ->color('warning')
                ->url(
                    fn (Order $record): string =>
                        OrderResource::getUrl('edit', [
                            'record' => $record,
                        ])
                ),

            // Add or update Steadfast tracking number
            Action::make('updateSteadfastTracking')
                ->label(
                    $this->record->steadfast_tracking_number
                        ? 'Update Tracking Number'
                        : 'Add Tracking Number'
                )
                ->icon('heroicon-o-truck')
                ->color('primary')
                ->modalHeading('Steadfast Tracking Number')
                ->modalDescription(
                    'Add or update the courier tracking number for this order.'
                )
                ->modalSubmitActionLabel('Save Tracking Number')
                ->fillForm(fn (Order $record): array => [
                    'steadfast_tracking_number' =>
                        $record->steadfast_tracking_number,
                ])
                ->form([
                    TextInput::make('steadfast_tracking_number')
                        ->label('Tracking Number')
                        ->placeholder('Enter Steadfast tracking number')
                        ->required()
                        ->maxLength(255)
                        ->autofocus(),
                ])
                ->action(function (
                    array $data,
                    Order $record
                ): void {
                    $record->update([
                        'steadfast_tracking_number' =>
                            trim($data['steadfast_tracking_number']),
                    ]);

                    Notification::make()
                        ->title('Tracking number saved')
                        ->body(
                            'The Steadfast tracking number has been updated.'
                        )
                        ->success()
                        ->send();
                }),
        ];
    }
}
