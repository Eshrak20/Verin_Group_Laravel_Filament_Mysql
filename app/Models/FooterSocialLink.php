<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FooterSocialLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'footer_setting_id',
        'platform',
        'url',
        'icon',
        'sort_order',
        'is_active',
    ];

    public function footerSetting()
    {
        return $this->belongsTo(FooterSetting::class);
    }
}
