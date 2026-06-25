<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use App\Models\SubCategory;
use App\Models\Brand;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\FileUpload;
use App\Models\AttributeValue;
use App\Services\SkuGenerator;
use App\Services\SlugService;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
      return $schema->components([

    Section::make('Product Details')
        ->columnSpanFull()
        ->schema([

            Grid::make(2)
                ->schema([

                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Set $set, ?string $state, string $context) {
                            SlugService::generate($set, $state, $context);
                        }),

                    TextInput::make('slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),

                ]),

            Grid::make(3)
                ->schema([

                    Select::make('category_id')
                        ->relationship('category', 'name')
                        ->searchable()
                        ->preload()
                        ->live()
                        ->required()
                        ->afterStateUpdated(fn(Get $get, Set $set) => self::updateAllVariantSkus($get, $set)),

                    Select::make('sub_category_id')
                        ->label('Sub Category')
                        ->options(
                            fn(Get $get) =>
                            SubCategory::query()
                                ->where('category_id', $get('category_id'))
                                ->pluck('name', 'id')
                        )
                        ->searchable()
                        ->live()
                        ->required()
                        ->afterStateUpdated(fn(Get $get, Set $set) => self::updateAllVariantSkus($get, $set)),

                    Select::make('brand_id')
                        ->label('Brand')
                        ->options(
                            fn(Get $get) =>
                            Brand::query()
                                ->where('sub_category_id', $get('sub_category_id'))
                                ->pluck('name', 'id')
                        )
                        ->searchable()
                        ->live()
                        ->afterStateUpdated(fn(Get $get, Set $set) => self::updateAllVariantSkus($get, $set)),

                ]),

            Select::make('attributes')
                ->label('Product Options (Color, Size etc.)')
                ->multiple()
                ->relationship('attributes', 'name')
                ->preload()
                ->searchable(),

            TextInput::make('thumbnail')
                ->label('Product Thumbnail (Cloudinary URL)')
                ->maxLength(255),

            Grid::make(2)
                ->schema([
                    Toggle::make('is_featured')
                        ->label('Featured')
                        ->default(false),

                    Toggle::make('status')
                        ->label('Active')
                        ->default(true),
                ]),

            Textarea::make('short_description')
                ->rows(3)
                ->columnSpanFull(),

            Textarea::make('description')
                ->rows(6)
                ->columnSpanFull(),
        ]),

    Section::make('Product Variants')
        ->columnSpanFull()
        ->schema([

            Repeater::make('variants')
                ->relationship('variants')
                ->columnSpanFull()
                ->grid(1)
                ->collapsed()
                ->schema([

                    Grid::make(3)
                        ->schema([

                            TextInput::make('sku')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('Auto-generated SKU')
                                ->formatStateUsing(function (Get $get, ?string $state) {
                                    if ($state) {
                                        return $state;
                                    }

                                    return SkuGenerator::generate(
                                        $get('../../category_id'),
                                        $get('../../sub_category_id'),
                                        $get('../../brand_id'),
                                        $get('attribute_values') ?? []
                                    );
                                }),

                            TextInput::make('price')
                                ->numeric()
                                ->required(),

                            TextInput::make('sale_price')
                                ->numeric(),

                        ]),

                    Select::make('attribute_values')
                        ->label('Options (Color / Size etc.)')
                        ->multiple()
                        ->searchable()
                        ->options(AttributeValue::all()->pluck('value', 'id'))
                        ->relationship('attributeValues', 'value')
                        ->live()
                        ->afterStateUpdated(function (Get $get, Set $set) {

                            $sku = SkuGenerator::generate(
                                $get('../../category_id'),
                                $get('../../sub_category_id'),
                                $get('../../brand_id'),
                                $get('attribute_values') ?? []
                            );

                            $set('sku', $sku);
                        }),

                    Section::make('Variant Media')
                        ->collapsible()
                        ->collapsed()
                        ->schema([

                            Repeater::make('images')
                                ->relationship('images')
                                ->label('Images')
                                ->columns(3)
                                ->schema([

                                    FileUpload::make('image')
                                        ->image()
                                        ->disk('cloudinary')
                                        ->required(),

                                ]),

                            Repeater::make('videos')
                                ->relationship('videos')
                                ->label('Videos')
                                ->columns(3)
                                ->schema([

                                    FileUpload::make('video_upload_action')
                                        ->label('Upload Video')
                                        ->disk('public')
                                        ->acceptedFileTypes([
                                            'video/mp4',
                                            'video/webm',
                                            'video/ogg',
                                            'video/quicktime',
                                        ])
                                        ->live()
                                        ->dehydrated(false)
                                        ->columnSpan(2)
                                        ->afterStateUpdated(function ($state, Set $set) {

                                            $file = is_array($state)
                                                ? reset($state)
                                                : $state;

                                            if (! $file instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                                                return;
                                            }

                                            $cloudinary = new \Cloudinary\Cloudinary([
                                                'cloud' => [
                                                    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                                                    'api_key' => env('CLOUDINARY_API_KEY'),
                                                    'api_secret' => env('CLOUDINARY_API_SECRET'),
                                                ],
                                            ]);

                                            $upload = $cloudinary
                                                ->uploadApi()
                                                ->upload(
                                                    $file->getRealPath(),
                                                    [
                                                        'resource_type' => 'video',
                                                        'folder' => 'product-videos',
                                                    ]
                                                );

                                            $set('video_url', $upload['secure_url']);
                                            $set('video_upload_action', null);
                                        }),

                                    TextInput::make('video_url')
                                        ->label('Video URL')
                                        ->url()
                                        ->required(),

                                ]),
                        ]),
                ]),
        ]),
]);
    }

    /**
     * Correctly pulls the existing variants array via $get and recalculates SKUs
     */
    protected static function updateAllVariantSkus(Get $get, Set $set): void
    {
        // 1. Get the current active structural IDs from the root form state
        $categoryId = $get('category_id');
        $subCategoryId = $get('sub_category_id');
        $brandId = $get('brand_id');

        // 2. Fetch all currently open variant items inside the repeater
        $variants = $get('variants') ?? [];

        // 3. Loop through and replace the SKUs dynamically
        foreach ($variants as $key => $variant) {
            $variants[$key]['sku'] = SkuGenerator::generate(
                $categoryId,
                $subCategoryId,
                $brandId,
                $variant['attribute_values'] ?? []
            );
        }

        // 4. Push the mutated data structure safely back to Livewire
        $set('variants', $variants);
    }
}
