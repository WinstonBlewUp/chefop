<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuLink extends Model
{
    protected $fillable = ['page_id', 'category_id', 'order'];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Helper pour obtenir le titre et le slug selon le type
    public function getTitleAttribute()
    {
        if ($this->page_id) {
            return $this->page->title;
        } elseif ($this->category_id) {
            return $this->category->name;
        }
        return null;
    }

    public function getSlugAttribute()
    {
        if ($this->page_id) {
            return $this->page->slug;
        } elseif ($this->category_id) {
            // Pour les catégories, on utilise directement le nom de la catégorie comme slug
            return $this->category->name;
        }
        return null;
    }

    public function getTypeAttribute()
    {
        return $this->page_id ? 'page' : 'category';
    }
}
