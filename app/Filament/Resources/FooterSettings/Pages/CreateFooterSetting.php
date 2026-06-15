<?php

namespace App\Filament\Resources\FooterSettings\Pages;

use App\Filament\Resources\FooterSettings\FooterSettingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFooterSetting extends CreateRecord
{
    protected static string $resource = FooterSettingResource::class;

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}