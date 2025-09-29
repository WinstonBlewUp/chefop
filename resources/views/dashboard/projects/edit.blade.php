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

                    {{-- Infos du projet --}}
                    <div class="mb-6">
                        <label class="block font-medium text-sm text-gray-700 mb-1">Titre</label>
                        <input type="text" name="title" value="{{ old('title', $project->title) }}"
                               class="w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>

                    <div class="mb-6">
                        <label class="block font-medium text-sm text-gray-700 mb-1">Slug (optionnel)</label>
                        <input type="text" name="slug" value="{{ old('slug', $project->slug) }}"
                               class="w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div class="mb-6">
                        <label class="block font-medium text-sm text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="4"
                                  class="w-full border-gray-300 rounded-md shadow-sm">{{ old('description', $project->description) }}</textarea>
                    </div>

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

                        <div class="flex items-center">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="is_selected_work" value="1" 
                                       id="is_selected_work" 
                                       {{ old('is_selected_work', $project->is_selected_work) ? 'checked' : '' }}
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
                        <label class="block font-medium text-sm text-gray-700 mb-2">project_type</label>
                        <input type="text" name="project_type" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-green-200 focus:border-green-400">
                    </div>

                    <div>
                        <label class="block font-medium text-sm text-gray-700 mb-2">Director</label>
                        <input type="text" name="director" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-green-200 focus:border-green-400">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block font-medium text-sm text-gray-700 mb-2">Productors</label>
                            <input type="text" name="productors" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-green-200 focus:border-green-400">
                        </div>
                        <div>
                            <label class="block font-medium text-sm text-gray-700 mb-2">production_company</label>
                            <input type="text" name="production_company" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-green-200 focus:border-green-400">
                        </div>
                    </div>                    

                    <div>
                        <label class="block font-medium text-sm text-gray-700 mb-2">Distributor</label>
                        <input type="text" name="distributor" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-green-200 focus:border-green-400">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block font-medium text-sm text-gray-700 mb-2">Award</label>
                            <input type="text" name="award" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-green-200 focus:border-green-400">
                        </div>
                        <div>
                            <label class="block font-medium text-sm text-gray-700 mb-2">Misc</label>
                            <input type="text" name="misc" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-green-200 focus:border-green-400">
                        </div>
                    </div>



                    {{-- Galerie --}}
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
@endsection
