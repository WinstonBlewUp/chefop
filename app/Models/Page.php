<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'project_id',
        'published',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
