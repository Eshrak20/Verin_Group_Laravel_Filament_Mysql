<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Site extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'domain',
        'logo',
        'favicon',
        'is_active',
    ];

    public function pages()
    {
        return $this->hasMany(Page::class);
    }
}