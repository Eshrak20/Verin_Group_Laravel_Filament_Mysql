<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FooterContactInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'footer_setting_id',
        'phone',
        'email',
        'address',
    ];

    public function footerSetting()
    {
        return $this->belongsTo(FooterSetting::class);
    }
}
