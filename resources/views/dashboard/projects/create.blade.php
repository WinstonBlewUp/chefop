@extends('layouts.app')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header avec titre et icône --}}
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
            <div class="p-6">
                <div class="flex items-center mb-6">
                    <div class="p-3 rounded-full bg-green-100 mr-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Créer un nouveau projet</h2>
                        <p class="text-gray-600">Ajoutez un projet et associez des médias</p>
                    </div>
                </div>

                <form action="{{ route('dashboard.projects.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block font-medium text-sm text-gray-700 mb-2">Titre</label>
                            <input type="text" name="title" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-green-200 focus:border-green-400" required>
                        </div>

                        <div>
                            <label class="block font-medium text-sm text-gray-700 mb-2">Slug (optionnel)</label>
                            <input type="text" name="slug" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-green-200 focus:border-green-400">
                        </div>
                    </div>

                    <div>
                        <label class="block font-medium text-sm text-gray-700 mb-2">Description</label>
                        <textarea name="description" rows="4" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-green-200 focus:border-green-400"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block font-medium text-sm text-gray-700 mb-2">Catégorie</label>
                            <select name="category_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-green-200 focus:border-green-400">
                                <option value="">-- Sélectionner une catégorie --</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="is_selected_work" value="1" 
                                       id="is_selected_work" 
                                       class="w-4 h-4 text-orange-600 bg-gray-100 border-gray-300 rounded focus:ring-orange-500 focus:ring-2">
                            </div>
                            <div class="ml-3">
                                <label for="is_selected_work" class="font-medium text-sm text-gray-700">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 text-orange-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        Ajouter à "Selected Work"
                                    </span>
                                </label>
                                <p class="text-xs text-gray-500 mt-1">Ce projet apparaîtra sur la page d'accueil en plus de sa catégorie</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block font-medium text-sm text-gray-700 mb-2">Contenu du projet</label>
                        <textarea id="content" name="content" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-green-200 focus:border-green-400"></textarea>
                    </div>

                    





                    


                    <div>
                        <label class="block font-medium text-sm text-gray-700 mb-4">Associer des médias</label>
                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                            @if($media->count() > 0)
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                                    @foreach ($media as $item)
                                        <label class="relative cursor-pointer group">
                                            <input type="checkbox" name="media[]" value="{{ $item->id }}" class="peer hidden">

                                            @if(Str::startsWith($item->type, 'image/'))
                                                <img src="{{ asset('storage/' . $item->file_path) }}"
                                                     alt="media"
                                                     class="w-full h-32 object-cover rounded-lg border-2 border-gray-300 peer-checked:border-green-500 peer-checked:ring-2 peer-checked:ring-green-200 transition-all hover:shadow-md">
                                            @elseif(Str::startsWith($item->type, 'video/'))
                                                <video class="w-full h-32 object-cover rounded-lg border-2 border-gray-300 peer-checked:border-green-500 peer-checked:ring-2 peer-checked:ring-green-200 transition-all hover:shadow-md" muted>
                                                    <source src="{{ asset('storage/' . $item->file_path) }}" type="{{ $item->type }}">
                                                </video>
                                            @endif

                                            <div class="absolute top-2 right-2 bg-black bg-opacity-75 text-white text-xs px-2 py-1 rounded-md opacity-0 group-hover:opacity-100 transition-opacity">
                                                {{ strtoupper(pathinfo($item->file_path, PATHINFO_EXTENSION)) }}
                                            </div>

                                            <div class="absolute inset-0 rounded-lg bg-green-500/20 opacity-0 peer-checked:opacity-100 transition-opacity pointer-events-none"></div>
                                            
                                            {{-- Checkmark sur sélection --}}
                                            <div class="absolute top-2 left-2 opacity-0 peer-checked:opacity-100 transition-opacity">
                                                <div class="p-1 rounded-full bg-green-500">
                                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center text-gray-500 py-8">
                                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <p class="font-medium">Aucun média disponible</p>
                                    <p class="text-sm">Ajoutez des médias dans la section Gestion des Médias</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 font-medium">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Créer le projet
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Liste des projets existants --}}
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-6">
                <div class="flex items-center mb-6">
                    <div class="p-2 rounded-full bg-green-100 mr-3">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Projets existants</h3>
                </div>

                {{-- Projet Stills mis en évidence --}}
                @if($stillsProject)
                    <div class="flex items-center justify-between bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-400 p-4 rounded-lg mb-4 shadow-sm">
                        <div class="flex items-center space-x-4">
                            <div class="p-2 rounded-full bg-blue-100">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900 flex items-center">
                                    {{ $stillsProject->title }}
                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Projet spécial
                                    </span>
                                </div>
                                <div class="text-sm text-gray-500">{{ $stillsProject->slug }} • Collection de médias stills</div>
                                <div class="text-xs text-blue-600 mt-1">{{ $stillsProject->media->count() }} média(s) associé(s)</div>
                            </div>
                        </div>

                        <div class="flex items-center space-x-2">
                            <a href="{{ route('dashboard.projects.edit', $stillsProject) }}"
                               class="inline-flex items-center px-3 py-1.5 text-sm bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Modifier
                            </a>
                            <span class="inline-flex items-center px-3 py-1.5 text-sm bg-gray-100 text-gray-500 rounded-lg cursor-not-allowed" title="Ce projet ne peut pas être supprimé">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                </svg>
                                Protégé
                            </span>
                        </div>
                    </div>
                @endif

                {{-- Projets réguliers --}}
                @forelse ($regularProjects ?? [] as $proj)
                    <div class="flex items-center justify-between bg-gray-50 p-4 rounded-lg mb-3 hover:bg-gray-100 transition-colors">
                        <div class="flex items-center space-x-4">
                            <div class="p-2 rounded-full bg-green-100">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">{{ $proj->title }}</div>
                                <div class="text-sm text-gray-500">{{ $proj->slug }}</div>
                                <div class="flex items-center space-x-2 mt-1">
                                    @if($proj->category)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            {{ $proj->category->name }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                            Sans catégorie
                                        </span>
                                    @endif
                                    @if($proj->is_selected_work)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                            Selected Work
                                        </span>
                                    @endif

                                    @php
                                        $publishedPage = $proj->pages()->where('published', true)->first();
                                    @endphp
                                    @if($publishedPage)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            Publiée
                                        </span>
                                    @elseif($proj->pages()->count() > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            Brouillon
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center space-x-2">
                            @if($publishedPage)
                                <a href="{{ route('pages.show', $publishedPage->slug) }}" target="_blank"
                                   class="inline-flex items-center px-3 py-1.5 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    Voir
                                </a>
                            @endif
                            <a href="{{ route('dashboard.projects.edit', $proj) }}"
                               class="inline-flex items-center px-3 py-1.5 text-sm bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Modifier
                            </a>
                            <form action="{{ route('dashboard.projects.destroy', $proj) }}" method="POST" onsubmit="return confirm('Supprimer ce projet ?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button class="inline-flex items-center px-3 py-1.5 text-sm bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-500 py-12">
                        <div class="p-4 rounded-full bg-gray-100 mx-auto w-16 h-16 flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <p class="font-medium">Aucun projet pour le moment.</p>
                        <p class="text-sm">Créez votre premier projet avec le formulaire ci-dessus.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Modale de vérification catégorie --}}
