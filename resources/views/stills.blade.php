<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Stills</title>
    @vite('resources/css/app.css')
</head>
<body>
    @include('components.navbar')
    
    <div class="bg-white w-100 pr-20 py-5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex gap-8">
                {{-- Navigation latérale des projets de la catégorie --}}
                <div class="w-40 flex-shrink-0 {{ $stillsProjects->count() > 1 ? 'opacity-100' : 'opacity-0 pointer-events-none' }}">
                    <div class="sticky top-8">
                        <h3 class="font-semibold text-gray-900 text-sm uppercase tracking-wide mb-4">
                            Stills
                        </h3>
                        <div class="space-y-2">
                            @foreach($stillsProjects as $project)
                                <a href="{{ route('pages.show', $project->slug) }}" 
                                   class="block py-2 px-2 text-sm transition-colors hover:bg-gray-100 text-gray-700">
                                    {{ $project->title }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Contenu principal étendu pour correspondre à navbar --}}
                <div class="flex-1 pr-12">
                    <h1 class="text-3xl font-bold text-center mb-8">Stills</h1>

                    {{-- Grille des projets Stills --}}
                    @if($stillsProjects->isNotEmpty())
                        <div class="flex flex-wrap justify-center gap-4">
                            @foreach ($stillsProjects as $project)
                                @php
                                    $media = $project->media->first();
                                @endphp
                                @if ($media)
                                    <a href="{{ route('pages.show', $project->slug) }}" class="block relative h-60 max-w-[40%] min-w-[15%]">
                                        @if (Str::startsWith($media->type, 'image/'))
                                            <img
                                                src="{{ asset('storage/' . $media->file_path) }}"
                                                alt="media"
                                                class="h-full w-auto object-cover shadow mx-auto"
                                            />
                                        @elseif (Str::startsWith($media->type, 'video/'))
                                            <video
                                                src="{{ asset('storage/' . $media->file_path) }}"
                                                class="h-full w-auto object-cover shadow mx-auto"
                                                muted autoplay loop
                                            ></video>
                                        @endif
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-gray-400 py-12">Aucun projet stills à afficher.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>