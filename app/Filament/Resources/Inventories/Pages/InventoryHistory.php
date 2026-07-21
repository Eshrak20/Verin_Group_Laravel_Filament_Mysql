<?php

namespace App\Filament\Resources\Inventories\Pages;

use App\Filament\Resources\Inventories\InventoryResource;
use App\Models\InventoryTransaction;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\ViewAction;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;

class InventoryHistory extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;


    protected static string $resource = InventoryResource::class;


    protected string $view = 'filament.resources.inventories.pages.inventory-history';
    public ?int $inventoryId = null;

    public function mount()
    {
        $this->inventoryId = request()->get('inventory');
    }

    public function table(Table $table): Table
    {
        return $table

            ->query(

                InventoryTransaction::query()

                    ->when(
                        $this->inventoryId,

                        function ($query) {

                            $query->where(
                                'inventory_id',
                                $this->inventoryId
                            );
                        }

                    )

                    ->with([

                        'inventory.variant.product',

                        'inventory.branch',

                        'fromInventory.branch',

                        'toInventory.branch',

                        'user'

                    ])

                    ->latest()

            )


            ->columns([


                TextColumn::make('inventory.variant.product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),


                TextColumn::make('user.name')
                    ->label('Updated By')
                    ->default('System')
                    ->searchable(),


                BadgeColumn::make('type')
                    ->label('Transaction')
                    ->colors([

                        'success' => 'purchase',

                        'danger' => [
                            'sale',
                            'damage',
                            'transfer_out',
                        ],

                        'warning' => [
                            'adjustment',
                        ],

                        'info' => [
                            'transfer_in',
                            'refund',
                        ],

                    ]),



                TextColumn::make('before_stock')
                    ->label('Before')
                    ->numeric(),



                TextColumn::make('after_stock')
                    ->label('After')
                    ->numeric(),



                TextColumn::make('quantity')
                    ->label('Change')
                    ->formatStateUsing(function ($state, $record) {

                        if ($record->after_stock < $record->before_stock) {

                            return "" . $state;
                        }

                        return "+" . $state;
                    }),



                TextColumn::make('remarks')
                    ->label('Remarks')
                    ->limit(30),



                TextColumn::make('created_at')
                    ->label('Date')
                    ->date('d M Y'),



                TextColumn::make('created_at')
                    ->label('Time')
                    ->since(),


            ])


            ->filters([


                SelectFilter::make('type')

                    ->label('Transaction Type')

                    ->options([

                        'purchase' => 'Purchase',

                        'sale' => 'Sale',

                        'reserve' => 'Reserve',

                        'release' => 'Release',

                        'refund' => 'Refund',

                        'adjustment' => 'Adjustment',

                        'transfer_in' => 'Transfer In',

                        'transfer_out' => 'Transfer Out',

                        'damage' => 'Damage',

                    ]),



                Filter::make('created_at')

                    ->form([


                        DatePicker::make('from')
                            ->label('From Date'),


                        DatePicker::make('until')
                            ->label('Until Date'),


                    ])


                    ->query(function (
                        Builder $query,
                        array $data
                    ) {

                        return $query

                            ->when(
                                $data['from'] ?? null,
                                fn($q, $date) =>
                                $q->whereDate(
                                    'created_at',
                                    '>=',
                                    $date
                                )
                            )


                            ->when(
                                $data['until'] ?? null,
                                fn($q, $date) =>
                                $q->whereDate(
                                    'created_at',
                                    '<=',
                                    $date
                                )
                            );
                    })

                    ->indicateUsing(function (array $data) {

                        $indicators = [];


                        if ($data['from'] ?? false) {

                            $indicators[] =
                                'From ' . $data['from'];
                        }


                        if ($data['until'] ?? false) {

                            $indicators[] =
                                'Until ' . $data['until'];
                        }


                        return $indicators;
                    }),


            ])
            ->actions([
                ViewAction::make()
                    ->label('View')
                    ->modalHeading(
                        fn($record) =>
                        ucfirst(str_replace('_', ' ', $record->type))
                    )

                    ->infolist([


                        Section::make('Transaction Information')
                            ->icon('heroicon-o-arrows-right-left')
                            ->schema([

                                TextEntry::make('type')
                                    ->label('Transaction Type')
                                    ->badge(),


                                TextEntry::make('quantity')
                                    ->label('Quantity Changed')
                                    ->numeric(),


                                TextEntry::make('before_stock')
                                    ->label('Previous Stock'),


                                TextEntry::make('after_stock')
                                    ->label('New Stock'),

                            ])
                            ->columns(2),



                        Section::make('Product Information')
                            ->icon('heroicon-o-cube')
                            ->schema([


                                TextEntry::make('inventory.variant.product.name')
                                    ->label('Product'),


                                TextEntry::make('user.name')
                                    ->label('Updated By')
                                    ->default('System'),


                            ])
                            ->columns(2),



                        Section::make('Transfer Information')
                            ->icon('heroicon-o-building-storefront')
                            ->schema([


                                TextEntry::make('fromInventory.branch.name')
                                    ->label('From Branch'),


                                TextEntry::make('toInventory.branch.name')
                                    ->label('To Branch'),


                            ])
                            ->columns(2)
                            ->visible(
                                fn($record) =>
                                in_array($record->type, [
                                    'transfer_out',
                                    'transfer_in'
                                ])
                            ),



                        Section::make('Additional Information')
                            ->icon('heroicon-o-document-text')
                            ->schema([


                                TextEntry::make('remarks')
                                    ->label('Remarks')
                                    ->default('-'),


                                TextEntry::make('created_at')
                                    ->label('Created At')
                                    ->dateTime(),

                            ])
                            ->columns(2),
                    ])
            ])
            ->defaultSort(
                'created_at',
                'desc'
            );
    }
}
