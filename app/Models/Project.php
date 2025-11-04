<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'content',
        'category_id',
        'category_order',
        'is_selected_work',
        'is_locked',
        'thumbnail_id',
    ];

    protected $casts = [
        'is_selected_work' => 'boolean',
        'is_locked' => 'boolean',
    ];

    public function media()
    {
        return $this->belongsToMany(Media::class);
    }

    public function thumbnail()
    {
        return $this->belongsTo(Media::class, 'thumbnail_id');
    }

    public function pages() {
        return $this->hasMany(Page::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

}
