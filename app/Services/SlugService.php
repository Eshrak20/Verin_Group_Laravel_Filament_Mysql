<?php

namespace App\Services;

// Change this line to the namespace Intelephense found:
use Filament\Schemas\Components\Utilities\Set; 
use Illuminate\Support\Str;

class SlugService
{
    /**
     * Automatically generate a slug based on the field state during creation.
     */
    public static function generate(Set $set, ?string $state, string $context, string $targetField = 'slug'): void
    {
        if ($context === 'create') {
            $set($targetField, Str::slug($state));
        }
    }
}