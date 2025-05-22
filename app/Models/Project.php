<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'category_id',
    ];

    public function media()
    {
        return $this->belongsToMany(Media::class);
    }

    public function pages() {
        return $this->hasMany(Page::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

}
