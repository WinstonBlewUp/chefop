@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">

                <h2 class="text-xl font-semibold mb-6">Créer un nouveau projet</h2>

                <form action="{{ route('dashboard.projects.store') }}" method="POST">
                    @csrf

                    {{-- Infos du projet --}}
                    <div class="mb-6">
                        <label class="block font-medium text-sm text-gray-700 mb-1">Titre</label>
                        <input type="text" name="title" class="w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>

                    <div class="mb-6">
                        <label class="block font-medium text-sm text-gray-700 mb-1">Slug (optionnel)</label>
                        <input type="text" name="slug" class="w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div class="mb-6">
                        <label class="block font-medium text-sm text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="4" class="w-full border-gray-300 rounded-md shadow-sm"></textarea>
                    </div>

                    <div class="mb-6">
                        <label class="block font-medium text-sm text-gray-700 mb-1">Catégorie</label>
                        <select name="category_id" class="w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">-- Aucune --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>


                    {{-- Galerie --}}
                    <div class="mb-8">
                        <label class="block font-medium text-sm text-gray-700 mb-4">Associer des médias</label>

                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                            @foreach ($media as $item)
                                <label class="relative cursor-pointer group">
                                    <input type="checkbox" name="media[]" value="{{ $item->id }}" class="peer hidden">

                                    <img src="{{ asset('storage/' . $item->file_path) }}"
                                         alt="media"
                                         class="w-full h-32 object-cover rounded-md border border-gray-300 peer-checked:border-indigo-500 peer-checked:ring-2 peer-checked:ring-indigo-500 transition">

                                    <div class="absolute top-1 right-1 bg-white text-xs px-2 py-1 rounded shadow opacity-0 group-hover:opacity-100 transition">
                                        {{ strtoupper(pathinfo($item->file_path, PATHINFO_EXTENSION)) }}
                                    </div>

                                    <div class="absolute inset-0 rounded-md bg-indigo-500/20 opacity-0 peer-checked:opacity-100 transition pointer-events-none"></div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4 py-2 rounded-md">
                            Créer le projet
                        </button>
                    </div>
                </form>

                <hr class="my-10">

                <h3 class="text-lg font-semibold mb-4">Projets existants</h3>

                <ul class="space-y-2">
                    @forelse ($projects ?? [] as $proj)
                        <li class="flex justify-between items-center border p-3 rounded">
                            <div>
                                <div class="font-bold">{{ $proj->title }}</div>
                                <div class="text-sm text-gray-500">{{ $proj->slug }}</div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('dashboard.projects.edit', $proj) }}" class="text-indigo-600 hover:underline text-sm">Modifier</a>
                                <form action="{{ route('dashboard.projects.destroy', $proj) }}" method="POST" onsubmit="return confirm('Supprimer ce projet ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:underline text-sm">Supprimer</button>
                                </form>
                            </div>
                        </li>
                    @empty
                        <li class="text-gray-500 text-sm">Aucun projet pour le moment.</li>
                    @endforelse
                </ul>


            </div>
        </div>
    </div>
</div>

{{-- Modale de publication de page --}}
@if(session('show_publish_modal'))
    <div id="publishModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md mx-4">
            <h3 class="text-lg font-semibold mb-4">Page associée créée</h3>
            <p class="text-gray-600 mb-6">
                La page associée à votre projet a été créée mais n'est pas encore publiée. 
                Voulez-vous la publier maintenant ?
            </p>
            
            <div class="flex justify-end space-x-4">
                <button onclick="closeModal()" 
                        class="px-4 py-2 text-gray-600 hover:text-gray-800">
                    Non
                </button>
                <button onclick="publishPage({{ session('show_publish_modal') }})" 
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    Oui, publier
                </button>
            </div>
        </div>
    </div>

    <script>
        function closeModal() {
            document.getElementById('publishModal').style.display = 'none';
        }

        function publishPage(projectId) {
            fetch(`/dashboard/projects/${projectId}/publish-page`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Page publiée avec succès !');
                } else {
                    alert('Erreur : ' + data.message);
                }
                closeModal();
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue');
                closeModal();
            });
        }

        // Auto-focus sur la modale
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('publishModal');
            if (modal) {
                modal.focus();
            }
        });
    </script>
@endif
@endsection