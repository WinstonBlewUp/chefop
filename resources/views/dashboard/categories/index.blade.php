@extends('layouts.app')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header avec titre et icône --}}
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
            <div class="p-6">
                <div class="flex items-center mb-6">
                    <div class="p-3 rounded-full bg-purple-100 mr-4">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Gestion des Catégories</h2>
                        <p class="text-gray-600">Organisez vos projets par catégories</p>
                    </div>
                </div>

                {{-- Messages de succès --}}
                @if(session('success'))
                    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Messages d'erreur --}}
                @if(session('error'))
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Formulaire de création de catégorie --}}
                <form action="{{ route('dashboard.categories.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block font-medium text-sm text-gray-700 mb-2">Nom de la catégorie</label>
                            <input type="text" name="name" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-purple-200 focus:border-purple-400" required>
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block font-medium text-sm text-gray-700 mb-2">Slug (optionnel)</label>
                            <input type="text" name="slug" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-purple-200 focus:border-purple-400" placeholder="Généré automatiquement si vide">
                            @error('slug')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="inline-flex items-center px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200 font-medium">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Créer la catégorie
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Liste des catégories existantes --}}
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-6">
                <div class="flex items-center mb-6">
                    <div class="p-2 rounded-full bg-purple-100 mr-3">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Catégories existantes</h3>
                </div>

                @forelse ($categories as $category)
                    <div class="flex items-center justify-between bg-gray-50 p-4 rounded-lg mb-3 hover:bg-gray-100 transition-colors">
                        <div class="flex items-center space-x-4">
                            <div class="p-2 rounded-full bg-purple-100">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">{{ $category->name }}</div>
                                <div class="text-sm text-gray-500">{{ $category->slug }}</div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                    {{ $category->projects_count }} projet(s)
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center space-x-2">
                            <button onclick="editCategory({{ $category->id }}, '{{ $category->name }}', '{{ $category->slug }}')"
                                    class="inline-flex items-center px-3 py-1.5 text-sm bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition-colors">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Modifier
                            </button>
                            @if($category->projects_count == 0)
                                <form action="{{ route('dashboard.categories.destroy', $category) }}" method="POST"
                                      onsubmit="return confirm('Supprimer cette catégorie ?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="inline-flex items-center px-3 py-1.5 text-sm bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Supprimer
                                    </button>
                                </form>
                            @else
                                <span class="inline-flex items-center px-3 py-1.5 text-sm bg-gray-100 text-gray-500 rounded-lg cursor-not-allowed" title="Impossible de supprimer une catégorie contenant des projets">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Protégée
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-500 py-12">
                        <div class="p-4 rounded-full bg-gray-100 mx-auto w-16 h-16 flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                        </div>
                        <p class="font-medium">Aucune catégorie pour le moment.</p>
                        <p class="text-sm">Créez votre première catégorie avec le formulaire ci-dessus.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Modale d'édition --}}
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl shadow-2xl p-6 max-w-2xl mx-4 w-full">
        <div class="flex items-center mb-6">
            <div class="p-3 rounded-full bg-purple-100 mr-4">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900">Modifier la catégorie</h3>
        </div>

        <form id="editForm" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block font-medium text-sm text-gray-700 mb-2">Nom de la catégorie</label>
                        <input type="text" id="edit_name" name="name" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-purple-200 focus:border-purple-400" required>
                    </div>

                    <div>
                        <label class="block font-medium text-sm text-gray-700 mb-2">Slug</label>
                        <input type="text" id="edit_slug" name="slug" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-purple-200 focus:border-purple-400">
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeEditModal()" class="inline-flex items-center px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Annuler
                    </button>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors font-medium">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Mettre à jour
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function editCategory(id, name, slug) {
        document.getElementById('editForm').action = `/dashboard/categories/${id}`;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_slug').value = slug;

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
