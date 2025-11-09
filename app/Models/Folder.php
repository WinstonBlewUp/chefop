<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    protected $fillable = ['name', 'parent_id', 'order', 'color'];

    // Relation: dossier parent
    public function parent()
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }

    // Relation: sous-dossiers
    public function children()
    {
        return $this->hasMany(Folder::class, 'parent_id')->orderBy('order');
    }

    // Relation: médias dans ce dossier
    public function media()
    {
        return $this->hasMany(Media::class)->latest();
    }

    // Récupérer le chemin complet du dossier (breadcrumbs)
    public function getPathAttribute(): array
    {
        $path = [];
        $folder = $this;

        while ($folder) {
            array_unshift($path, [
                'id' => $folder->id,
                'name' => $folder->name
            ]);
            $folder = $folder->parent;
        }

        return $path;
    }
}
