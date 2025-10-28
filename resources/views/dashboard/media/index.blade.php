@extends('layouts.app')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header avec titre et icône --}}
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
            <div class="p-6">
                <div class="flex items-center mb-6">
                    <div class="p-3 rounded-full bg-orange-100 mr-4">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Gestion des Médias</h1>
                        <p class="text-gray-600">Uploadez et organisez vos fichiers</p>
                    </div>
                </div>

                {{-- Zone d'upload modernisée --}}
                <form id="uploadForm" action="{{ route('dashboard.media.upload') }}" method="POST" enctype="multipart/form-data"
                      class="border-2 border-dashed border-orange-300 rounded-xl p-8 bg-orange-50 text-center hover:bg-orange-100 transition-colors cursor-pointer">
                    @csrf
                    <input type="file" name="files" id="fileInput" class="hidden" accept="image/*,video/*" multiple>
                    <div class="p-4 rounded-full bg-orange-100 mx-auto w-16 h-16 flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                    </div>
                    <p class="text-gray-700 font-medium mb-2">Glissez-déposez vos fichiers ici ou cliquez pour en sélectionner</p>
                    <p class="text-sm text-gray-500 mb-2">(Sélection multiple supportée - Max 10 fichiers à la fois)</p>
                    <p class="text-xs text-gray-400">Images : JPEG, PNG, GIF, WEBP, BMP, TIFF, SVG</p>
                    <p class="text-xs text-gray-400">Vidéos : MP4, MOV, AVI, MKV, WMV, FLV, WEBM, M4V, 3GP... - Max 120 Mo par fichier</p>
                </form>

                {{-- Barre de progression pour les uploads multiples --}}
                <div id="uploadProgress" class="mt-4 hidden">
                    <div class="bg-gray-200 rounded-full h-2 mb-2">
                        <div id="progressBar" class="bg-orange-500 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600">
                        <span id="progressText">Upload en cours...</span>
                        <span id="progressPercent">0%</span>
                    </div>
                    <div id="filesList" class="mt-2 max-h-32 overflow-y-auto"></div>
                </div>

                <div id="uploadStatus" class="mt-4 text-sm text-gray-600 hidden flex items-center justify-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-orange-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Upload en cours...
                </div>
            </div>
        </div>

        {{-- Section des dossiers --}}
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <div class="p-2 rounded-full bg-blue-100 mr-3">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Dossiers</h2>
                            <p class="text-sm text-gray-600">Organisez vos médias par dossier</p>
                        </div>
                    </div>
                    <button onclick="openCreateFolderModal()" class="flex items-center gap-2 bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Nouveau dossier
                    </button>
                </div>

                @if($currentFolder)
                    {{-- Breadcrumb et retour --}}
                    <div class="mb-4 flex items-center gap-2 text-sm">
                        <a href="{{ route('dashboard.media.index') }}" class="text-orange-600 hover:text-orange-700">Racine</a>
                        @php
                            $path = [];
                            $folder = $currentFolder;
                            while ($folder) {
                                array_unshift($path, $folder);
                                $folder = $folder->parent;
                            }
                        @endphp
                        @foreach($path as $crumb)
                            <span class="text-gray-400">/</span>
                            <a href="{{ route('dashboard.media.index', ['folder' => $crumb->id]) }}" class="text-orange-600 hover:text-orange-700">
                                {{ $crumb->name }}
                            </a>
                        @endforeach
                    </div>

                    {{-- Bouton retour au parent --}}
                    <a href="{{ $currentFolder->parent_id ? route('dashboard.media.index', ['folder' => $currentFolder->parent_id]) : route('dashboard.media.index') }}"
                       class="inline-flex items-center gap-2 mb-4 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Dossier parent
                    </a>
                @endif

                @if($folders->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @foreach($folders as $folder)
                            <div class="relative group bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-all overflow-hidden"
                                 data-folder-id="{{ $folder->id }}"
                                 ondragover="handleFolderDragOver(event)"
                                 ondragleave="handleFolderDragLeave(event)"
                                 ondrop="handleDropOnFolder(event, {{ $folder->id }})">
                                <div onclick="toggleFolderContent({{ $folder->id }})" class="block p-4 text-center cursor-pointer">
                                    <svg class="w-12 h-12 mx-auto mb-2 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/>
                                    </svg>
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $folder->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $folder->media_count }} fichier(s)</p>
                                </div>

                                {{-- Actions --}}
                                <div class="absolute top-2 right-2 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button onclick="event.stopPropagation(); openEditFolderModal({{ $folder->id }}, '{{ $folder->name }}')" class="p-1 bg-white rounded shadow hover:bg-gray-100">
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                        </svg>
                                    </button>
                                    <button onclick="event.stopPropagation(); deleteFolder({{ $folder->id }}, '{{ $folder->name }}')" class="p-1 bg-white rounded shadow hover:bg-red-100">
                                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>

                                {{-- Contenu du dossier (caché par défaut) --}}
                                <div id="folder-content-{{ $folder->id }}" class="hidden border-t border-gray-200 bg-gray-50 p-3">
                                    @if($folder->media->count() > 0)
                                        <div class="grid grid-cols-2 gap-2">
                                            @foreach($folder->media as $item)
                                                <div class="relative cursor-pointer group/media bg-white rounded border border-gray-200 shadow-sm hover:shadow-md transition-all overflow-hidden"
                                                     onclick="openMediaModal('{{ asset('storage/' . $item->file_path) }}', '{{ $item->type }}', '{{ pathinfo($item->file_path, PATHINFO_FILENAME) }}')">
                                                    @if(Str::startsWith($item->type, 'image/'))
                                                        <img src="{{ asset('storage/' . $item->file_path) }}"
                                                             alt="media"
                                                             class="w-full h-20 object-cover group-hover/media:scale-105 transition-transform duration-300">
                                                    @elseif(Str::startsWith($item->type, 'video/'))
                                                        <div class="relative w-full h-20 bg-gray-900">
                                                            <video class="w-full h-full object-cover" muted preload="metadata">
                                                                <source src="{{ asset('storage/' . $item->file_path) }}#t=1" type="{{ $item->type }}">
                                                            </video>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-xs text-gray-500 text-center">Aucun média</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-gray-500 py-8">
                        <p class="text-sm">Aucun dossier. Créez-en un pour organiser vos médias.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Galerie des médias existants --}}
        @if($media->count() > 0)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center mb-6">
                        <div class="p-2 rounded-full bg-orange-100 mr-3">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Médias non organisés</h2>
                            <p class="text-sm text-gray-600">{{ $media->count() }} média(s) sans dossier</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @foreach($media as $item)
                            <div class="relative cursor-move group bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-all overflow-hidden"
                                 draggable="true"
                                 data-media-id="{{ $item->id }}"
                                 ondragstart="handleMediaDragStart(event, {{ $item->id }})"
                                 ondragend="handleMediaDragEnd(event)"
                                 onclick="openMediaModal('{{ asset('storage/' . $item->file_path) }}', '{{ $item->type }}', '{{ pathinfo($item->file_path, PATHINFO_FILENAME) }}')">
                                
                                @if(Str::startsWith($item->type, 'image/'))
                                    <img src="{{ asset('storage/' . $item->file_path) }}"
                                         alt="media"
                                         class="w-full h-32 object-cover group-hover:scale-105 transition-transform duration-300">
                                @elseif(Str::startsWith($item->type, 'video/'))
                                    <div class="relative w-full h-32 bg-gray-900">
                                        <video class="w-full h-full object-cover"
                                               muted preload="metadata">
                                            <source src="{{ asset('storage/' . $item->file_path) }}#t=1" type="{{ $item->type }}">
                                            {{-- Fallback pour les navigateurs qui ne supportent pas le format --}}
                                            <div class="w-full h-full flex items-center justify-center bg-gray-800 text-white">
                                                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M8 5v14l11-7z"/>
                                                </svg>
                                            </div>
                                        </video>
                                        {{-- Overlay de lecture --}}
                                        <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-30 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <div class="bg-white bg-opacity-90 rounded-full p-2">
                                                <svg class="w-6 h-6 text-gray-800" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M8 5v14l11-7z"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- Badge du format de fichier --}}
                                <div class="absolute top-2 right-2 bg-black bg-opacity-75 text-white text-xs px-2 py-1 rounded-md opacity-0 group-hover:opacity-100 transition-opacity">
                                    {{ strtoupper(pathinfo($item->file_path, PATHINFO_EXTENSION)) }}
                                </div>

                                {{-- Indicateur de type de média --}}
                                <div class="absolute bottom-2 left-2">
                                    @if(Str::startsWith($item->type, 'image/'))
                                        <div class="p-1 rounded-full bg-green-100 bg-opacity-90">
                                            <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @elseif(Str::startsWith($item->type, 'video/'))
                                        <div class="p-1 rounded-full bg-blue-100 bg-opacity-90">
                                            <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                {{-- Durée de la vidéo (visible en bas à droite) --}}
                                @if(Str::startsWith($item->type, 'video/'))
                                    <div class="absolute bottom-2 right-2 bg-black bg-opacity-75 text-white text-xs px-1 py-0.5 rounded">
                                        <svg class="w-3 h-3 inline" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="p-6">
                    <div class="text-center text-gray-500 py-12">
                        <div class="p-4 rounded-full bg-gray-100 mx-auto w-16 h-16 flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <p class="font-medium">Aucun média pour le moment.</p>
                        <p class="text-sm">Utilisez la zone d'upload ci-dessus pour ajouter vos premiers fichiers.</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Modal de prévisualisation --}}
