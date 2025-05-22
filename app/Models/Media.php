<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = ['file_path', 'type', 'alt'];

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    public function isImage(): bool
    {
        return str_starts_with($this->type, 'image');
    }

    public function isVideo(): bool
    {
        return str_starts_with($this->type, 'video');
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'media_project');
    }

}