@if(session('show_category_modal'))
    <div id="categoryModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-2xl p-6 max-w-md mx-4">
            <div class="flex items-center mb-4">
                <div class="p-3 rounded-full bg-yellow-100 mr-4">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Aucune catégorie sélectionnée</h3>
            </div>
            <p class="text-gray-600 mb-6">
                Vous n'avez pas sélectionné de catégorie pour ce projet. 
                Voulez-vous vraiment continuer sans catégorie ?
            </p>
            
            <div class="flex justify-end space-x-3">
                <button onclick="closeCategoryModal()" 
                        class="inline-flex items-center px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Non, retourner
                </button>
                <button onclick="createWithoutCategory()" 
                        class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors font-medium">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Oui, continuer
                </button>
            </div>
        </div>
    </div>

    <script>
        function closeCategoryModal() {
            document.getElementById('categoryModal').style.display = 'none';
        }

        function createWithoutCategory() {
            const formData = @json(session('form_data'));
            
            fetch(`/dashboard/projects/store-without-category`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    form_data: formData
                })
            })
            .then(response => {
                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Une erreur est survenue');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue');
            });
        }

        // Auto-focus sur la modale
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('categoryModal');
            if (modal) {
                modal.focus();
            }
        });
    </script>
@endif

{{-- Modale de publication de page --}}
@if(session('show_publish_modal'))
    <div id="publishModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-2xl p-6 max-w-md mx-4">
            <div class="flex items-center mb-4">
                <div class="p-3 rounded-full bg-green-100 mr-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Page associée créée</h3>
            </div>
            <p class="text-gray-600 mb-6">
                La page associée à votre projet a été créée mais n'est pas encore publiée. 
                Voulez-vous la publier maintenant ?
            </p>
            
            <div class="flex justify-end space-x-3">
                <button onclick="closeModal()" 
                        class="inline-flex items-center px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Non
                </button>
                <button onclick="publishPage({{ session('show_publish_modal') }})" 
                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
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

{{-- TinyMCE Editor - Initialisé via resources/js/tinymce-init.js --}}
@endsection