<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'footer_setting_id',
        'page_type',
        'title',
        'short_description',
        'content',
        'is_published',
        'published_at',
    ];

    public function footerSetting()
    {
        return $this->belongsTo(FooterSetting::class);
    }
}
