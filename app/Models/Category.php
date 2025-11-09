<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'is_special'];

    protected $casts = [
        'is_special' => 'boolean',
    ];

    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }

    public function pages()
    {
        return $this->hasMany(Page::class);
    }
}
