@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">

                <h2 class="text-xl font-semibold mb-6">Gérer les pages</h2>

                {{-- Messages de succès --}}
                @if(session('success'))
                    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Formulaire de création de page --}}
                <form action="{{ route('dashboard.pages.store') }}" method="POST" class="mb-8">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block font-medium text-sm text-gray-700 mb-1">Titre</label>
                            <input type="text" name="title" class="w-full border-gray-300 rounded-md shadow-sm" required>
                            @error('title')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block font-medium text-sm text-gray-700 mb-1">Slug (optionnel)</label>
                            <input type="text" name="slug" class="w-full border-gray-300 rounded-md shadow-sm">
                            @error('slug')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6">
                        <label class="block font-medium text-sm text-gray-700 mb-1">Contenu</label>
                        <textarea name="content" rows="4" class="w-full border-gray-300 rounded-md shadow-sm"></textarea>
                        @error('content')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                        <div>
                            <label class="block font-medium text-sm text-gray-700 mb-1">Projet associé</label>
                            <select name="project" class="w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">-- Aucun --</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->title }}</option>
                                @endforeach
                            </select>
                            @error('project')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block font-medium text-sm text-gray-700 mb-1">Catégorie</label>
                            <select name="category_id" class="w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">-- Aucune --</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center">
                            <input type="hidden" name="published" value="0">
                            <input type="checkbox" name="published" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <label class="ml-2 text-sm text-gray-700">Publier immédiatement</label>
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4 py-2 rounded-md">
                            Créer la page
                        </button>
                    </div>
                </form>

                <hr class="my-10">

                {{-- Liste des pages existantes --}}
                <h3 class="text-lg font-semibold mb-4">Pages existantes</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Titre
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Slug
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Association
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Statut
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($pages as $page)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900">{{ $page->title }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-500">{{ $page->slug }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($page->project)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                📽️ {{ $page->project->title }}
                                            </span>
                                        @elseif($page->category)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                📂 {{ $page->category->name }}
                                            </span>
                                        @else
                                            <span class="text-sm text-gray-400">Autonome</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($page->published)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Publiée
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Brouillon
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            @if($page->published)
                                                <a href="{{ route('pages.show', $page->slug) }}" target="_blank" 
                                                   class="text-blue-600 hover:text-blue-900">
                                                    Voir
                                                </a>
                                            @endif
                                            <button onclick="editPage({{ $page->id }}, '{{ $page->title }}', '{{ $page->slug }}', '{{ addslashes($page->content) }}', {{ $page->project_id ?? 'null' }}, {{ $page->category_id ?? 'null' }}, {{ $page->published ? 'true' : 'false' }})"
                                                    class="text-indigo-600 hover:text-indigo-900">
                                                Modifier
                                            </button>
                                            <form action="{{ route('dashboard.pages.destroy', $page) }}" method="POST" 
                                                  onsubmit="return confirm('Supprimer cette page ?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-red-600 hover:text-red-900">Supprimer</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        Aucune page pour le moment.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Modale d'édition --}}
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-2xl mx-4 w-full">
        <h3 class="text-lg font-semibold mb-4">Modifier la page</h3>
        
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block font-medium text-sm text-gray-700 mb-1">Titre</label>
                    <input type="text" id="edit_title" name="title" class="w-full border-gray-300 rounded-md shadow-sm" required>
                </div>

                <div>
                    <label class="block font-medium text-sm text-gray-700 mb-1">Slug</label>
                    <input type="text" id="edit_slug" name="slug" class="w-full border-gray-300 rounded-md shadow-sm">
                </div>
            </div>

            <div class="mt-6">
                <label class="block font-medium text-sm text-gray-700 mb-1">Contenu</label>
                <textarea id="edit_content" name="content" rows="4" class="w-full border-gray-300 rounded-md shadow-sm"></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                <div>
                    <label class="block font-medium text-sm text-gray-700 mb-1">Projet associé</label>
                    <select id="edit_project" name="project" class="w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">-- Aucun --</option>
                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-medium text-sm text-gray-700 mb-1">Catégorie</label>
                    <select id="edit_category_id" name="category_id" class="w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">-- Aucune --</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center">
                    <input type="hidden" name="published" value="0">
                    <input type="checkbox" id="edit_published" name="published" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm">
                    <label class="ml-2 text-sm text-gray-700">Publier</label>
                </div>
            </div>

            <div class="flex justify-end space-x-4 mt-6">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">
                    Annuler
                </button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function editPage(id, title, slug, content, projectId, categoryId, published) {
        document.getElementById('editForm').action = `/dashboard/pages/${id}`;
        document.getElementById('edit_title').value = title;
        document.getElementById('edit_slug').value = slug;
        document.getElementById('edit_content').value = content;
        document.getElementById('edit_project').value = projectId || '';
        document.getElementById('edit_category_id').value = categoryId || '';
        document.getElementById('edit_published').checked = published;
        
        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    // Fermer la modale en cliquant à l'extérieur
    document.getElementById('editModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeEditModal();
        }
    });
</script>
@endsection