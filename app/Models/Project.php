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
        //Miniature       
        'thumbnail_media_id',
        'thumbnail_path',
    ];

    protected $casts = [
        'is_selected_work' => 'boolean',
        'is_locked' => 'boolean',
    ];

    public function media()
    {
        return $this->belongsToMany(Media::class);
    }

    public function pages()
    {
        return $this->hasMany(Page::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // ▼ Miniature: relation vers Media
    public function thumbnailMedia()
    {
        return $this->belongsTo(Media::class, 'thumbnail_media_id');
    }

    // ▼ Miniature: URL d’affichage (priorité: Media > fichier uploadé)
    public function getThumbnailUrlAttribute(): ?string
    {
        if ($this->thumbnailMedia && $this->thumbnailMedia->path) {
            return asset('storage/' . ltrim($this->thumbnailMedia->path, '/'));
        }

        if ($this->thumbnail_path) {
            return asset('storage/' . ltrim($this->thumbnail_path, '/'));
        }

        return null;
    }
}