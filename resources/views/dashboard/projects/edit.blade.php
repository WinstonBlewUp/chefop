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
                        <label class="block font-medium text-sm text-gray-700 mb-1">Description (notes internes)</label>
                        <textarea name="description" rows="4"
                                  class="w-full border-gray-300 rounded-md shadow-sm">{{ old('description', $project->description) }}</textarea>
                    </div>
                    @endif  

                    {{-- Catégorie --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block font-medium text-sm text-gray-700 mb-1">Catégorie</label>
                            <select name="category_id" class="w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">-- Aucune --</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $project->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Selected Work --}}
                        <div class="flex items-center">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="is_selected_work" value="1" 
                                       id="is_selected_work" 
                                       {{ old('is_selected_work', $project->is_selected_work) ? 'checked' : '' }}
                                       class="w-4 h-4 text-orange-600 bg-gray-100 border-gray-300 rounded focus:ring-orange-500 focus:ring-2">
                            </div>
                            <div class="ml-3">
                                <label for="is_selected_work" class="font-medium text-sm text-gray-700">
                                    Ajouter à "Selected Work"
                                </label>
                                <p class="text-xs text-gray-500 mt-1">Ce projet apparaîtra sur la page d'accueil en plus de sa catégorie</p>
                            </div>
                        </div>
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

                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                                    @foreach($folders as $folder)
                                        <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                                            {{-- Dossier clickable pour toggle --}}
                                            <div class="p-3 border-b border-gray-100">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center flex-1 cursor-pointer" onclick="toggleFolderMedias({{ $folder->id }})">
                                                        <svg class="w-8 h-8 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/>
                                                        </svg>
                                                        <div>
                                                            <p class="text-sm font-medium text-gray-900 truncate">{{ $folder->name }}</p>
                                                            <p class="text-xs text-gray-500">{{ $folder->media_count }} fichier(s)</p>
                                                        </div>
                                                    </div>
                                                    {{-- Checkbox pour sélectionner tous les médias du dossier --}}
                                                    <label class="ml-2" title="Tout sélectionner" onclick="event.stopPropagation()">
                                                        <input type="checkbox"
                                                               class="folder-select-all w-4 h-4 text-green-600 rounded"
                                                               data-folder-id="{{ $folder->id }}"
                                                               onchange="toggleAllFolderMedia({{ $folder->id }})">
                                                    </label>
                                                </div>
                                            </div>

                                            {{-- Médias du dossier (cachés par défaut) --}}
                                            <div id="folder-medias-{{ $folder->id }}" class="hidden p-2 bg-gray-50">
                                                @if($folder->media->count() > 0)
                                                    <div class="space-y-2">
                                                        @foreach($folder->media as $item)
                                                            <label class="relative cursor-pointer group block">
                                                                <input type="checkbox"
                                                                       name="media[]"
                                                                       value="{{ $item->id }}"
                                                                       class="peer hidden folder-media-{{ $folder->id }}"
                                                                       {{ in_array($item->id, $attachedMedia) ? 'checked' : '' }}
                                                                       onchange="updateFolderSelectAll({{ $folder->id }})">

                                                                @if(Str::startsWith($item->type, 'image/'))
                                                                    <img src="{{ asset('storage/' . $item->file_path) }}"
                                                                         alt="media"
                                                                         class="w-full h-20 object-cover rounded border-2 border-gray-300 peer-checked:border-indigo-500 peer-checked:ring-2 peer-checked:ring-indigo-500 transition-all hover:shadow-md">
                                                                @elseif($item->is_external)
                                                                    <iframe src="{{ $item->getEmbedUrl() }}"
                                                                            class="w-full h-20 rounded border-2 border-gray-300 peer-checked:border-indigo-500 peer-checked:ring-2 peer-checked:ring-indigo-500 transition-all hover:shadow-md pointer-events-none"
                                                                            frameborder="0"
                                                                            allowfullscreen></iframe>
                                                                @elseif(Str::startsWith($item->type, 'video/'))
                                                                    <video class="w-full h-20 object-cover rounded border-2 border-gray-300 peer-checked:border-indigo-500 peer-checked:ring-2 peer-checked:ring-indigo-500 transition-all hover:shadow-md" muted>
                                                                        <source src="{{ asset('storage/' . $item->file_path) }}" type="{{ $item->type }}">
                                                                    </video>
                                                                @endif

                                                                <div class="absolute inset-0 rounded bg-indigo-500/20 opacity-0 peer-checked:opacity-100 transition-opacity pointer-events-none"></div>

                                                                {{-- Checkmark --}}
                                                                <div class="absolute top-1 left-1 opacity-0 peer-checked:opacity-100 transition-opacity">
                                                                    <div class="p-1 rounded-full bg-indigo-500">
                                                                        <svg class="w-2 h-2 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                                        </svg>
                                                                    </div>
                                                                </div>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <p class="text-xs text-gray-500 text-center py-2">Aucun média</p>
                                                @endif
                                            </div>
                                        </div>
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
                    <div class="mb-8">
                        <label class="block font-medium text-sm text-gray-700 mb-4">
                            Thumbnail du projet
                            <span class="text-xs text-gray-500 font-normal ml-2">(Cliquez sur un dossier pour voir ses médias)</span>
                        </label>

                        <div id="thumbnail-selector" class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                            <p class="text-sm text-gray-600 mb-4">La thumbnail sera utilisée pour représenter ce projet sur les pages de catégorie et la page d'accueil.</p>

                            @php
                                $currentThumbnailId = old('thumbnail_id', $project->thumbnail_id);
                            @endphp

                            {{-- Option: Pas de thumbnail --}}
                            <div class="mb-4">
                                <label class="relative cursor-pointer group inline-block">
                                    <input type="radio" name="thumbnail_id" value=""
                                           class="peer hidden"
                                           {{ !$currentThumbnailId ? 'checked' : '' }}>

                                    <div class="w-32 h-32 flex items-center justify-center rounded-md border-2 border-dashed border-gray-300 peer-checked:border-indigo-500 peer-checked:ring-2 peer-checked:ring-indigo-500 transition bg-white">
                                        <div class="text-center">
                                            <svg class="w-8 h-8 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            <p class="text-xs text-gray-500 mt-1">Aucune</p>
                                        </div>
                                    </div>
                                    <div class="absolute inset-0 rounded-md bg-indigo-500/20 opacity-0 peer-checked:opacity-100 transition pointer-events-none"></div>
                                </label>
                            </div>

                            {{-- Liste des dossiers pliables --}}
                            <div class="space-y-2">
                                {{-- Dossiers existants --}}
                                @foreach($folders as $folder)
                                    @if($folder->media->count() > 0)
                                        <div class="border border-gray-200 rounded-lg bg-white overflow-hidden">
                                            <button type="button"
                                                    onclick="toggleThumbnailFolder('folder-{{ $folder->id }}')"
                                                    class="w-full px-4 py-3 flex items-center justify-between hover:bg-gray-50 transition-colors">
                                                <div class="flex items-center">
                                                    <svg class="w-5 h-5 mr-3 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/>
                                                    </svg>
                                                    <span class="font-medium text-gray-900">{{ $folder->name }}</span>
                                                    <span class="ml-2 text-xs text-gray-500">({{ $folder->media->count() }} médias)</span>
                                                </div>
                                                <svg class="w-5 h-5 text-gray-400 transform transition-transform" id="icon-folder-{{ $folder->id }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </button>

                                            <div id="folder-{{ $folder->id }}" class="hidden px-4 pb-4">
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

                                {{-- Médias non organisés --}}
                                @if($media->count() > 0)
                                    <div class="border border-gray-200 rounded-lg bg-white overflow-hidden">
                                        <button type="button"
                                                onclick="toggleThumbnailFolder('folder-unorganized')"
                                                class="w-full px-4 py-3 flex items-center justify-between hover:bg-gray-50 transition-colors">
                                            <div class="flex items-center">
                                                <svg class="w-5 h-5 mr-3 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <span class="font-medium text-gray-900">Médias non organisés</span>
                                                <span class="ml-2 text-xs text-gray-500">({{ $media->count() }} médias)</span>
                                            </div>
                                            <svg class="w-5 h-5 text-gray-400 transform transition-transform" id="icon-folder-unorganized" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>

                                        <div id="folder-unorganized" class="hidden px-4 pb-4">
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

                    <script>
                        function toggleThumbnailFolder(folderId) {
                            const folder = document.getElementById(folderId);
                            const icon = document.getElementById('icon-' + folderId);

                            if (folder.classList.contains('hidden')) {
                                folder.classList.remove('hidden');
                                icon.style.transform = 'rotate(180deg)';
                            } else {
                                folder.classList.add('hidden');
                                icon.style.transform = 'rotate(0deg)';
                            }
                        }
                    </script>

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
        const mediaContainer = document.getElementById(`folder-medias-${folderId}`);
        if (mediaContainer.classList.contains('hidden')) {
            mediaContainer.classList.remove('hidden');
        } else {
            mediaContainer.classList.add('hidden');
        }
    }

    function toggleAllFolderMedia(folderId) {
        const selectAllCheckbox = document.querySelector(`.folder-select-all[data-folder-id="${folderId}"]`);
        const mediaCheckboxes = document.querySelectorAll(`.folder-media-${folderId}`);

        mediaCheckboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
    }

    function updateFolderSelectAll(folderId) {
        const selectAllCheckbox = document.querySelector(`.folder-select-all[data-folder-id="${folderId}"]`);
        const mediaCheckboxes = document.querySelectorAll(`.folder-media-${folderId}`);
        const checkedCount = document.querySelectorAll(`.folder-media-${folderId}:checked`).length;

        if (checkedCount === 0) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        } else if (checkedCount === mediaCheckboxes.length) {
            selectAllCheckbox.checked = true;
            selectAllCheckbox.indeterminate = false;
        } else {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = true;
        }
    }

    // Initialiser l'état indeterminate au chargement de la page
    document.addEventListener('DOMContentLoaded', function() {
        const folders = document.querySelectorAll('.folder-select-all');
        folders.forEach(checkbox => {
            const folderId = checkbox.getAttribute('data-folder-id');
            updateFolderSelectAll(folderId);
        });
    });

    // ============ Fin gestion des dossiers ============
</script>

{{-- TinyMCE Editor - Initialisé via resources/js/tinymce-init.js --}}
@endsection
