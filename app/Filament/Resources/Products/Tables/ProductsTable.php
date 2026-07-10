<?php

namespace App\Filament\Resources\Products\Tables;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\SkuGenerator;
use Filament\Tables\Table;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('Product')
                    ->searchable(
                        query: function (Builder $query, string $search): Builder {
                            return $query->where(function (Builder $query) use ($search) {

                                // Product Name
                                $query->where('name', 'like', "%{$search}%")

                                    // Product Slug
                                    ->orWhere('slug', 'like', "%{$search}%")

                                    // Variant SKU
                                    ->orWhereHas('variants', function (Builder $query) use ($search) {
                                        $query->where('sku', 'like', "%{$search}%");
                                    })

                                    // Attribute Names (Color, Size)
                                    ->orWhereHas('attributes', function (Builder $query) use ($search) {
                                        $query->where('name', 'like', "%{$search}%");
                                    })

                                    // Attribute Values (Red, XL, etc.)
                                    ->orWhereHas('variants.attributeValues', function (Builder $query) use ($search) {
                                        $query->where('value', 'like', "%{$search}%");
                                    })

                                    // Category
                                    ->orWhereHas('category', function (Builder $query) use ($search) {
                                        $query->where('name', 'like', "%{$search}%");
                                    })

                                    // Sub Category
                                    ->orWhereHas('subCategory', function (Builder $query) use ($search) {
                                        $query->where('name', 'like', "%{$search}%");
                                    })

                                    // Brand
                                    ->orWhereHas('brand', function (Builder $query) use ($search) {
                                        $query->where('name', 'like', "%{$search}%");
                                    });
                            });
                        }
                    )
                    ->sortable(),

                TextColumn::make('category.name')
                    ->label('Category'),

                TextColumn::make('subCategory.name')
                    ->label('Sub Category'),

                TextColumn::make('brand.name')
                    ->label('Brand'),

                IconColumn::make('is_featured')
                    ->boolean(),

                IconColumn::make('status')
                    ->boolean(),
            ])

            // This ensures the entire table filters update cleanly when values alter
            ->filters([
                // 1. Parent Category Filter
                SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                // 2. Dependent Sub-Category Filter
                SelectFilter::make('sub_category_id')
                    ->label('Sub Category')
                    ->relationship('subCategory', 'name', function (Builder $query) use ($table) {
                        $categoryData = $table->getFilter('category_id')?->getState();
                        $categoryId = $categoryData['value'] ?? null;

                        if ($categoryId) {
                            return $query->where('category_id', $categoryId);
                        }

                        return $query->whereNull('id');
                    })
                    ->searchable()
                    ->preload(),

                // 3. Dependent Brand Filter
                SelectFilter::make('brand_id')
                    ->label('Brand')
                    ->relationship('brand', 'name', function (Builder $query) use ($table) {
                        $subCategoryData = $table->getFilter('sub_category_id')?->getState();
                        $subCategoryId = $subCategoryData['value'] ?? null;

                        if ($subCategoryId) {
                            return $query->where('sub_category_id', $subCategoryId);
                        }

                        return $query->whereNull('id');
                    })
                    ->searchable()
                    ->preload(),
            ])


            ->recordActions([
                Action::make('clone')
                    ->label('Clone')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->action(function (Product $record) {

                        DB::transaction(function () use ($record) {

                            /*
            |--------------------------------------------------------------------------
            | Clone Product
            |--------------------------------------------------------------------------
            */

                            $product = $record->replicate();

                            $baseSlug = Str::slug($record->name) . '-copy';
                            $slug = $baseSlug;
                            $i = 1;

                            while (Product::where('slug', $slug)->exists()) {
                                $slug = "{$baseSlug}-{$i}";
                                $i++;
                            }

                            $product->slug = $slug;
                            $product->save();

                            /*
            |--------------------------------------------------------------------------
            | Clone Product Attributes
            |--------------------------------------------------------------------------
            */

                            $product->attributes()->sync(
                                $record->attributes()->pluck('attributes.id')->toArray()
                            );

                            /*
            |--------------------------------------------------------------------------
            | Clone Variants
            |--------------------------------------------------------------------------
            */

                            $record->load([
                                'variants.attributeValues',
                                'variants.images',
                                'variants.videos',
                            ]);

                            foreach ($record->variants as $variant) {

                                $newVariant = $variant->replicate();

                                $newVariant->product_id = $product->id;

                                /*
                |--------------------------------------------------------------------------
                | Generate unique SKU
                |--------------------------------------------------------------------------
                */

                                $attributeIds = $variant->attributeValues
                                    ->pluck('id')
                                    ->toArray();

                                $sku = SkuGenerator::generate(
                                    $product->category_id,
                                    $product->sub_category_id,
                                    $product->brand_id,
                                    $attributeIds,
                                );

                                $baseSku = $sku;
                                $count = 1;

                                while (ProductVariant::where('sku', $sku)->exists()) {
                                    $sku = "{$baseSku}-{$count}";
                                    $count++;
                                }

                                $newVariant->sku = $sku;

                                $newVariant->save();

                                /*
                |--------------------------------------------------------------------------
                | Clone Variant Attribute Values
                |--------------------------------------------------------------------------
                */

                                $newVariant->attributeValues()->sync($attributeIds);

                                /*
                |--------------------------------------------------------------------------
                | Clone Images
                |--------------------------------------------------------------------------
                */

                                foreach ($variant->images as $image) {

                                    $newImage = $image->replicate();

                                    $newImage->product_variant_id = $newVariant->id;

                                    $newImage->save();
                                }

                                /*
                |--------------------------------------------------------------------------
                | Clone Videos
                |--------------------------------------------------------------------------
                */

                                foreach ($variant->videos as $video) {

                                    $newVideo = $video->replicate();

                                    $newVideo->product_variant_id = $newVariant->id;

                                    $newVideo->save();
                                }
                            }

                            Notification::make()
                                ->title('Product cloned successfully.')
                                ->success()
                                ->send();
                        });
                    }),

                EditAction::make(),
            ]);
    }
}