<div id="mediaModal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50 flex items-center justify-center p-4">
    <div class="relative max-w-4xl max-h-full">
        <button onclick="closeMediaModal()" class="absolute -top-12 right-0 text-white hover:text-gray-300 transition-colors">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <div id="modalContent" class="bg-white rounded-lg overflow-hidden shadow-2xl max-h-full">
            {{-- Le contenu sera injecté dynamiquement --}}
        </div>
    </div>
</div>

<script>
    const dropzone = document.getElementById('uploadForm');
    const fileInput = document.getElementById('fileInput');
    const status = document.getElementById('uploadStatus');
    const progressContainer = document.getElementById('uploadProgress');
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    const progressPercent = document.getElementById('progressPercent');
    const filesList = document.getElementById('filesList');

    // Types MIME supportés étendus
    const allowedTypes = [
        // Images
        'image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp', 
        'image/bmp', 'image/tiff', 'image/svg+xml',
        // Vidéos
        'video/mp4', 'video/mov', 'video/avi', 'video/x-msvideo', 'video/quicktime',
        'video/x-ms-wmv', 'video/x-flv', 'video/webm', 'video/x-m4v', 
        'video/3gpp', 'video/ogg', 'video/x-ms-asf'
    ];
    const maxSize = 120 * 1024 * 1024; // 120 Mo (pour correspondre à post_max_size)

    dropzone.addEventListener('click', (e) => {
        e.preventDefault();
        fileInput.click();
    });

    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.classList.add('border-orange-500', 'bg-orange-100');
        dropzone.classList.remove('border-orange-300', 'bg-orange-50');
    });

    dropzone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        dropzone.classList.remove('border-orange-500', 'bg-orange-100');
        dropzone.classList.add('border-orange-300', 'bg-orange-50');
    });

    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.classList.remove('border-orange-500', 'bg-orange-100');
        dropzone.classList.add('border-orange-300', 'bg-orange-50');
        
        const files = Array.from(e.dataTransfer.files);
        if (files.length > 0) {
            uploadFiles(files);
        }
    });

    fileInput.addEventListener('change', () => {
        const files = Array.from(fileInput.files);
        if (files.length > 0) {
            uploadFiles(files);
        }
    });

    function uploadFiles(files) {
        // Validation côté client
        const validFiles = [];
        const errors = [];

        if (files.length > 10) {
            showError('Vous ne pouvez pas uploader plus de 10 fichiers à la fois.');
            return;
        }

        files.forEach(file => {
            if (file.size > maxSize) {
                console.log('ERROR: Client-side validation failed for file size');
                errors.push(`${file.name}: Fichier trop volumineux (${(file.size / 1024 / 1024).toFixed(1)} Mo). Max 120 Mo.`);
            } else if (!allowedTypes.includes(file.type)) {
                console.log('ERROR: Client-side validation failed for file type');
                errors.push(`${file.name}: Format non supporté (${file.type}).`);
            } else {
                console.log('SUCCESS: Client-side validation passed');
                validFiles.push(file);
            }
        });

        if (errors.length > 0) {
            showError(errors.join('<br>'));
            if (validFiles.length === 0) return;
        }

        if (validFiles.length === 0) return;

        // Préparer l'interface pour l'upload
        status.classList.add('hidden');
        progressContainer.classList.remove('hidden');
        progressBar.style.width = '0%';
        progressPercent.textContent = '0%';
        progressText.textContent = `Upload de ${validFiles.length} fichier(s)...`;
        
        // Afficher la liste des fichiers
        filesList.innerHTML = validFiles.map(file => 
            `<div class="text-xs text-gray-600 truncate">${file.name} (${(file.size / 1024 / 1024).toFixed(1)} Mo)</div>`
        ).join('');

        // Créer FormData avec les fichiers valides
        const formData = new FormData();
        if (validFiles.length === 1) {
            // Fichier unique
            formData.append('file', validFiles[0]);
        } else {
            // Fichiers multiples
            validFiles.forEach(file => {
                formData.append('files[]', file);
            });
        }

        // Upload avec suivi de progression
        const xhr = new XMLHttpRequest();

        xhr.upload.addEventListener('progress', (e) => {
            if (e.lengthComputable) {
                const percent = Math.round((e.loaded / e.total) * 100);
                progressBar.style.width = percent + '%';
                progressPercent.textContent = percent + '%';
                progressText.textContent = `Upload en cours... ${percent}%`;
            }
        });

        xhr.addEventListener('load', () => {
            progressContainer.classList.add('hidden');
            
            if (xhr.status === 200 || xhr.status === 201) {
                try {
                    const data = JSON.parse(xhr.responseText);
                    if (data.success) {
                        showSuccess(data.message);
                        if (data.errors && data.errors.length > 0) {
                            setTimeout(() => {
                                showError('Erreurs partielles:<br>' + data.errors.join('<br>'));
                            }, 2000);
                        }
                        // Ajouter les nouveaux médias à la grille sans recharger la page
                        if (data.media) {
                            addMediaToGrid(Array.isArray(data.media) ? data.media : [data.media]);
                        }
                    } else {
                        showError(data.message || 'Erreur lors de l\'upload');
                        if (data.errors) {
                            setTimeout(() => {
                                showError('Détails:<br>' + data.errors.join('<br>'));
                            }, 2000);
                        }
                    }
                } catch (e) {
                    // En cas d'erreur de parsing, vérifier si c'est une erreur de limite PHP
                    if (xhr.responseText.includes('POST Content-Length') && xhr.responseText.includes('exceeds the limit')) {
                        console.log('ERROR: PHP Content-Length limit exceeded detected in response');
                        showError('Fichier trop volumineux. Veuillez utiliser un fichier de moins de 120 Mo.');
                    } else {
                        console.log('ERROR: Other JSON parsing error');
                        showError('Erreur de communication avec le serveur. Veuillez réessayer.');
                    }
                }
            } else {
                let errorMessage = `Erreur serveur (${xhr.status})`;
                if (xhr.status === 422) {
                    errorMessage = 'Erreur de validation - Vérifiez le format et la taille des fichiers';
                } else if (xhr.status === 413) {
                    errorMessage = 'Fichiers trop volumineux pour le serveur';
                } else if (xhr.status === 500) {
                    errorMessage = 'Erreur interne du serveur';
                }
                showError(errorMessage);
            }
        });

        xhr.addEventListener('error', () => {
            progressContainer.classList.add('hidden');
            showError('Erreur de connexion lors de l\'upload');
        });

        xhr.open('POST', dropzone.action);
        xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
        xhr.send(formData);
    }

    function showError(message) {
        status.classList.remove('hidden', 'text-gray-600', 'text-green-600');
        status.classList.add('text-red-600');
        status.innerHTML = `
            <svg class="w-5 h-5 text-red-600 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>${message}</span>
        `;
        setTimeout(() => {
            status.classList.add('hidden');
        }, 8000);
    }

    function showSuccess(message) {
        status.classList.remove('hidden', 'text-gray-600', 'text-red-600');
        status.classList.add('text-green-600');
        status.innerHTML = `
            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            ${message}
        `;
    }

    // Fonctions pour la modal
    function openMediaModal(url, type, name) {
        const modal = document.getElementById('mediaModal');
        const content = document.getElementById('modalContent');
        
        if (type.startsWith('image/')) {
            content.innerHTML = `
                <div class="p-4">
                    <img src="${url}" alt="${name}" class="max-w-full max-h-[80vh] object-contain mx-auto">
                    <div class="mt-4 text-center text-gray-600">
                        <p class="font-medium">${name}</p>
                        <p class="text-sm">${type}</p>
                    </div>
                </div>
            `;
        } else if (type.startsWith('video/')) {
            content.innerHTML = `
                <div class="p-4">
                    <video controls class="max-w-full max-h-[80vh] mx-auto">
                        <source src="${url}" type="${type}">
                        Votre navigateur ne supporte pas la lecture de cette vidéo.
                    </video>
                    <div class="mt-4 text-center text-gray-600">
                        <p class="font-medium">${name}</p>
                        <p class="text-sm">${type}</p>
                    </div>
                </div>
            `;
        }
        
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeMediaModal() {
        const modal = document.getElementById('mediaModal');
        modal.classList.add('hidden');
        document.body.style.overflow = '';
        
        // Arrêter les vidéos en cours de lecture
        const videos = modal.querySelectorAll('video');
        videos.forEach(video => {
            video.pause();
            video.currentTime = 0;
        });
    }

    // Fermer la modal en cliquant à l'extérieur
    document.getElementById('mediaModal').addEventListener('click', (e) => {
        if (e.target === e.currentTarget) {
            closeMediaModal();
        }
    });

    // Fermer la modal avec Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeMediaModal();
        }
    });

    // ============ Gestion du drag & drop ============

    let draggedMediaId = null;

    function handleMediaDragStart(event, mediaId) {
        draggedMediaId = mediaId;
        event.currentTarget.style.opacity = '0.5';
        event.dataTransfer.effectAllowed = 'move';
    }

    function handleMediaDragEnd(event) {
        event.currentTarget.style.opacity = '1';
    }

    function handleFolderDragOver(event) {
        event.preventDefault();
        event.dataTransfer.dropEffect = 'move';
        // Ajouter un indicateur visuel
        event.currentTarget.classList.add('border-orange-500', 'border-2', 'bg-orange-50');
    }

    function handleFolderDragLeave(event) {
        event.currentTarget.classList.remove('border-orange-500', 'border-2', 'bg-orange-50');
    }

    function handleDropOnFolder(event, folderId) {
        event.preventDefault();
        event.stopPropagation();
        event.currentTarget.classList.remove('border-orange-500', 'border-2', 'bg-orange-50');

        if (!draggedMediaId) return;

        // Déplacer le média vers le dossier
        fetch(`/dashboard/media/${draggedMediaId}/move`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                folder_id: folderId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess(data.message);
                // Retirer le média de la liste
                const mediaElement = document.querySelector(`[data-media-id="${draggedMediaId}"]`);
                if (mediaElement) {
                    mediaElement.style.transition = 'opacity 0.3s, transform 0.3s';
                    mediaElement.style.opacity = '0';
                    mediaElement.style.transform = 'scale(0.8)';
                    setTimeout(() => {
                        mediaElement.remove();
                        // Mettre à jour le compteur
                        updateMediaCount(-1);
                        // Recharger pour voir le média dans le dossier
                        setTimeout(() => location.reload(), 500);
                    }, 300);
                }
            } else {
                showError(data.message || 'Erreur lors du déplacement');
            }
        })
        .catch(error => {
            showError('Erreur lors du déplacement du média');
            console.error(error);
        })
        .finally(() => {
            draggedMediaId = null;
        });
    }

    function updateMediaCount(delta) {
        const counterElement = document.querySelector('.text-sm.text-gray-600');
        if (counterElement && counterElement.textContent.includes('média(s) sans dossier')) {
            const currentCount = parseInt(counterElement.textContent.match(/\d+/)[0]);
            const newCount = currentCount + delta;
            counterElement.textContent = `${newCount} média(s) sans dossier`;
        }
    }

    function toggleFolderContent(folderId) {
        const content = document.getElementById(`folder-content-${folderId}`);
        if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
        } else {
            content.classList.add('hidden');
        }
    }

    // ============ Fin gestion du drag & drop ============

    // ============ Gestion des dossiers ============

    function openCreateFolderModal() {
        const folderName = prompt('Nom du nouveau dossier:');
        if (!folderName || !folderName.trim()) return;

        fetch('{{ route("dashboard.folders.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                name: folderName.trim(),
                parent_id: {{ $currentFolder ? $currentFolder->id : 'null' }}
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess(data.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                showError(data.message || 'Erreur lors de la création du dossier');
            }
        })
        .catch(error => {
            showError('Erreur lors de la création du dossier');
            console.error(error);
        });
    }

    function openEditFolderModal(folderId, currentName) {
        const newName = prompt('Nouveau nom du dossier:', currentName);
        if (!newName || !newName.trim() || newName === currentName) return;

        fetch(`/dashboard/folders/${folderId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                name: newName.trim()
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess(data.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                showError(data.message || 'Erreur lors de la modification');
            }
        })
        .catch(error => {
            showError('Erreur lors de la modification du dossier');
            console.error(error);
        });
    }

    function deleteFolder(folderId, folderName) {
        if (!confirm(`Supprimer le dossier "${folderName}" ?\n\nLes médias et sous-dossiers seront déplacés vers le dossier parent.`)) {
            return;
        }

        fetch(`/dashboard/folders/${folderId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess(data.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                showError(data.message || 'Erreur lors de la suppression');
            }
        })
        .catch(error => {
            showError('Erreur lors de la suppression du dossier');
            console.error(error);
        });
    }

    // ============ Fin gestion des dossiers ============

    // Fonction pour ajouter dynamiquement les nouveaux médias à la grille
    function addMediaToGrid(mediaList) {
        const mediaGrid = document.querySelector('.grid');
        const emptyState = document.querySelector('.text-center.text-gray-500');
        
        // Cacher le message "Aucun média" s'il existe
        if (emptyState) {
            emptyState.closest('.bg-white').style.display = 'none';
        }
        
        // S'assurer que la grille existe
        if (!mediaGrid) {
            location.reload();
            return;
        }

        mediaList.forEach(media => {
            const mediaItem = document.createElement('div');
            mediaItem.className = 'relative cursor-pointer group bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-all overflow-hidden';
            mediaItem.onclick = () => openMediaModal(media.url, media.type, media.file_path.split('/').pop().split('.')[0]);
            
            if (media.type.startsWith('image/')) {
                mediaItem.innerHTML = `
                    <img src="${media.url}" alt="media" class="w-full h-32 object-cover group-hover:scale-105 transition-transform duration-300">
                    <div class="absolute top-2 right-2 bg-black bg-opacity-75 text-white text-xs px-2 py-1 rounded-md opacity-0 group-hover:opacity-100 transition-opacity">
                        ${media.file_path.split('.').pop().toUpperCase()}
                    </div>
                    <div class="absolute bottom-2 left-2">
                        <div class="p-1 rounded-full bg-green-100 bg-opacity-90">
                            <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                `;
            } else if (media.type.startsWith('video/')) {
                mediaItem.innerHTML = `
                    <div class="relative w-full h-32 bg-gray-900">
                        <video class="w-full h-full object-cover" muted preload="metadata">
                            <source src="${media.url}#t=1" type="${media.type}">
                            <div class="w-full h-full flex items-center justify-center bg-gray-800 text-white">
                                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </div>
                        </video>
                        <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-30 opacity-0 group-hover:opacity-100 transition-opacity">
                            <div class="bg-white bg-opacity-90 rounded-full p-2">
                                <svg class="w-6 h-6 text-gray-800" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="absolute top-2 right-2 bg-black bg-opacity-75 text-white text-xs px-2 py-1 rounded-md opacity-0 group-hover:opacity-100 transition-opacity">
                        ${media.file_path.split('.').pop().toUpperCase()}
                    </div>
                    <div class="absolute bottom-2 left-2">
                        <div class="p-1 rounded-full bg-blue-100 bg-opacity-90">
                            <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="absolute bottom-2 right-2 bg-black bg-opacity-75 text-white text-xs px-1 py-0.5 rounded">
                        <svg class="w-3 h-3 inline" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z"/>
                        </svg>
                    </div>
                `;
            }
            
            // Ajouter l'élément au début de la grille
            mediaGrid.insertBefore(mediaItem, mediaGrid.firstChild);
            
            // Animation d'apparition
            mediaItem.style.opacity = '0';
            mediaItem.style.transform = 'scale(0.8)';
            setTimeout(() => {
                mediaItem.style.transition = 'opacity 0.3s, transform 0.3s';
                mediaItem.style.opacity = '1';
                mediaItem.style.transform = 'scale(1)';
            }, 10);
        });

        // Mettre à jour le compteur de médias
        const counterElement = document.querySelector('.text-sm.text-gray-600');
        if (counterElement && counterElement.textContent.includes('média(s) au total')) {
            const currentCount = parseInt(counterElement.textContent.match(/\d+/)[0]);
            const newCount = currentCount + mediaList.length;
            counterElement.textContent = `${newCount} média(s) au total`;
        }
    }
</script>
@endsection
