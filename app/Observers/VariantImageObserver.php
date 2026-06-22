<?php

namespace App\Observers;

use App\Models\ProductVariantImage;
use App\Models\VariantImage;
use Illuminate\Support\Facades\Storage;

class VariantImageObserver
{
    /**
     * Handle the VariantImage "updating" event.
     */
    public function updating(ProductVariantImage $variantImage): void
    {
        // Check if the image field was changed during edit
        if ($variantImage->isDirty('image')) {
            $oldImage = $variantImage->getOriginal('image');

            if (!empty($oldImage)) {
                Storage::disk('cloudinary')->delete($oldImage);
            }
        }
    }

    /**
     * Handle the VariantImage "deleted" event.
     */
    public function deleted(ProductVariantImage $variantImage): void
    {
        // Triggers when a row is removed from the repeater card collection
        if (!empty($variantImage->image)) {
            Storage::disk('cloudinary')->delete($variantImage->image);
        }
    }
}