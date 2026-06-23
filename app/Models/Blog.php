<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'title_bng',
        'slug',
        'content',
        'summary',
        'excerpt',
        'content_bng',
        'summary_bng',
        'featured_image',
        'category_id',
        'author_id',
        'status',
        'meta_title',
        'meta_description',
        'views',
        'is_featured',
        'reading_time',
        'published_at',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public static array $categories = [
        1 => 'Tech',
        2 => 'Business',
        3 => 'Lifestyle',
        4 => 'Education',
    ];
}
