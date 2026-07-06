<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class FooterSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_key',
        'logo',
        'company_name',
        'description',
        'copyright_text',
        'show_social_links',
        'is_active',
    ];

    public function socialLinks()
    {
        return $this->hasMany(FooterSocialLink::class);
    }

    public function links()
    {
        return $this->hasMany(FooterLink::class);
    }

    public function contactInfo()
    {
        return $this->hasOne(FooterContactInfo::class);
    }
    protected $appends = ['image_url'];

    public function getImageUrlAttribute(): ?string
    {
        return $this->logo
            ? Storage::disk('public')->url($this->logo)
            : null;
    }
    public function pages()
    {
        return $this->hasMany(Page::class);
    }
}
