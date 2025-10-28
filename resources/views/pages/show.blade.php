<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $page->title }}</title>
    @vite('resources/css/app.css')
</head>
<body>
@include('components.navbar')

<div class="bg-white w-full lg:pr-20 py-5">
    <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
        <div class="flex gap-4 lg:gap-8">
            {{-- Navigation latérale des projets de la catégorie - Cachée sur mobile --}}
            <div class="hidden lg:block w-40 flex-shrink-0 {{ $categoryProjects->count() > 1 ? 'opacity-100' : 'opacity-0 pointer-events-none' }}">
                <div class="sticky top-8">
                    <h3 class="font-semibold text-gray-900 text-sm uppercase tracking-wide mb-4">
                        {{ $page->project->category ? $page->project->category->name : 'PROJETS' }}
                    </h3>
                    <div class="space-y-2">
                        @foreach($categoryProjects as $project)
                            @php $isActive = $project->slug === $page->project->slug; @endphp
                            <a href="{{ route('pages.show', $project->slug) }}"
                               class="block py-2 px-2 text-sm transition-colors hover:bg-gray-100 {{ $isActive ? 'bg-gray-100 font-medium' : 'text-gray-700' }}">
                                {{ $project->title }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Colonne principale : on empile (titre/texte/grille/infos) --}}
            <div class="flex-1 min-w-0 pr-0 lg:pr-12 flex flex-col">

                {{-- Grille des médias (garde la largeur initiale) --}}
                <div class="w-full"><!-- forcer la grille à prendre 100% de la colonne -->
                    @include('components.grid', ['page' => $page])
                </div>

               {{-- Contenu riche du projet --}}
                <div class="mt-6 prose prose-sm max-w-none">
                    @if ($page->project && $page->project->content)
                        {!! $page->project->content !!}
                    @endif
                </div>
        </div>
    </div>
</div>
</body>
</html>



