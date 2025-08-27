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
                    <input type="file" name="file" id="fileInput" class="hidden" accept="image/*,video/*">
                    <div class="p-4 rounded-full bg-orange-100 mx-auto w-16 h-16 flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                    </div>
                    <p class="text-gray-700 font-medium mb-2">Glissez-déposez un fichier ici ou cliquez pour en sélectionner un</p>
                    <p class="text-sm text-gray-500">(JPG, PNG, GIF, MP4, MOV...)</p>
                </form>

                <div id="uploadStatus" class="mt-4 text-sm text-gray-600 hidden flex items-center justify-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-orange-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Upload en cours...
                </div>
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
                            <h2 class="text-lg font-bold text-gray-900">Médias existants</h2>
                            <p class="text-sm text-gray-600">{{ $media->count() }} média(s) au total</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @foreach($media as $item)
                            <div class="relative cursor-pointer group">
                                @if(Str::startsWith($item->type, 'image/'))
                                    <img src="{{ asset('storage/' . $item->file_path) }}"
                                         alt="media"
                                         class="w-full h-32 object-cover rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-all group-hover:scale-105">
                                @elseif(Str::startsWith($item->type, 'video/'))
                                    <video class="w-full h-32 object-cover rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-all"
                                           muted>
                                        <source src="{{ asset('storage/' . $item->file_path) }}" type="{{ $item->type }}">
                                    </video>
                                @endif

                                <div class="absolute top-2 right-2 bg-black bg-opacity-75 text-white text-xs px-2 py-1 rounded-md opacity-0 group-hover:opacity-100 transition-opacity">
                                    {{ strtoupper(pathinfo($item->file_path, PATHINFO_EXTENSION)) }}
                                </div>

                                {{-- Indicateur de type de média --}}
                                <div class="absolute bottom-2 left-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    @if(Str::startsWith($item->type, 'image/'))
                                        <div class="p-1 rounded-full bg-green-100">
                                            <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @elseif(Str::startsWith($item->type, 'video/'))
                                        <div class="p-1 rounded-full bg-blue-100">
                                            <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
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

<script>
    const dropzone = document.getElementById('uploadForm');
    const fileInput = document.getElementById('fileInput');
    const status = document.getElementById('uploadStatus');

    dropzone.addEventListener('click', () => fileInput.click());

    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.classList.add('bg-gray-50');
    });

    dropzone.addEventListener('dragleave', () => {
        dropzone.classList.remove('bg-gray-50');
    });

    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.classList.remove('bg-gray-50');
        fileInput.files = e.dataTransfer.files;
        uploadFile();
    });

    fileInput.addEventListener('change', () => {
        uploadFile();
    });

    function uploadFile() {
        const file = fileInput.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('file', file);

        status.classList.remove('hidden');

        fetch(dropzone.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        }).then(res => {
            if (res.ok) {
                status.innerHTML = `
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Upload terminé avec succès !
                `;
                status.classList.remove('text-gray-600');
                status.classList.add('text-green-600');
            } else {
                status.innerHTML = `
                    <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Erreur lors de l'upload
                `;
                status.classList.remove('text-gray-600');
                status.classList.add('text-red-600');
            }
            setTimeout(() => location.reload(), 1500);
        });
    }
</script>
@endsection
