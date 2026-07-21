<?php

namespace App\Filament\Resources\Inventories\Pages;

use App\Filament\Resources\Inventories\InventoryResource;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\DB;
use App\Models\Branch;
use App\Models\Inventory;

class ManageInventory extends Page
{
    use InteractsWithRecord;

    protected static string $resource = InventoryResource::class;

    protected string $view = 'filament.resources.inventories.pages.manage-inventory';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }
    protected function getHeaderActions(): array
    {
        return [

            Action::make('addStock')
                ->label('Add Stock')
                ->icon('heroicon-o-plus-circle')
                ->color('success')
                ->slideOver()

                ->form([

                    TextInput::make('quantity')
                        ->label('Quantity')
                        ->numeric()
                        ->minValue(1)
                        ->required()

                        ->helperText(
                            fn() =>
                            "Available stock: {$this->record->stock}"
                        )

                        ->rules([

                            fn() => function ($attribute, $value, $fail) {

                                if ($value > $this->record->stock) {

                                    $fail(
                                        "Only {$this->record->stock} items are available."
                                    );
                                }
                            },

                        ]),

                    Select::make('type')
                        ->label('Reason')
                        ->options([

                            'purchase' => 'Purchase Received',

                            'refund' => 'Customer Return',

                            'adjustment' => 'Manual Adjustment',

                        ])
                        ->default('purchase')
                        ->required(),

                    Textarea::make('remarks'),

                ])

                ->action(function (array $data) {

                    DB::transaction(function () use ($data) {

                        $before = $this->record->stock;

                        $this->record->increment('stock', $data['quantity']);

                        $this->record->refresh();

                        InventoryTransaction::create([
                            'inventory_id' => $this->record->id,
                            'user_id' => auth()->id(),
                            'type' => $data['type'],
                            'quantity' => $data['quantity'],
                            'before_stock' => $before,
                            'after_stock' => $this->record->stock,
                            'remarks' => $data['remarks'],
                        ]);
                    });
                }),
            Action::make('removeStock')

                ->label('Remove Stock')

                ->icon('heroicon-o-minus-circle')

                ->color('danger')

                ->slideOver()


                ->form([


                    TextInput::make('quantity')
                        ->label('Quantity')
                        ->numeric()
                        ->minValue(1)
                        ->required()

                        ->helperText(
                            fn() =>
                            "Available stock: {$this->record->stock}"
                        )

                        ->rules([

                            fn() => function ($attribute, $value, $fail) {

                                if ($value > $this->record->stock) {

                                    $fail(
                                        "Only {$this->record->stock} items are available."
                                    );
                                }
                            },

                        ]),


                    Select::make('type')

                        ->label('Reason')

                        ->options([

                            'sale' => 'Sale',

                            'damage' => 'Damage',

                        ])

                        ->default('sale')

                        ->required(),


                    Textarea::make('remarks'),


                ])


                ->action(function (array $data) {


                    DB::transaction(function () use ($data) {


                        $before = $this->record->stock;


                        if ($before < $data['quantity']) {

                            throw new \Exception('Not enough stock');
                        }


                        $this->record->decrement(
                            'stock',
                            $data['quantity']
                        );


                        $this->record->refresh();


                        InventoryTransaction::create([

                            'inventory_id' => $this->record->id,

                            'user_id' => auth()->id(),

                            'type' => $data['type'],

                            'quantity' => $data['quantity'],

                            'before_stock' => $before,

                            'after_stock' => $this->record->stock,

                            'remarks' => $data['remarks'],

                        ]);
                    });
                }),
            Action::make('adjustStock')
                ->label('Adjust Stock')
                ->icon('heroicon-o-scale')
                ->color('warning')
                ->slideOver()

                ->form([

                    TextInput::make('actual_stock')
                        ->numeric()
                        ->required(),

                    Textarea::make('remarks'),

                ])

                ->action(function (array $data) {

                    DB::transaction(function () use ($data) {

                        $before = $this->record->stock;

                        $this->record->update([
                            'stock' => $data['actual_stock'],
                        ]);

                        $difference = $data['actual_stock'] - $before;

                        InventoryTransaction::create([
                            'inventory_id' => $this->record->id,
                            'user_id' => auth()->id(),
                            'type' => 'adjustment',
                            'quantity' => abs($difference),
                            'before_stock' => $before,
                            'after_stock' => $data['actual_stock'],
                            'remarks' => $data['remarks'],
                        ]);
                    });
                }),



            Action::make('history')
                ->label('History')
                ->icon('heroicon-o-clock')
                ->color('warning')
                ->url(fn() => InventoryResource::getUrl('history', [
                    'inventory' => $this->record->id,
                ])),



            Action::make('transfer')
                ->label('Transfer')
                ->icon('heroicon-o-arrow-right-circle')
                ->color('info')
                ->slideOver()

                ->form([

                    Select::make('destination_branch_id')
                        ->label('Transfer To Branch')
                        ->options(function () {

                            return Branch::query()

                                ->where('id', '!=', $this->record->branch_id)

                                ->where('status', true)

                                ->pluck('name', 'id');
                        })
                        ->searchable()
                        ->required(),


                    TextInput::make('quantity')
                        ->label('Quantity')
                        ->numeric()
                        ->minValue(1)
                        ->required()

                        ->helperText(
                            fn() =>
                            "Available stock: {$this->record->stock}"
                        )

                        ->rules([

                            fn() => function ($attribute, $value, $fail) {

                                if ($value > $this->record->stock) {

                                    $fail(
                                        "Only {$this->record->stock} items are available."
                                    );
                                }
                            },

                        ]),

                    Textarea::make('remarks')
                        ->label('Remarks'),

                ])

                ->action(function (array $data) {

                    DB::transaction(function () use ($data) {

                        $quantity = $data['quantity'];


                        // Source branch
                        $sourceBefore = $this->record->stock;

                        $this->record->decrement(
                            'stock',
                            $quantity
                        );


                        $this->record->refresh();



                        InventoryTransaction::create([

                            'inventory_id' => $this->record->id,

                            'user_id' => auth()->id(),

                            'type' => 'transfer_out',

                            'quantity' => $quantity,

                            'before_stock' => $sourceBefore,

                            'after_stock' => $this->record->stock,

                            'remarks' => $data['remarks'],

                        ]);



                        // Destination branch

                        // Destination branch

                        $destination = Inventory::firstOrCreate([

                            'branch_id' => $data['destination_branch_id'],

                            'product_variant_id' => $this->record->product_variant_id,

                        ], [

                            'stock' => 0,

                        ]);


                        $destinationBefore = $destination->stock;


                        $destination->increment(
                            'stock',
                            $quantity
                        );


                        $destination->refresh();



                        InventoryTransaction::create([

                            'inventory_id' => $this->record->id,

                            'from_inventory_id' => $this->record->id,

                            'to_inventory_id' => $destination->id,

                            'user_id' => auth()->id(),

                            'type' => 'transfer_out',

                            'quantity' => $quantity,

                            'before_stock' => $sourceBefore,

                            'after_stock' => $this->record->stock,

                            'remarks' => $data['remarks'],

                        ]);
                    });
                }),
        ];
    }
}
