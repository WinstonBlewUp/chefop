<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = ['file_path', 'type', 'alt', 'folder_id', 'is_external', 'external_url'];

    protected $casts = [
        'is_external' => 'boolean',
    ];

    public function getUrlAttribute(): string
    {
        if ($this->is_external) {
            return $this->external_url;
        }
        return asset('storage/' . $this->file_path);
    }

    public function isImage(): bool
    {
        return str_starts_with($this->type, 'image');
    }

    public function isVideo(): bool
    {
        return str_starts_with($this->type, 'video') || $this->is_external;
    }

    public function isExternalVideo(): bool
    {
        return $this->is_external;
    }

    /**
     * Extrait l'ID de la vidéo YouTube depuis une URL
     */
    public function getYoutubeId(): ?string
    {
        if (!$this->is_external || !$this->external_url) {
            return null;
        }

        $url = $this->external_url;

        // Format: youtube.com/watch?v=ID
        if (preg_match('/youtube\.com\/watch\?v=([^&]+)/', $url, $matches)) {
            return $matches[1];
        }

        // Format: youtu.be/ID
        if (preg_match('/youtu\.be\/([^?]+)/', $url, $matches)) {
            return $matches[1];
        }

        // Format: youtube.com/embed/ID
        if (preg_match('/youtube\.com\/embed\/([^?]+)/', $url, $matches)) {
            return $matches[1];
        }

        // Format: youtube.com/shorts/ID
        if (preg_match('/youtube\.com\/shorts\/([^?]+)/', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Extrait l'ID de la vidéo Vimeo depuis une URL
     */
    public function getVimeoId(): ?string
    {
        if (!$this->is_external || !$this->external_url) {
            return null;
        }

        $url = $this->external_url;

        // Format: vimeo.com/ID
        if (preg_match('/vimeo\.com\/(\d+)/', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Retourne l'URL embed pour la vidéo externe
     */
    public function getEmbedUrl(): ?string
    {
        if (!$this->is_external) {
            return null;
        }

        // YouTube
        if ($youtubeId = $this->getYoutubeId()) {
            return "https://www.youtube.com/embed/{$youtubeId}";
        }

        // Vimeo
        if ($vimeoId = $this->getVimeoId()) {
            return "https://player.vimeo.com/video/{$vimeoId}";
        }

        // Si c'est déjà une URL embed, la retourner telle quelle
        return $this->external_url;
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'media_project');
    }

    // Relation: dossier contenant ce média
    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

}
