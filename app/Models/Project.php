<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
    ];

    public function media()
    {
        return $this->belongsToMany(Media::class);
    }
}
