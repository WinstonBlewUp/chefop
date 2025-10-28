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

                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                            @foreach ($media as $item)
                                <label class="relative cursor-pointer group">
                                    <input type="checkbox" name="media[]" value="{{ $item->id }}"
                                           class="peer hidden"
                                           {{ in_array($item->id, $attachedMedia) ? 'checked' : '' }}>

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

{{-- TinyMCE --}}
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
tinymce.init({
    selector: '#content',
    height: 400,
    menubar: false,
    plugins: 'link lists code image',
    toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image | code'
});
</script>
@endsection
