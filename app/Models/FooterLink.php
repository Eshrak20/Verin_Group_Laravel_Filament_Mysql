<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FooterLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'footer_setting_id',
        'title',
        'url',
        'open_new_tab',
        'sort_order',
    ];

    public function footerSetting()
    {
        return $this->belongsTo(FooterSetting::class);
    }
}