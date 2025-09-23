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
        'is_selected_work',   
        'project_type',
        'director',
        'productors',
        'production_company',
        'distributor',
        'award',
        'misc',    
    ];

    protected $casts = [
        'is_selected_work' => 'boolean',
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
