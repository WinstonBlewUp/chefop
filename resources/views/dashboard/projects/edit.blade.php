@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h2 class="text-xl font-semibold mb-6">Modifier le projet</h2>

                <form action="{{ route('dashboard.projects.update', $project) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Titre --}}
                    <div class="mb-6">
                        <label class="block font-medium text-sm text-gray-700 mb-1">Titre</label>
                        <input type="text" name="title" value="{{ old('title', $project->title) }}"
                               class="w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>

                    {{-- Slug --}}
                    <div class="mb-6">
                        <label class="block font-medium text-sm text-gray-700 mb-1">Slug (optionnel)</label>
                        <input type="text" name="slug" value="{{ old('slug', $project->slug) }}"
                               class="w-full border-gray-300 rounded-md shadow-sm">
                    </div> 

                    {{-- Description (visible uniquement pour l'admin) --}}
                    @if(auth()->check() && auth()->user()->is_admin)
                    <div class="mb-6">
                        <label class="block font-medium text-sm text-gray-700 mb-1">
                            Description
                            <span class="inline-flex items-center ml-2 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                Visible uniquement par l'admin
                            </span>
                        </label>
                        <textarea name="description" rows="4"
                                  class="w-full border-gray-300 rounded-md shadow-sm">{{ old('description', $project->description) }}</textarea>
                    </div>
                    @endif  

                    {{-- Catégories --}}
                    <div class="mb-6">
                        <label class="block font-medium text-sm text-gray-700 mb-3">Catégories</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @php
                                $selectedCategories = $project->categories->pluck('id')->toArray();
                            @endphp
                            @foreach ($categories as $category)
                                <div class="flex items-center">
                                    <input type="checkbox"
                                           name="categories[]"
                                           value="{{ $category->id }}"
                                           id="category_{{ $category->id }}"
                                           {{ in_array($category->id, $selectedCategories) ? 'checked' : '' }}
                                           class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                                    <label for="category_{{ $category->id }}" class="ml-2 text-sm text-gray-700">
                                        {{ $category->name }}
                                        @if($category->is_special)
                                            <span class="text-orange-500">★</span>
                                        @endif
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Sélectionnez une ou plusieurs catégories pour ce projet</p>
                    </div>

                    {{-- Contenu --}}
                    <div class="mb-6">
                        <label class="block font-medium text-sm text-gray-700 mb-2">Contenu du projet</label>
                        <textarea id="content" name="content" class="w-full border-gray-300 rounded-lg shadow-sm">{{ old('content', $project->content) }}</textarea>
                    </div>

                    {{-- Galerie médias --}}
                    <div class="mb-8">
                        <label class="block font-medium text-sm text-gray-700 mb-4">Médias associés</label>

                        {{-- Section Dossiers --}}
                        @if($folders->count() > 0)
                            <div class="border border-gray-200 rounded-lg p-4 bg-blue-50 mb-4">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/>
                                        </svg>
                                        <h3 class="font-medium text-gray-900">Dossiers</h3>
                                    </div>
                                    <p class="text-sm text-gray-600">{{ $folders->count() }} dossier(s)</p>
                                </div>

                                {{-- Liste des dossiers pliables --}}
                                <div class="space-y-2">
                                    @foreach($folders as $folder)
                                        @if($folder->media->count() > 0)
                                            <div class="border border-gray-200 rounded-lg bg-white overflow-hidden">
                                                <div class="w-full px-4 py-3 flex items-center justify-between hover:bg-gray-50 transition-colors cursor-pointer"
                                                     onclick="toggleFolderMedias({{ $folder->id }})">
                                                    <div class="flex items-center">
                                                        <svg class="w-5 h-5 mr-3" fill="{{ $folder->color ?? '#3B82F6' }}" viewBox="0 0 24 24">
                                                            <path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/>
                                                        </svg>
                                                        <span class="font-medium text-gray-900">{{ $folder->name }}</span>
                                                        <span class="ml-2 text-xs text-gray-500">({{ $folder->media->count() }} médias)</span>
                                                    </div>
                                                    <svg class="w-5 h-5 text-gray-400 transform transition-transform" id="icon-folder-{{ $folder->id }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                </div>

                                                <div id="folder-{{ $folder->id }}" class="hidden px-4 pb-4">
                                                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 pt-3 border-t border-gray-100">
                                                        @foreach($folder->media as $item)
                                                            <label class="relative cursor-pointer group">
                                                                <input type="checkbox"
                                                                       name="media[]"
                                                                       value="{{ $item->id }}"
                                                                       class="peer hidden"
                                                                       {{ in_array($item->id, $attachedMedia) ? 'checked' : '' }}>

                                                                @if(Str::startsWith($item->type, 'image/'))
                                                                    <img src="{{ asset('storage/' . $item->file_path) }}"
                                                                         alt="media"
                                                                         class="w-full h-32 object-cover rounded-md border-2 border-gray-300 peer-checked:border-indigo-500 peer-checked:ring-2 peer-checked:ring-indigo-500 transition">
                                                                @elseif($item->is_external)
                                                                    <div class="w-full h-32 rounded-md border-2 border-gray-300 peer-checked:border-indigo-500 peer-checked:ring-2 peer-checked:ring-indigo-500 transition overflow-hidden">
                                                                        <iframe src="{{ $item->getEmbedUrl() }}"
                                                                                class="w-full h-full pointer-events-none"
                                                                                frameborder="0"></iframe>
                                                                    </div>
                                                                @elseif(Str::startsWith($item->type, 'video/'))
                                                                    <video class="w-full h-32 object-cover rounded-md border-2 border-gray-300 peer-checked:border-indigo-500 peer-checked:ring-2 peer-checked:ring-indigo-500 transition" muted>
                                                                        <source src="{{ asset('storage/' . $item->file_path) }}" type="{{ $item->type }}">
                                                                    </video>
                                                                @endif

                                                                <div class="absolute inset-0 rounded-md bg-indigo-500/20 opacity-0 peer-checked:opacity-100 transition pointer-events-none"></div>

                                                                <div class="absolute top-2 right-2 opacity-0 peer-checked:opacity-100 transition-opacity">
                                                                    <div class="p-1 rounded-full bg-indigo-500">
                                                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                                        </svg>
                                                                    </div>
                                                                </div>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Section Médias non organisés --}}
                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-orange-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <h3 class="font-medium text-gray-900">Médias non organisés</h3>
                                </div>
                                <p class="text-sm text-gray-600">{{ $media->count() }} média(s)</p>
                            </div>

                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                            @foreach ($media as $item)
                                <label class="relative cursor-pointer group">
                                    <input type="checkbox" name="media[]" value="{{ $item->id }}"
                                           class="peer hidden"
                                           {{ in_array($item->id, $attachedMedia) ? 'checked' : '' }}>

                                    @if(Str::startsWith($item->type, 'image/'))
                                        <img src="{{ asset('storage/' . $item->file_path) }}"
                                             alt="media"
                                             class="w-full h-32 object-cover rounded-md border border-gray-300 peer-checked:border-indigo-500 peer-checked:ring-2 peer-checked:ring-indigo-500 transition">
                                    @elseif($item->is_external)
                                        <iframe src="{{ $item->getEmbedUrl() }}"
                                                class="w-full h-32 rounded-md border border-gray-300 peer-checked:border-indigo-500 peer-checked:ring-2 peer-checked:ring-indigo-500 transition pointer-events-none"
                                                frameborder="0"
                                                allowfullscreen></iframe>
                                    @elseif(Str::startsWith($item->type, 'video/'))
                                        <video class="w-full h-32 object-cover rounded-md border border-gray-300 peer-checked:border-indigo-500 peer-checked:ring-2 peer-checked:ring-indigo-500 transition" muted>
                                            <source src="{{ asset('storage/' . $item->file_path) }}" type="{{ $item->type }}">
                                        </video>
                                    @endif

                                    @if(!$item->is_external)
                                    <div class="absolute top-1 right-1 bg-white text-xs px-2 py-1 rounded shadow opacity-0 group-hover:opacity-100 transition">
                                        {{ strtoupper(pathinfo($item->file_path, PATHINFO_EXTENSION)) }}
                                    </div>
                                    @else
                                    <div class="absolute top-1 right-1 bg-white text-xs px-2 py-1 rounded shadow opacity-0 group-hover:opacity-100 transition">
                                        {{ $item->getYoutubeId() ? 'YOUTUBE' : 'VIMEO' }}
                                    </div>
                                    @endif

                                    <div class="absolute inset-0 rounded-md bg-indigo-500/20 opacity-0 peer-checked:opacity-100 transition pointer-events-none"></div>
                                </label>
                            @endforeach
                        </div>
                        </div>
                    </div>

                    {{-- Sélection de la thumbnail --}}
                    @if(!$project->is_locked)
                        <div class="mb-8">
                            <label class="block font-medium text-sm text-gray-700 mb-4">
                                Thumbnail du projet
                                <span class="text-xs text-gray-500 font-normal ml-2">(Cliquez sur un dossier pour voir ses médias)</span>
                            </label>

                            @php
                                $currentThumbnailId = old('thumbnail_id', $project->thumbnail_id);
                                // Récup miniature courante (via relation si chargée, sinon find)
                                $currentThumb = $currentThumbnailId
                                    ? ($project->relationLoaded('thumbnail') ? $project->thumbnail : \App\Models\Media::find($currentThumbnailId))
                                    : null;

                                // Nom du dossier d’origine
                                $currentThumbFolderName = $currentThumb?->folder?->name ?? 'Médias non organisés';
                                $currentThumbFolderId   = $currentThumb?->folder?->id; // null => non organisé
                            @endphp

                            <div id="thumbnail-selector" class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                <p class="text-sm text-gray-600 mb-4">
                                    La thumbnail sera utilisée pour représenter ce projet sur les pages de catégorie et la page d'accueil.
                                </p>

                                {{-- Tuile de tête : si une miniature est sélectionnée, on l’affiche ici (à la place de "Aucune") --}}
                                <div class="mb-4 flex items-center gap-4">
                                    <label class="relative cursor-pointer group inline-block">
                                        @if($currentThumb)
                                            <input type="radio" name="thumbnail_id" value="{{ $currentThumb->id }}"
                                                class="peer hidden" checked>

                                            <div class="w-36 h-36 rounded-md border-2 border-indigo-500 ring-2 ring-indigo-400/50 overflow-hidden shadow-sm transition bg-white">
                                                @if(Str::startsWith($currentThumb->type, 'image/'))
                                                    <img src="{{ asset('storage/' . $currentThumb->file_path) }}"
                                                        alt="thumbnail sélectionnée"
                                                        class="w-full h-full object-cover">
                                                @elseif($currentThumb->is_external)
                                                    <iframe src="{{ $currentThumb->getEmbedUrl() }}"
                                                            class="w-full h-full pointer-events-none" frameborder="0"></iframe>
                                                @elseif(Str::startsWith($currentThumb->type, 'video/'))
                                                    <video class="w-full h-full object-cover" muted>
                                                        <source src="{{ asset('storage/' . $currentThumb->file_path) }}" type="{{ $currentThumb->type }}">
                                                    </video>
                                                @endif

                                                {{-- Badge "Sélection actuelle" --}}
                                                <div class="absolute top-2 left-2">
                                                    <span class="px-2 py-0.5 text-[10px] font-semibold rounded bg-indigo-600 text-white shadow">
                                                        Sélection actuelle
                                                    </span>
                                                </div>

                                                {{-- Badge dossier --}}
                                                <div class="absolute bottom-2 left-2">
                                                    <span class="px-2 py-0.5 text-[10px] rounded bg-white/90 text-gray-700 shadow">
                                                        Dossier : {{ $currentThumbFolderName }}
                                                    </span>
                                                </div>
                                            </div>

                                        @else
                                            {{-- Cas sans miniature : tuile "Aucune" identique à avant --}}
                                            <input type="radio" name="thumbnail_id" value=""
                                                class="peer hidden" checked>
                                            <div class="w-36 h-36 flex items-center justify-center rounded-md border-2 border-dashed border-gray-300
                                                        peer-checked:border-indigo-500 peer-checked:ring-2 peer-checked:ring-indigo-500 transition bg-white">
                                                <div class="text-center">
                                                    <svg class="w-8 h-8 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                    <p class="text-xs text-gray-500 mt-1">Aucune</p>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="absolute inset-0 rounded-md bg-indigo-500/10 opacity-0 peer-checked:opacity-100 transition pointer-events-none"></div>
                                    </label>

                                    {{-- Bouton "Réinitialiser: Aucune" --}}
                                    <label class="inline-flex items-center gap-2 text-sm text-gray-600 cursor-pointer select-none">
                                        <input type="radio" name="thumbnail_id" value="" class="sr-only"
                                            @if(!$currentThumb) checked @endif>
                                        <span class="px-3 py-1 rounded-md border border-gray-300 hover:border-gray-400 bg-white">
                                            Réinitialiser : Aucune
                                        </span>
                                    </label>
                                </div>

                                {{-- ======= Liste des dossiers pliables ======= --}}
                                <div class="space-y-2">
                                    {{-- Dossiers existants --}}
                                    @foreach($folders as $folder)
                                        @if($folder->media->count() > 0)
                                            @php
                                                $panelId = 'thumbnail-folder-'.$folder->id;
                                                $iconId  = 'icon-thumbnail-folder-'.$folder->id;
                                                $isCurrentFolder = $currentThumbFolderId && $currentThumbFolderId === $folder->id;
                                            @endphp

                                            <div class="border border-gray-200 rounded-lg bg-white overflow-hidden
                                                        @if($isCurrentFolder) ring-1 ring-indigo-300 @endif">
                                                <button type="button"
                                                        onclick="toggleThumbnailFolder('{{ $panelId }}')"
                                                        class="w-full px-4 py-3 flex items-center justify-between hover:bg-gray-50 transition-colors">
                                                    <div class="flex items-center">
                                                        <svg class="w-5 h-5 mr-3" fill="{{ $folder->color ?? '#3B82F6' }}" viewBox="0 0 24 24">
                                                            <path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/>
                                                        </svg>
                                                        <span class="font-medium text-gray-900">{{ $folder->name }}</span>
                                                        <span class="ml-2 text-xs text-gray-500">({{ $folder->media->count() }} médias)</span>
                                                        @if($isCurrentFolder)
                                                            <span class="ml-2 px-2 py-0.5 text-[10px] rounded bg-indigo-100 text-indigo-700 font-semibold">
                                                                Contient la miniature
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <svg class="w-5 h-5 text-gray-400 transform transition-transform"
                                                        id="{{ $iconId }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                </button>

                                                <div id="{{ $panelId }}" class="hidden px-4 pb-4">
                                                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 pt-3 border-t border-gray-100">
                                                        @foreach($folder->media as $item)
                                                            <label class="relative cursor-pointer group">
                                                                <input type="radio" name="thumbnail_id" value="{{ $item->id }}"
                                                                    class="peer hidden"
                                                                    {{ $currentThumbnailId == $item->id ? 'checked' : '' }}>

                                                                @if(Str::startsWith($item->type, 'image/'))
                                                                    <img src="{{ asset('storage/' . $item->file_path) }}"
                                                                        alt="thumbnail option"
                                                                        class="w-full h-32 object-cover rounded-md border-2 border-gray-300 peer-checked:border-indigo-500 peer-checked:ring-2 peer-checked:ring-indigo-500 transition">
                                                                @elseif($item->is_external)
                                                                    <div class="w-full h-32 rounded-md border-2 border-gray-300 peer-checked:border-indigo-500 peer-checked:ring-2 peer-checked:ring-indigo-500 transition overflow-hidden">
                                                                        <iframe src="{{ $item->getEmbedUrl() }}"
                                                                                class="w-full h-full pointer-events-none" frameborder="0"></iframe>
                                                                    </div>
                                                                @elseif(Str::startsWith($item->type, 'video/'))
                                                                    <video class="w-full h-32 object-cover rounded-md border-2 border-gray-300 peer-checked:border-indigo-500 peer-checked:ring-2 peer-checked:ring-indigo-500 transition" muted>
                                                                        <source src="{{ asset('storage/' . $item->file_path) }}" type="{{ $item->type }}">
                                                                    </video>
                                                                @endif

                                                                <div class="absolute inset-0 rounded-md bg-indigo-500/20 opacity-0 peer-checked:opacity-100 transition pointer-events-none"></div>

                                                                <div class="absolute top-2 right-2 opacity-0 peer-checked:opacity-100 transition-opacity">
                                                                    <div class="p-1 rounded-full bg-indigo-500">
                                                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                                        </svg>
                                                                    </div>
                                                                </div>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach

                                    {{-- Médias non organisés --}}
                                    @if($media->count() > 0)
                                        @php
                                            $unorganizedPanelId = 'thumbnail-folder-unorganized';
                                            $unorganizedIconId  = 'icon-thumbnail-folder-unorganized';
                                            $isUnorganizedCurrent = $currentThumb && !$currentThumbFolderId;
                                        @endphp

                                        <div class="border border-gray-200 rounded-lg bg-white overflow-hidden @if($isUnorganizedCurrent) ring-1 ring-indigo-300 @endif">
                                            <button type="button"
                                                    onclick="toggleThumbnailFolder('{{ $unorganizedPanelId }}')"
                                                    class="w-full px-4 py-3 flex items-center justify-between hover:bg-gray-50 transition-colors">
                                                <div class="flex items-center">
                                                    <svg class="w-5 h-5 mr-3 {{ $isUnorganizedCurrent ? 'text-indigo-600' : 'text-gray-600' }}"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2z"/>
                                                    </svg>
                                                    <span class="font-medium text-gray-900">Médias non organisés</span>
                                                    <span class="ml-2 text-xs text-gray-500">({{ $media->count() }} médias)</span>
                                                    @if($isUnorganizedCurrent)
                                                        <span class="ml-2 px-2 py-0.5 text-[10px] rounded bg-indigo-100 text-indigo-700 font-semibold">
                                                            Contient la miniature
                                                        </span>
                                                    @endif
                                                </div>
                                                <svg class="w-5 h-5 text-gray-400 transform transition-transform"
                                                    id="{{ $unorganizedIconId }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </button>

                                            <div id="{{ $unorganizedPanelId }}" class="hidden px-4 pb-4">
                                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 pt-3 border-t border-gray-100">
                                                    @foreach($media as $item)
                                                        <label class="relative cursor-pointer group">
                                                            <input type="radio" name="thumbnail_id" value="{{ $item->id }}"
                                                                class="peer hidden"
                                                                {{ $currentThumbnailId == $item->id ? 'checked' : '' }}>

                                                            @if(Str::startsWith($item->type, 'image/'))
                                                                <img src="{{ asset('storage/' . $item->file_path) }}"
                                                                    alt="thumbnail option"
                                                                    class="w-full h-32 object-cover rounded-md border-2 border-gray-300 peer-checked:border-indigo-500 peer-checked:ring-2 peer-checked:ring-indigo-500 transition">
                                                            @elseif($item->is_external)
                                                                <div class="w-full h-32 rounded-md border-2 border-gray-300 peer-checked:border-indigo-500 peer-checked:ring-2 peer-checked:ring-indigo-500 transition overflow-hidden">
                                                                    <iframe src="{{ $item->getEmbedUrl() }}"
                                                                            class="w-full h-full pointer-events-none" frameborder="0"></iframe>
                                                                </div>
                                                            @elseif(Str::startsWith($item->type, 'video/'))
                                                                <video class="w-full h-32 object-cover rounded-md border-2 border-gray-300 peer-checked:border-indigo-500 peer-checked:ring-2 peer-checked:ring-indigo-500 transition" muted>
                                                                    <source src="{{ asset('storage/' . $item->file_path) }}" type="{{ $item->type }}">
                                                                </video>
                                                            @endif

                                                            <div class="absolute inset-0 rounded-md bg-indigo-500/20 opacity-0 peer-checked:opacity-100 transition pointer-events-none"></div>

                                                            <div class="absolute top-2 right-2 opacity-0 peer-checked:opacity-100 transition-opacity">
                                                                <div class="p-1 rounded-full bg-indigo-500">
                                                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                                    </svg>
                                                                </div>
                                                            </div>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                @if($folders->count() == 0 && $media->count() == 0)
                                    <p class="text-sm text-amber-600 mt-3">
                                        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        Aucun média disponible. Ajoutez des médias dans la section Gestion des Médias.
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if(!$project->is_locked)
                    <script>
                        // Ouvrir le panneau du dossier qui contient la miniature actuelle et le mettre en avant
                        document.addEventListener('DOMContentLoaded', function () {
                            @if($currentThumb)
                                @if($currentThumbFolderId)
                                    toggleThumbnailFolder('thumbnail-folder-{{ $currentThumbFolderId }}');
                                    const elBtn{{ $currentThumbFolderId }} = document.querySelector('#icon-thumbnail-folder-{{ $currentThumbFolderId }}');
                                    if (elBtn{{ $currentThumbFolderId }}) elBtn{{ $currentThumbFolderId }}.style.transform = 'rotate(180deg)';
                                @else
                                    toggleThumbnailFolder('thumbnail-folder-unorganized');
                                    const unIcon = document.querySelector('#icon-thumbnail-folder-unorganized');
                                    if (unIcon) unIcon.style.transform = 'rotate(180deg)';
                                @endif

                                // scroll dans la vue en douceur
                                const selectedPanel = document.querySelector(
                                    @if($currentThumbFolderId)
                                        '#thumbnail-folder-{{ $currentThumbFolderId }}'
                                    @else
                                        '#thumbnail-folder-unorganized'
                                    @endif
                                );
                                if (selectedPanel) {
                                    selectedPanel.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                }
                            @endif
                        });

                        function toggleThumbnailFolder(folderId) {
                            const folder = document.getElementById(folderId);
                            const icon = document.getElementById('icon-' + folderId);
                            if (!folder) return;

                            if (folder.classList.contains('hidden')) {
                                folder.classList.remove('hidden');
                                if (icon) icon.style.transform = 'rotate(180deg)';
                            } else {
                                folder.classList.add('hidden');
                                if (icon) icon.style.transform = 'rotate(0deg)';
                            }
                        }
                    </script>
                    @endif


                    {{-- Bouton --}}
                    <div class="flex justify-end">
                        <button type="submit"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4 py-2 rounded-md">
                            Mettre à jour
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<script>
    // ============ Gestion des dossiers dans la sélection de médias ============

    function toggleFolderMedias(folderId) {
        const folder = document.getElementById(`folder-${folderId}`);
        const icon = document.getElementById(`icon-folder-${folderId}`);

        if (folder.classList.contains('hidden')) {
            folder.classList.remove('hidden');
            if (icon) icon.style.transform = 'rotate(180deg)';
        } else {
            folder.classList.add('hidden');
            if (icon) icon.style.transform = 'rotate(0deg)';
        }
    }

    // ============ Fin gestion des dossiers ============
</script>

{{-- TinyMCE from local files --}}
<script src="{{ asset('tinymce/tinymce.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    tinymce.init({
        selector: '#content',
        license_key: 'gpl',
        height: 500,
        menubar: false,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | ' +
            'bold italic backcolor | alignleft aligncenter ' +
            'alignright alignjustify | bullist numlist outdent indent | ' +
            'removeformat | help',
        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }',
        skin_url: '{{ asset("tinymce/skins/ui/oxide") }}',
        content_css: '{{ asset("tinymce/skins/content/default/content.css") }}'
    });
});
</script>
@endsection
