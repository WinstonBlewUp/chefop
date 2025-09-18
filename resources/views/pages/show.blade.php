<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>{{ $page->title }}</title>
    @vite('resources/css/app.css')
</head>
<body>
    @include('components.navbar')
    
    <div class="bg-white w-100 pr-20 py-5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex gap-8">
                {{-- Navigation latérale des projets de la catégorie --}}
                <div class="w-40 flex-shrink-0 {{ $categoryProjects->count() > 1 ? 'opacity-100' : 'opacity-0 pointer-events-none' }}">
                    <div class="sticky top-8">
                        <h3 class="font-semibold text-gray-900 text-sm uppercase tracking-wide mb-4">
                            {{ $page->project->category ? $page->project->category->name : 'PROJETS' }}
                        </h3>
                        <div class="space-y-2">
                            @foreach($categoryProjects as $project)
                                @php
                                    $isActive = $project->slug === $page->project->slug;
                                @endphp
                                <a href="{{ route('pages.show', $project->slug) }}" 
                                   class="block py-2 px-2 text-sm transition-colors hover:bg-gray-100 {{ $isActive ? 'bg-gray-100 font-medium' : 'text-gray-700' }}">
                                    {{ $project->title }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Contenu principal étendu pour correspondre à navbar --}}
                <div class="flex-1 pr-12">
                <h1 class="text-3xl font-bold text-center mb-8">{{ $page->title }}</h1>

                @if($page->content)
                    <div class="text-center text-gray-600 mb-8">
                        {!! nl2br(e($page->content)) !!}
                    </div>
                @endif

                {{-- Grille des médias --}}
                @include('components.grid', ['page' => $page])
                </div>
            </div>
        </div>
    </div>
</body>
</html>
