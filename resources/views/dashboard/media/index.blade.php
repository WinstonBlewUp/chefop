@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-12">
    <h1 class="text-xl font-semibold mb-4">Ajouter des médias</h1>

    <form id="uploadForm" action="{{ route('dashboard.media.upload') }}" method="POST" enctype="multipart/form-data"
          class="border-2 border-dashed border-gray-300 rounded-lg p-8 bg-white text-center">
        @csrf
        <input type="file" name="file" id="fileInput" class="hidden" accept="image/*,video/*">
        <p class="text-gray-500">Glissez-déposez un fichier ici ou cliquez pour en sélectionner un</p>
        <p class="text-sm text-gray-400 mt-1">(jpg, png, gif, mp4, mov...)</p>
    </form>

    <div id="uploadStatus" class="mt-4 text-sm text-gray-600 hidden">Upload en cours...</div>

    {{-- Galerie des médias existants --}}
    @if($media->count() > 0)
        <div class="mt-12">
            <h2 class="text-xl font-semibold mb-6">Médias existants</h2>
            
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($media as $item)
                    <div class="relative cursor-pointer group">
                        <img src="{{ asset('storage/' . $item->file_path) }}"
                             alt="media"
                             class="w-full h-32 object-cover rounded-md border border-gray-300 transition">

                        <div class="absolute top-1 right-1 bg-white text-xs px-2 py-1 rounded shadow opacity-0 group-hover:opacity-100 transition">
                            {{ strtoupper(pathinfo($item->file_path, PATHINFO_EXTENSION)) }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
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
            status.textContent = res.ok ? 'Upload terminé ✅' : 'Erreur lors de l’upload ❌';
            setTimeout(() => location.reload(), 1000);
        });
    }
</script>
@endsection
